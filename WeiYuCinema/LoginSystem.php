<?php
session_start();
include_once 'DbConnect.php';

class LoginSystem {
    private $conn;
    private $table_name = "memberProfile";

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // L1: MainLogin (登入主模組)
    public function MainLogin($account, $pwd) {
        // L5: CheckLogin
        if ($this->CheckLogin($account, $pwd)) {
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['memberAccount'] = $account;
            return ["status" => true, "message" => "登入成功"];
        } else {
            return ["status" => false, "message" => "帳號或密碼錯誤"]; // L5 False
        }
    }

    // L2: CheckLoginState
    public function CheckLoginState() {
        if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
            return true;
        }
        return false;
    }

    // L5: CheckLogin (資料庫比對)
    public function CheckLogin($email, $pwd) {
        $query = "SELECT memberPwd FROM " . $this->table_name . " WHERE memberEmail = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // 使用 password_verify 驗證加密後的密碼
            if (password_verify($pwd, $row['memberPwd'])) {
                return true;
            }
        }
        return false;
    }

    // L7: ReturnHint (取得使用者的密碼提示題目)
    public function ReturnHint($email) {
        // 假設 memberPwdQuestion 表存在，或直接從 memberProfile 拿 hintId 轉文字
        // 這裡示範直接回傳 HintId，前端再轉成文字，或是這裡做 Join
        $query = "SELECT memberPwdHintId FROM " . $this->table_name . " WHERE memberEmail = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return ["status" => true, "hintId" => $row['memberPwdHintId']];
        }
        return ["status" => false, "message" => "查無此帳號"];
    }
    
    // L11: Logout
    public function Logout() {
        session_destroy();
        return ["status" => true, "message" => "登出成功"];
    }

    // L6~L10: ForgetPwd 流程 (簡化版)
    public function ForgetPwdFlow($email, $hintId, $ans, $newPwd) {
        // L9 CheckHint
        if ($this->CheckHint($email, $hintId, $ans)) {
             // L10 ResetPwd
             return $this->ResetPwd($email, $newPwd);
        }
        return false;
    }

    // L9: CheckHint
    private function CheckHint($email, $hintId, $ans) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE memberEmail = :email AND memberPwdHintId = :hintId AND memberPwdHintAns = :ans";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':hintId', $hintId);
        $stmt->bindParam(':ans', $ans);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // L10: ResetPwd
    private function ResetPwd($email, $newPwd) {
        $query = "UPDATE " . $this->table_name . " SET memberPwd = :pwd WHERE memberEmail = :email";
        $stmt = $this->conn->prepare($query);
        $hashedPwd = password_hash($newPwd, PASSWORD_DEFAULT);
        $stmt->bindParam(':pwd', $hashedPwd);
        $stmt->bindParam(':email', $email);
        
        if($stmt->execute()) {
            return "密碼更改成功";
        } else {
            return "請輸入相同密碼"; // 根據規格書 L10 的 False 訊息
        }
    }
}
?>