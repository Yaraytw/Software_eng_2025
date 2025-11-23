<?php
include_once 'DbConnect.php';

class RefundSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // R1: MainRefund (退票主模組)
    public function MainRefund($orderNumber, $userId) {
        // R2: RefundChoose (使用者已選擇訂單，傳入 orderNumber)

        // 獲取訂單與場次資訊
        $orderInfo = $this->getOrderInfo($orderNumber);
        
        if (!$orderInfo) {
            return $this->DisplayRefundError("找不到訂單"); // R10
        }

        // R3: CheckTwoHour (檢查是否為放映前兩小時)
        if (!$this->CheckTwoHour($orderInfo['showingDate'], $orderInfo['startTime'])) {
            return $this->DisplayRefundError("已超過退票期限 (需於開演前2小時退票)"); // R10
        }

        // R4: GetTicketStyle (搜尋票型 - 一般或特殊)
        // 這裡簡化邏輯：假設皆可退，實際可根據 ticketTypeId 判斷

        // R5: GetPayWay (搜尋付款方式)
        // 這裡呼叫輔助函式判斷付款方式 (bank 或 card)
        $paymentMethod = $this->GetPayWay($orderNumber);
        
        $refundSuccess = false;
        
        // 依照付款方式執行對應退款流程
        if ($paymentMethod == 'card') {
            // R7: 退回儲值卡
            $refundSuccess = $this->RefundToCard($userId, $orderInfo['totalPrice']);
        } else {
            // R6: 退回銀行卡
            $refundSuccess = $this->RefundToBank($userId, $orderInfo['totalPrice']);
        }

        if ($refundSuccess) {
            // R8: RecordRefund (寫入資料庫 - 更新狀態)
            $this->RecordRefund($orderNumber);
            return $this->DisplayRefundSuccess(); // R9
        } else {
            return $this->DisplayRefundError("退款處理失敗"); // R10
        }
    }

    // =================================================================
    // 輔助與核心邏輯函式
    // =================================================================

    // R3: CheckTwoHour
    private function CheckTwoHour($date, $time) {
        // 組合場次時間字串
        $showingDatetimeStr = $date . ' ' . $time; // e.g., "2025-10-30 14:30"
        $showingTimestamp = strtotime($showingDatetimeStr);
        $currentTimestamp = time();

        // 計算差距秒數 (2小時 = 7200秒)
        if (($showingTimestamp - $currentTimestamp) > 7200) {
            return true; // 可退票
        }
        return false; // 不可退票
    }

    // R5: GetPayWay (判斷付款方式)
    private function GetPayWay($orderNumber) {
        // 由於規格書資料庫設計未明確規範「付款方式」存在哪張表
        // 這裡模擬：若 memberCashCard 有扣款紀錄則為 card，否則為 bank
        // 或是根據你的實作邏輯，預設回傳 'card' 方便測試
        return 'card'; 
    }

    // R6: RefundToBank (模擬銀行退款)
    private function RefundToBank($memberId, $amount) {
        // 實際專案會呼叫銀行 API (例如 Stripe, PayPal, 綠界)
        // 這裡直接回傳 true 代表銀行端退刷成功
        return true; 
    }

    // R7: RefundToCard (儲值卡退款)
    private function RefundToCard($memberId, $amount) {
        $query = "UPDATE memberCashCard SET balance = balance + :amount WHERE memberId = :mid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':mid', $memberId);
        return $stmt->execute();
    }

    // R8: RecordRefund (更新訂單狀態)
    private function RecordRefund($orderNumber) {
        // 假設 OrderStatusId: 1=已支付, 2=已退票
        $query = "UPDATE bookingRecord SET orderStatusId = 2 WHERE orderNumber = :ordNo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordNo', $orderNumber);
        return $stmt->execute();
    }

    // Helper: 獲取訂單資訊
    private function getOrderInfo($orderNumber) {
        $query = "SELECT s.showingDate, s.startTime, br.totalPrice 
                  FROM bookingRecord br
                  JOIN showing s ON br.showingId = s.showingId
                  WHERE br.orderNumber = :ordNo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordNo', $orderNumber);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // R9: 顯示成功
    private function DisplayRefundSuccess() {
        return ["status" => true, "message" => "退票成功"];
    }

    // R10: 顯示失敗
    private function DisplayRefundError($msg) {
        return ["status" => false, "message" => $msg];
    }
}
?>