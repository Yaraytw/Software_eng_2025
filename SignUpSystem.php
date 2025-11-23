<?php
include_once 'DbConnect.php';

class SignUpSystem {
    private $conn;
    private $table_name = "memberProfile";

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // S1: MainSign (註冊主模組)
    public function MainSign($data) {
        // 依照流程圖順序執行檢查
        
        // S3: CheckIdRepeat
        if ($this->CheckIdRepeat($data['memberId'])) {
            return "身分證字號重複";
        }

        // S8: CheckEmail (格式檢查通常由前端JS先做，後端再驗證)
        if (!$this->CheckEmail($data['memberEmail'])) {
            return "信箱格式錯誤";
        }

        // S9: CheckEmailRepeat
        if ($this->CheckEmailRepeat($data['memberEmail'])) {
            return "信箱已被註冊";
        }

        // S13: CheckPwd
        if (!$this->CheckPwd($data['memberPwd'], $data['memberPwdConfirm'])) {
            return "兩次密碼不一致";
        }

        // S16: CheckCellphone
        if (!$this->CheckCellphone($data['memberPhone'])) {
            return "手機格式錯誤";
        }

        // S30: CheckConfirm (驗證碼邏輯，假設已驗證通過或此步驟在API層處理)
        
        // 執行寫入資料庫動作 (整合 S4, S6, S10, S12, S17, S19, S21, S23, S26)
        if ($this->InsertMember($data)) {
            return $this->ShowSign($data); // S31
        } else {
            return "註冊失敗";
        }
    }

    // S3: CheckIdRepeat
    public function CheckIdRepeat($id) {
        $query = "SELECT memberId FROM " . $this->table_name . " WHERE memberId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0; // True if exists (Repeat)
    }

    // S8: CheckEmail (格式)
    public function CheckEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // S9: CheckEmailRepeat (資料庫)
    public function CheckEmailRepeat($email) {
        $query = "SELECT memberEmail FROM " . $this->table_name . " WHERE memberEmail = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // S13: CheckPwd
    public function CheckPwd($pwd1, $pwd2) {
        return $pwd1 === $pwd2;
    }

    // S16: CheckCellphone
    public function CheckCellphone($phone) {
        return preg_match("/^09[0-9]{8}$/", $phone);
    }

    // 整合 InsertId, InsertName, InsertEmail... 等函式
    private function InsertMember($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (memberId, memberName, memberEmail, memberPwd, memberPhone, memberBirth, memberPwdHintId, memberPwdHintAns) 
                  VALUES (:id, :name, :email, :pwd, :phone, :birth, :hintId, :hintAns)";
        
        $stmt = $this->conn->prepare($query);

        // 密碼加密 (安全需求)
        $hashedPwd = password_hash($data['memberPwd'], PASSWORD_DEFAULT);

        $stmt->bindParam(':id', $data['memberId']);
        $stmt->bindParam(':name', $data['memberName']);
        $stmt->bindParam(':email', $data['memberEmail']);
        $stmt->bindParam(':pwd', $hashedPwd);
        $stmt->bindParam(':phone', $data['memberPhone']);
        $stmt->bindParam(':birth', $data['memberBirth']);
        $stmt->bindParam(':hintId', $data['memberPwdHintId']);
        $stmt->bindParam(':hintAns', $data['memberPwdHintAns']);

        return $stmt->execute();
    }
// 在 SignUpSystem.php 類別中新增以下方法

    // S27: GenerateConfirm (產生驗證碼)
    public function GenerateConfirm() {
        // 產生 6 位數隨機碼
        $code = rand(100000, 999999);
        // 將驗證碼存入 Session 以供後續比對 (S30)
        if(!isset($_SESSION)) session_start();
        $_SESSION['register_otp'] = $code;
        return $code;
    }

    // S29: SendConfirm (模擬寄信)
    public function SendConfirm($email, $code) {
        // 在實際專案中會使用 PHPMailer
        // 這裡模擬寄送成功，將驗證碼寫入 log 或直接回傳給前端方便測試
        // file_put_contents("email_log.txt", "To: $email, Code: $code\n", FILE_APPEND);
        return true; 
    }

    // S30: CheckConfirm (驗證比對)
    public function CheckConfirm($inputCode) {
        if(!isset($_SESSION)) session_start();
        if (isset($_SESSION['register_otp']) && $_SESSION['register_otp'] == $inputCode) {
            return true;
        }
        return false;
    }

    // 修改原本的 MainSign 流程，加入驗證碼檢查
    public function MainSignWithOTP($data, $inputCode) {
        if (!$this->CheckConfirm($inputCode)) {
            return "驗證碼錯誤";
        }
        // 驗證通過，繼續執行原本的註冊邏輯
        return $this->MainSign($data);
    }
    // S31: ShowSign
    public function ShowSign($data) {
        return [
            "status" => "success",
            "message" => "註冊成功",
            "data" => [
                "name" => $data['memberName'],
                "id" => $data['memberId']
            ]
        ];
    }
}
?>