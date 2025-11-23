<?php
include_once 'DbConnect.php';

class TopUpSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // T1: MainTopUpCard
    public function MainTopUpCard($memberId, $amount, $paymentInfo) {
        // T3: InputPayAccount (取得輸入)
        
        // T4: CheckPayAccount (驗證格式/正確性)
        if (!$this->CheckPayAccount($paymentInfo)) {
             return "付款資訊錯誤";
        }

        // 執行儲值
        if ($this->UpdateBalance($memberId, $amount)) {
            // T7: RecordTopUpCard (紀錄加值資訊)
            return $this->DisplayTopUpSuccess(); // T5
        } else {
            return $this->DisplayTopUpError(); // T6
        }
    }

    // T2: SearchBalance
    public function SearchBalance($memberId) {
        $query = "SELECT balance FROM memberCashCard WHERE memberId = :mid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mid', $memberId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function CheckPayAccount($info) {
        // 模擬驗證銀行API
        return true;
    }

    private function UpdateBalance($memberId, $amount) {
        $query = "UPDATE memberCashCard SET balance = balance + :amount WHERE memberId = :mid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':mid', $memberId);
        return $stmt->execute();
    }

    // T5
    private function DisplayTopUpSuccess() {
        return "加值成功";
    }

    // T6
    private function DisplayTopUpError() {
        return "加值失敗";
    }
}
?>