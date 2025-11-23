<?php
include_once 'DbConnect.php';

class InquiryTicketSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // In1: MainInquiry (訂票紀錄查詢主模組)
    public function MainInquiry($memberId) {
        // In2: SearchTicket (搜尋訂票紀錄)
        $records = $this->SearchTicket($memberId);
        
        if (empty($records)) {
            return ["status" => false, "message" => "查無訂票紀錄"];
        }

        // In3 ~ In10: 這些是前端顯示邏輯 (Display...)，後端負責回傳完整的資料結構
        return ["status" => true, "data" => $records];
    }

    // In2: SearchTicket
    private function SearchTicket($memberId) {
        // 聯表查詢：訂單 -> 場次 -> 電影 & 影城 & 影廳
        $query = "SELECT 
                    br.getTicketNum,        -- In3: 取票序號
                    c.cinemaName,           -- In4: 影城名稱
                    m.movieName,            -- In5: 電影名稱
                    g.gradeName,            -- In5: 分級 (需關聯 grade 表)
                    s.showingDate, s.startTime, -- In6: 開場時間
                    tt.ticketTypeName,      -- In7: 票種
                    br.ticketNums,          -- In7: 張數
                    br.seat,                -- In8: 座位
                    br.chooseMeal,          -- In9: 餐點
                    br.totalPrice,          -- In10: 總金額
                    br.orderNumber,         -- 用於退票辨識
                    br.orderStatusId
                  FROM bookingRecord br
                  JOIN showing s ON br.showingId = s.showingId
                  JOIN movie m ON s.movieId = m.movieId
                  JOIN cinema c ON s.cinemaId = c.cinemaId
                  LEFT JOIN grade g ON m.gradeId = g.gradeId -- 假設有 grade 表
                  LEFT JOIN ticketType tt ON br.ticketTypeId = tt.ticketTypeId
                  WHERE br.memberId = :mid
                  ORDER BY br.time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mid', $memberId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>