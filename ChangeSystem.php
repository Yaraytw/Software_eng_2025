<?php
include_once 'DbConnect.php';

class ChangeTicketSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // C1: MainChange (修改訂票方案主模組)
    public function MainChange($oldOrderNumber, $newBookingData) {
        // 1. 檢查舊訂單是否符合換票時間 (C3 CheckTwoHour 邏輯同 Refund)
        // 為簡化，假設已通過時間檢查

        // 2. 計算差價 (C8)
        $diff = $this->PriceDifference($oldOrderNumber, $newBookingData['totalPrice']);
        
        // 3. 處理差價 (退款或補繳)
        // 若 diff > 0 (新票較便宜)，退 diff
        // 若 diff < 0 (新票較貴)，需補繳 abs(diff)
        
        // 4. 執行換票 (C9 RecordChange)
        // 實務上通常是：將舊訂單設為無效/已換票，並建立新訂單
        if ($this->RecordChange($oldOrderNumber, $newBookingData)) {
            return $this->DisplayChangeSuccess($diff); // C10
        } else {
            return $this->DisplayChangeError(); // C11
        }
    }

    // C8: PriceDifference
    private function PriceDifference($oldOrderNumber, $newTotalPrice) {
        // 取得舊訂單金額
        $query = "SELECT totalPrice FROM bookingRecord WHERE orderNumber = :ordNo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordNo', $oldOrderNumber);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldPrice = $row['totalPrice'] ?? 0;

        return $oldPrice - $newTotalPrice;
    }

    // C9: RecordChange (將更改紀錄寫入資料庫)
    private function RecordChange($oldOrderNumber, $newData) {
        try {
            $this->conn->beginTransaction();

            // 1. 更新舊訂單狀態為 "已換票" (假設 ID 3)
            $upd = $this->conn->prepare("UPDATE bookingRecord SET orderStatusId = 3 WHERE orderNumber = ?");
            $upd->execute([$oldOrderNumber]);

            // 2. 插入新訂單 (簡化版，欄位應對應 newData)
            // 實際需呼叫 Element B 的 CreateBooking 邏輯
            // $this->bookingSystem->CreateBooking($newData); 
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // C10
    private function DisplayChangeSuccess($diff) {
        $msg = "換票成功";
        if ($diff > 0) $msg .= "，已退還差價 $$diff";
        if ($diff < 0) $msg .= "，已補繳差價 $" . abs($diff);
        return ["status" => true, "message" => $msg];
    }

    // C11
    private function DisplayChangeError() {
        return ["status" => false, "message" => "換票失敗"];
    }
}
?>