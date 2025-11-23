<?php
include_once 'DbConnect.php';

class MemberChangeSystem {
    private $conn;
    private $table_name = "memberProfile";

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // M1: MainMemberChange (更改會員主函式)
    public function MainMemberChange($memberId, $newData) {
        // M3: GetUserinMemberChange (取得輸入) - 這裡由參數 $newData 傳入
        
        // M4: CheckPhoneFmt
        if (isset($newData['memberPhone']) && !$this->CheckPhoneFmt($newData['memberPhone'])) {
            return ["status" => false, "message" => "手機格式錯誤"];
        }

        // M5: CheckEmailFmt
        if (isset($newData['memberEmail']) && !$this->CheckEmailFmt($newData['memberEmail'])) {
            return ["status" => false, "message" => "信箱格式錯誤"];
        }

        // M6: SaveMemberChange
        if ($this->SaveMemberChange($memberId, $newData)) {
            return ["status" => true, "message" => "更改資料成功"];
        } else {
            return ["status" => false, "message" => "更改失敗"];
        }
    }

    // M2: ShowMemberInfo (顯示會員資料)
    public function ShowMemberInfo($memberId) {
        $query = "SELECT memberName, memberEmail, memberPhone, memberBirth, memberPayAccount 
                  FROM " . $this->table_name . " WHERE memberId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $memberId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // M4: CheckPhoneFmt
    private function CheckPhoneFmt($phone) {
        return preg_match("/^09[0-9]{8}$/", $phone);
    }

    // M5: CheckEmailFmt
    private function CheckEmailFmt($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // M6: SaveMemberChange (儲存修改資訊)
    private function SaveMemberChange($memberId, $data) {
        // 動態構建 SQL 更新語句
        $fields = [];
        $params = [':id' => $memberId];

        if (!empty($data['memberName'])) {
            $fields[] = "memberName = :name";
            $params[':name'] = $data['memberName'];
        }
        if (!empty($data['memberPhone'])) {
            $fields[] = "memberPhone = :phone";
            $params[':phone'] = $data['memberPhone'];
        }
        if (!empty($data['memberPwd'])) {
            $fields[] = "memberPwd = :pwd";
            $params[':pwd'] = password_hash($data['memberPwd'], PASSWORD_DEFAULT);
        }
        // ... 其他欄位

        if (empty($fields)) {
            return false; // 無資料需更新
        }

        $sql = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE memberId = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }
}
?>