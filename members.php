<?php
    class Members extends CI_Model {
        /* 오류코드, 형식 : array("코드", "필드명") 혹은 string 코드명 */
        private $err_code = "";

        /* 최종 리턴 데이터 */
        private $arr_return = array();

        public function __construct() {
            parent::__construct();
            $this->load->database();

            $this->arr_return["result"] = true;
        }


        /* 회원가입 */
        public function regist() {
            //테스트용
            //$_POST = array("name" => "AU", "nickname" => "bu", "password" => "111111a!S1111", "tel" => "01000000000", "email" => "ewre@wr.com", "sex"=> "F");

            //데이터 유효성 체크
            $arr_chk_datas = $this->set_validation("regist", $_POST);
            $arr_chk_datas["created"] = date("Y-m-d H:i:s"); //회원가입 일자

            $this->db->insert("members", $arr_chk_datas);

            //회원 가입 성공
            if($this->db->affected_rows() > 0 === true) {
                $this->arr_return["msg"] = "회원가입성공";

                //추천인코드 INSERT
                $int_insert_id = $this->db->insert_id(); //인서트회원PK
                $str_recommend_code = $this->create_recommend_code($int_insert_id); //추천인 코드 생성

                $this->db->insert("recommend_codes", array("member_id" => $int_insert_id, "code" => $str_recommend_code));

                if($this->db->affected_rows() > 0 === false) {
                    //추천인코드 생성실패
                    $this->err_code = "regist_02";
                }
            } else {
                //가입실패
                $this->err_code = "regist_01";
            }

            $this->set_output();
        }


        /* 회원수정 */
        public function modify() {
            //데이터 유효성 체크
            $arr_chk_datas = $this->set_validation("modify", $_POST);

            //where 조건 설정
            $this->db->where("id", $arr_chk_datas["member_id"]);

            //member_id, PK는 수정에서 제외
            unset($arr_chk_datas["member_id"]);

            //수정할 데이터가 없는경우
            if(empty($arr_chk_datas)) {
                $this->err_code = "modify_01";

            //수정진행
            } else {
                $arr_chk_datas["updated"] = date("Y-m-d H:i:s"); //회원수정 일자

                $this->db->update("members", $arr_chk_datas);

                if($this->db->affected_rows() > 0 === true) {
                    $this->arr_return["msg"] = "회원수정성공";
                } else {
                    //수정실패
                    $this->err_code = "modify_02";
                }
            }

            $this->set_output();
        }


        /* 
            회원탈퇴(삭제)
            - $delete = "Y" : row delete,
            - $delete = "N" : 탈퇴 플래그 업데이트
        */
        public function withdraw($delete = "N") {
            //데이터 유효성 체크
            $arr_chk_datas = $this->set_validation("withdraw", $_POST);

            //where 조건 설정
            $this->db->where("id", $arr_chk_datas["member_id"]);

            //member_id, PK는 수정에서 제외
            unset($arr_chk_datas["member_id"]);

            //row delete
            if($delete == "Y") {
                $this->db->delete("members");

            //탈퇴 플래그 업데이트
            } else {
                $arr_chk_datas["flag"] = "F"; //회원탈퇴 플래그
                $arr_chk_datas["flag_out_time"] = date("Y-m-d H:i:s"); //회원탈퇴 일자

                $this->db->update("members", $arr_chk_datas);
            }

            if($this->db->affected_rows() > 0 === true) {
                $this->arr_return["msg"] = "회원" . ($delete == "Y" ? "삭제" : "탈퇴") . "성공";
            } else {
                $this->err_code = "withdraw_" . ($delete == "Y" ? "01" : "02");
            }

            $this->set_output();
        }


        /* 회원1명 조회 */
        public function detail() {
            //데이터 유효성 체크
            $arr_chk_datas = $this->set_validation("detail", $_POST);

            $str_qry = "
                SELECT 
                    a.id AS member_id, a.name, a.nickname, a.tel, a.email, a.sex, a.created,
                    b.code,
                    COUNT(c.id) AS log_cnt,
                    GROUP_CONCAT( SUBSTR(c.created, 1,7) ORDER BY c.created DESC ) AS log_created
                FROM
                    (members a, recommend_codes b)
                    LEFT JOIN
                    recommend_logs c ON (a.id = c.member_id)
                WHERE
                    a.id = b.member_id AND
                    a.id = " . $arr_chk_datas["member_id"] . "
            ";

            $arr_result = $this->db->query($str_qry);
            $arr_row = $arr_result->row_array();

            if($arr_result->num_rows() > 0 && !empty($arr_row["member_id"])) {
                $arr_row["log_this_month"] = 0;

                /*
                    member_id : 3번 회원 데이터 예시

                    Array
                    (
                        [id] => 3
                        [name] => AC
                        [nickname] => bc
                        [tel] => 01000000000
                        [email] => werwer3@ewr.com
                        [sex] => F
                        [created] => 2019-03-18 00:00:00
                        [code] => c53yEcD17v
                        [log_cnt] => 4 //피추천 횟수
                        [log_created] => 2019-09,2019-09,2019-08,2019-07 //이번달 피추천 횟수를 구하기위해
                        [log_this_month] => 0
                    )
                */

                //이번달 피추천 횟수 구하기
                if(!empty($arr_row["log_created"])) {
                    if(($is_array = (bool) preg_match_all("/" . date("Y-m") . "/", $arr_row["log_created"], $this_month)) === true) {
                        $arr_row["log_this_month"] = count($this_month[0]);
                    }
                }

            //데이터가 존재하지 않는경우
            } else {
                $arr_row = array();
                $this->err_code = "search_01";
            }

            $this->set_output($arr_row);
        }

        /*
            회원리스트 조회
            - $int_page : 현재 페이지
            - $int_limit : 페이지당 표시할 데이터수
        */
        public function list($int_page = 1, $int_limit = 5) {
            $this->load->library("pagination");

            //리턴 데이터
            $arr_return = array();

            //쿼리 limit offset 설정
            $int_offset = ($int_page - 1) * $int_limit;

            //table 토탈 row 구하기
            $str_qry_tot = "
                SELECT
                    COUNT(*) AS tot_cnt
                FROM
                    members
            ";

            $arr_result = $this->db->query($str_qry_tot);
            $arr_row_tot = $arr_result->row_array();

            if(isset($arr_row_tot["tot_cnt"]) && $arr_row_tot["tot_cnt"] > 0) {
                //전체 회원수 이상은 처리안되도록
                if($int_offset >= $arr_row_tot["tot_cnt"]) {
                    $this->err_code = "search_01";
                    $this->set_output();
                }

                $arr_return["datas"] = array();

                $str_qry = "
                    SELECT 
                        SQL_CALC_FOUND_ROWS
                        a.id AS member_id, a.name, a.nickname, a.tel, a.email, a.sex, a.created,
                        b.code,
                        COUNT(c.id) AS log_cnt,
                        GROUP_CONCAT( SUBSTR(c.created, 1,7) ORDER BY c.created DESC ) AS log_created
                    FROM
                        (members a, recommend_codes b)
                        LEFT JOIN
                        recommend_logs c ON (a.id = c.member_id)
                    WHERE
                        a.id = b.member_id
                    GROUP BY
                        a.id
                    LIMIT
                        " . $int_offset . ", " . $int_limit . "
                ";

                $arr_result = $this->db->query($str_qry);

                foreach($arr_result->result_array() as $row) {
                    $row["log_this_month"] = 0; //이번달 피추천 횟수

                    //이번달 피추천 횟수 구하기
                    if(!empty($row["log_created"])) {
                        if(($is_array = (bool) preg_match_all("/" . date("Y-m") . "/", $row["log_created"], $this_month)) === true) {
                            $row["tmp_log_this_month"] = count($this_month[0]);
                        }
                    }

                    $arr_return["datas"][] = $row;
                }

                $config["base_url"] = base_url() . "index.php/member/index";
                $config["total_rows"] = $arr_row_tot["tot_cnt"];
                $config["per_page"] = $int_limit;

                $this->pagination->initialize($config);

                $arr_return["paging"] = $this->pagination->create_links();

            //데이터가 존재하지 않는경우
            } else {
                $this->err_code = "search_01";
            }

            $this->set_output($arr_return);
        }


        /*
            리턴설정
            - $datas : 리턴할 데이터
            - $str_type : 리턴 형식 ("json" or 그외)
            - $str_type = "json" : 출력후 종료, 그외 $datas 형식대로 리턴
        */
        public function set_output($datas = null, $str_type = "json") {
            //오류처리
            if(!empty($this->err_code)) {
                $this->set_error();
            }

            //리턴값이 없을경우 default 셋팅
            if(empty($datas)) {
                $datas = $this->arr_return;
            }

            switch($str_type) {
                case "json" :
                    echo json_encode($datas);
                    break;

                default :
                    return $datas;
            }

            die();
        }


        /* 
            데이터 유효성 체크 패턴 설정
            - ci_ 로 시작되는 패턴은 ci 내부 함수 이용
            - 그외 패턴은 개별 함수 생성하여 처리
            - 함수명뒤 [] 안의 데이터는 해당함수 인자
            - 체크 데이터는 $_POST값으로 진행

        */
        public function set_validation($flag = null, $arr_chk_datas = array()) {
            $this->load->library("form_validation");

            if(empty($flag) || empty($arr_chk_datas)) {
                $this->err_code = "default";
                $this->set_output();
            }

            $arr_result_datas = array(); //체크 완료된 리턴 데이터

            switch($flag) {
                case "regist" :
                    $arr_chk_rules = array(
                         //이름 : 필수, 최대길이(20), 한글/알파벳 대소문자 체크
                        "name" => array("required", "ci_max_length[20]", "han_alpha"),

                         //별명 : 필수, 최대길이(30), 알파벳 소문자 체크
                        "nickname" => array("required", "ci_max_length[30]", "alpha_lower"),

                         //비밀번호 : 필수, 최소길이(10), 비밀번호형식(영문 대/소문자, 특수문자, 숫자 각 1개이상) 체크
                        "password" => array("required", "ci_min_length[10]", "passwd"),

                         //전화번호 : 필수, 최대길이(20), 숫자 체크
                        "tel" => array("required", "ci_max_length[20]", "ci_numeric"),

                         //이메일 : 필수, 최대길이(100), 이메일형식, 회원별중복, 유니크값 체크
                        "email" => array("required", "ci_max_length[100]", "ci_valid_email", "ci_is_unique[members.email]"),

                         //성별 : null 혹은 기본값(M or F) 체크
                        "sex" => array("simple_value[M|F]"),

                        //추천인코드 : 추천인코드형식 (10자리, 유니크값) 체크
                        "recommend_code" => array("chk_recommend_code")
                    );
                    break;

                case "modify" :
                    $arr_chk_rules = array(
                        //회원PK : 필수, 숫자 체크
                        "member_id" => array("required", "ci_numeric"),

                         //이름 : 최대길이(20), 한글/알파벳 대소문자 체크
                        "name" => array("ci_max_length[20]", "han_alpha"),

                         //별명 : 최대길이(30), 알파벳 소문자 체크
                        "nickname" => array("ci_max_length[30]", "alpha_lower"),

                         //비밀번호 : 최소길이(10), 비밀번호형식(영문 대/소문자, 특수문자, 숫자 각 1개이상) 체크
                        "password" => array("ci_min_length[10]", "passwd"),

                         //전화번호 : 최대길이(20), 숫자 체크
                        "tel" => array("ci_max_length[20]", "ci_numeric"),

                         //이메일 : 필수, 최대길이(100), 이메일형식, 회원별중복, 유니크값 체크
                        "email" => array("ci_max_length[100]", "ci_valid_email", "ci_is_unique[members.email]"),

                         //성별 : null 혹은 기본값(M or F) 체크
                        "sex" => array("simple_value[M|F]")
                    );
                    break;

                case "withdraw" :
                case "detail" :
                    $arr_chk_rules = array(
                         //회원PK : 필수, 숫자 체크
                        "member_id" => array("required", "ci_numeric")
                    );
                    break;
            }

            //체크 루틴 시작
            foreach($arr_chk_rules as $str_field => $rules) {
                foreach($rules as $function) {
                    $tmp_f_nm = array(); //인자값이 있는 함수, 함수명
                    $str_f_nm = preg_replace("/^ci_/", "", $function); //ci 기본함수 or 개별 함수, 함수명
                    $str_f_arg = ""; //함수 인자값
                    $obj_chk_mdl = (substr($function, 0, 3) == "ci_" ? $this->form_validation : $this); //함수 호출 모델

                    //인자값이 있는 함수 체크
                    if(($is_array = (bool) preg_match_all("/\[(.*?)\]/", $str_f_nm, $arg)) === true) {
                        sscanf($str_f_nm, '%[^[][', $tmp_f_nm[0]); //함수명 추출

                        $str_f_nm = $tmp_f_nm[0]; //함수명
                        $str_f_arg = $arg[1][0]; //함수 인자값
                    }

                    //필수값 함수 체크
                    if($str_f_nm == "required") {
                        if(!isset($arr_chk_datas[$str_field]) || !$this->form_validation->{$str_f_nm}($arr_chk_datas[$str_field])) {
                            $this->err_code = array($str_f_nm, $str_field);
                            $this->set_output();
                        }

                    //패턴별 체크함수 실행
                    }else if(isset($arr_chk_datas[$str_field])) {

                        if(!$obj_chk_mdl->{$str_f_nm}($arr_chk_datas[$str_field], $str_f_arg)) {
                            $this->err_code = array($str_f_nm, $str_field, $arr_chk_datas[$str_field]);
                            $this->set_output();
                        }
                    }
                }

                //체크성공 데이터 생성
                if(isset($arr_chk_datas[$str_field])) {
                    //비밀번호는 md5 암호화
                    if($str_field == "password") {
                        $arr_chk_datas[$str_field] = md5($arr_chk_datas[$str_field]);
                    }

                    $arr_result_datas[$str_field] = $arr_chk_datas[$str_field];
                }
            }

            return $arr_result_datas;
        }


        /*
            오류처리
            - 용도에 따라 별도 로그처리 진행 (error file log 등)
            - 현재는 단순 리턴용도
        */
        public function set_error() {
            $str_field = "";
            $this->arr_return["result"] = false;

            if(is_array($this->err_code)) {
                $this->arr_return["data"] = (isset($this->err_code[2]) ? $this->err_code[2] : ""); //오류 데이터값
                $str_field = $this->err_code[1]; //오류 필드
                $this->err_code = $this->err_code[0]; //오류 코드
            }

            switch($this->err_code) {
                case "default" :
                    $this->arr_return["msg"] = "정상적이지 않은 접근입니다";
                    break;

                case "required" :
                    $this->arr_return["msg"] = "필수 " . $str_field . " 누락";
                    break;

                case "unique" :
                    $this->arr_return["msg"] = "유니크 " . $str_field . " 중복";
                    break;

                case "regist_01" :
                    $this->arr_return["msg"] = "회원 가입 실패";
                    break;

                case "regist_02" :
                    $this->arr_return["msg"] = "추천인코드 생성 실패";
                    break;

                case "modify_01" :
                    $this->arr_return["msg"] = "회원 수정할 데이터가 없습니다";
                    break;

                case "modify_02" :
                    $this->arr_return["msg"] = "회원 수정 실패";
                    break;

                case "withdraw_01" :
                    $this->arr_return["msg"] = "회원 삭제 실패";
                    break;

                case "withdraw_02" :
                    $this->arr_return["msg"] = "회원 탈퇴 실패";
                    break;

                case "search_01" :
                    $this->arr_return["msg"] = "회원 데이터가 존재하지 않습니다";
                    break;

                default :
                    $this->arr_return["msg"] = "잘못된 " . $str_field . " 형식";
            }
        }


        /*
            추천인코드 발급
            - $int_member_id : 가입성공한 회원 PK
            - A-Z, 0-9, a-z 랜덤코드, 회원 PK 값을 조합하여 10자리 생성
            - 회원 PK (member_id) 9999999 번 까지만 처리가능
        */
        public function create_recommend_code($int_member_id = 1) {
            $tmp_member_id = intval($int_member_id);
            $tmp_member_id = str_pad($tmp_member_id, 7, 0, STR_PAD_LEFT);
            $tmp_member_id = intval( strval("1" . $tmp_member_id) );
            $str_tmp_code = base_convert($tmp_member_id, 10, 36);

            $str_rnd_key = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
            $int_key_len = strlen($str_rnd_key) - 1;

            $tmp_recommend_code = "";

            for($i = 0; strlen($tmp_recommend_code) < 10; $i++) {
                $tmp_recommend_code .= $str_rnd_key[rand(0, $int_key_len)] . $str_tmp_code[$i];
            }

            return $tmp_recommend_code;
        }




        /*
            추천인코드 유효성 체크
            - 문자열형식 10자리
            - db 데이터 체크
        */
        private function chk_recommend_code($str_code = "") {
            //값이 있고 문자열 형식
            if(empty($str_code) || !is_string($str_code)) {
                return false;
            }

            //10자리 체크
            if(!$this->form_validation->exact_length($str_code, 10)) {
                return false;
            }

            $str_qry = "
                SELECT
                    COUNT(*) AS cnt,
                    IFNULL(a.member_id, 0) AS member_id
                FROM
                    recommend_codes a,
                    recommend_logs b
                WHERE
                    a.member_id = b.member_id AND
                    a.code=" . $this->db->escape($str_code) . "
                GROUP BY
                    a.member_id
            ";

            $arr_result = $this->db->query($str_qry);

            //존재하는 추천인코드는 1개여야 한다
            if($arr_result->num_rows() == 1) {
                $arr_row = $arr_result->row_array();

                //고객당 5번까지
                if($arr_row["cnt"] < 5 && $arr_row["member_id"] > 0) {
                    return true;
                }
            }

            return false;
        }


        /* 한글, 영문 대/소문자 체크 */
        private function han_alpha($str_data = null) {
            return (preg_match("/^[가-힣a-zA-Z]+$/", $str_data));
        }


        /* 영문 소문자 체크 */
        private function alpha_lower($str_data = null) {
            return (ctype_lower($str_data));
        }


        /* 비밀번호형식(영문 대/소문자, 특수문자, 숫자 각 1개이상) 체크 */
        private function passwd($str_data = null) {
            return (preg_match("/^.*(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&+=])(?=.*\d).*$/", $str_data));
        }


        /* null 혹은 기본값(M or F) 체크 */
        private function simple_value($str_data = null, $str_pattern = null) {
            if(is_string($str_data) && is_string($str_pattern)) {
                return (empty($str_data) || preg_match("/^(" . $str_pattern . ")$/", $str_data));
            }

            return false;
        }
    }
?>