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
        // 假設 bookingRecord 有紀錄付款方式，或透過 PayAccount 判斷
        
        $refundSuccess = false;
        
        // R6 & R7: 退款流程
        // 假設我們從 memberProfile 判斷或 bookingRecord 紀錄的付款方式
        // 這裡示範退回儲值卡 (R7)
        $refundSuccess = $this->RefundToCard($userId, $orderInfo['totalPrice']);
        
        // 若是銀行卡 (R6)，通常是呼叫銀行 API 退刷，這裡省略

        if ($refundSuccess) {
            // R8: RecordRefund (寫入資料庫 - 更新狀態)
            $this->RecordRefund($orderNumber);
            return $this->DisplayRefundSuccess(); // R9
        } else {
            return $this->DisplayRefundError("退款處理失敗"); // R10
        }
    }

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

    // R7: RefundToCard
    private function RefundToCard($memberId, $amount) {
        $query = "UPDATE memberCashCard SET balance = balance + :amount WHERE memberId = :mid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':mid', $memberId);
        return $stmt->execute();
    }

    // R8: RecordRefund
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

    // R9
    private function DisplayRefundSuccess() {
        return ["status" => true, "message" => "退票成功"];
    }

    // R10
    private function DisplayRefundError($msg) {
        return ["status" => false, "message" => $msg];
    }
}
?>