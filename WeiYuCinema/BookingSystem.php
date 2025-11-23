<?php
include_once 'DbConnect.php';

class BookingSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // =================================================================
    // 1. 查詢階段 (Query Phase) - B2 ~ B19
    // =================================================================

    // B2: SearchMovie & B3: DisplayMovie
    public function SearchMovie() {
        $query = "SELECT movieId, movieName, movieImg, gradeId FROM movie"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // B6: SearchDate & B7: DisplayDate
    public function SearchDate($movieId) {
        $query = "SELECT DISTINCT showingDate FROM showing 
                  WHERE movieId = :movieId 
                  ORDER BY showingDate ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':movieId', $movieId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // B9: SearchShowing & B10: DisplayShowing
    public function SearchShowing($movieId, $date) {
        $query = "SELECT s.showingId, s.startTime, c.cinemaName, s.theaterId 
                  FROM showing s
                  JOIN cinema c ON s.cinemaId = c.cinemaId
                  WHERE s.movieId = :movieId AND s.showingDate = :date
                  ORDER BY s.startTime ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':movieId', $movieId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // B13: DisplayType (列出票種)
    public function GetTicketTypes() {
        $query = "SELECT ticketClassId, ticketClassName, ticketClassPrice FROM ticketClass";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // B16: DisplayMenu (列出餐點)
    public function GetMeals() {
        $query = "SELECT mealsId, mealsName, mealsPrice, mealsPhoto FROM meals";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // B18: SearchSeat & B19: DisplaySeat
    public function SearchSeat($showingId) {
        $query = "SELECT seatNumber, seatEmpty FROM seatCondition WHERE showingId = :showingId ORDER BY LENGTH(seatNumber), seatNumber";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':showingId', $showingId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =================================================================
    // 2. 流程暫存階段 (Session Phase) - B5, B12 (新增部分)
    // =================================================================

    // B5: InsertMovie (暫存使用者選擇的電影 ID)
    // 功能：當使用者選好電影，將 ID 存入 Session，方便下一步驟使用
    public function InsertMovie($movieId) {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['booking_step1_movieId'] = $movieId;
        return true;
    }

    // B12: InsertShowing (暫存使用者選擇的場次 ID)
    // 功能：當使用者選好場次，將 ID 存入 Session
    public function InsertShowing($showingId) {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['booking_step2_showingId'] = $showingId;
        return true;
    }

    // =================================================================
    // 3. 交易階段 (Transaction Phase) - B1, B20, B28, B29
    // =================================================================

    // B1 MainBook: 建立訂單主流程
    public function CreateBooking($memberId, $showingId, $seats, $totalPrice, $paymentMethod, $mealString, $mainTicketTypeId) {
        try {
            // 1. 開啟資料庫交易
            $this->conn->beginTransaction();

            // 2. 檢查座位有效性
            foreach ($seats as $seatNum) {
                if (!$this->isSeatAvailable($showingId, $seatNum)) {
                    throw new Exception("很抱歉，座位 $seatNum 剛剛已被其他使用者選走。");
                }
            }

            // 3. B20: ChooseSeat (鎖定座位)
            $this->lockSeats($showingId, $seats);

            // 4. B28: 付款處理
            $orderStatusId = 1; // 預設 1: 已支付
            if ($paymentMethod === 'card') {
                if (!$this->processCardPayment($memberId, $totalPrice)) {
                    throw new Exception("儲值卡餘額不足，請先儲值或更換付款方式。");
                }
            } 
            // 若有串接第三方金流，可能會先設為 0 (未支付)，等待 Callback 呼叫 ChangePaid 改為 1

            // 5. B29: 生成訂單資料
            $orderNumber = $this->generateOrderNumber();
            $ticketNum = rand(100000, 999999);
            $seatString = implode(",", $seats);
            $ticketNums = count($seats);

            // 6. 寫入訂單
            $query = "INSERT INTO bookingRecord 
                      (orderNumber, memberId, showingId, time, seat, chooseMeal, ticketTypeId, ticketNums, totalPrice, orderStatusId, getTicketNum) 
                      VALUES (:ordNo, :mid, :sid, NOW(), :seat, :meals, :tid, :nums, :price, :status, :getTick)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':ordNo', $orderNumber);
            $stmt->bindParam(':mid', $memberId);
            $stmt->bindParam(':sid', $showingId);
            $stmt->bindParam(':seat', $seatString);
            $stmt->bindParam(':meals', $mealString);
            $stmt->bindParam(':tid', $mainTicketTypeId);
            $stmt->bindParam(':nums', $ticketNums);
            $stmt->bindParam(':price', $totalPrice);
            $stmt->bindParam(':status', $orderStatusId);
            $stmt->bindParam(':getTick', $ticketNum);
            
            if (!$stmt->execute()) {
                throw new Exception("系統繁忙，訂單建立失敗。");
            }

            // 7. 提交交易
            $this->conn->commit();

            // B30: 回傳結果
            return [
                "status" => true, 
                "message" => "訂票成功！",
                "data" => [
                    "orderNumber" => $orderNumber,
                    "getTicketNum" => $ticketNum,
                    "totalPrice" => $totalPrice
                ]
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            return ["status" => false, "message" => "訂票失敗: " . $e->getMessage()];
        }
    }

    // =================================================================
    // 4. 訂單管理階段 (Management Phase) - B31 (新增部分)
    // =================================================================

    // B31: ChangePaid (更新付款狀態)
    // 功能：可用於後台管理員修改狀態，或第三方金流 Callback 更新狀態
    // statusId: 1=已支付, 2=已取消, 0=未支付
    public function ChangePaid($orderNumber, $statusId) {
        $query = "UPDATE bookingRecord SET orderStatusId = :status WHERE orderNumber = :ordNo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $statusId);
        $stmt->bindParam(':ordNo', $orderNumber);
        
        if ($stmt->execute()) {
            return ["status" => true, "message" => "訂單狀態更新成功"];
        } else {
            return ["status" => false, "message" => "訂單狀態更新失敗"];
        }
    }

    // =================================================================
    // 5. 內部輔助函式 (Private Helper Functions)
    // =================================================================

    private function isSeatAvailable($showingId, $seatNum) {
        $sql = "SELECT seatEmpty FROM seatCondition 
                WHERE showingId = :sid AND seatNumber = :snum 
                FOR UPDATE"; 
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':sid', $showingId);
        $stmt->bindParam(':snum', $seatNum);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row || $row['seatEmpty'] != 1) {
            return false;
        }
        return true;
    }

    private function lockSeats($showingId, $seats) {
        $sql = "UPDATE seatCondition SET seatEmpty = 0 
                WHERE showingId = :sid AND seatNumber = :snum";
        $stmt = $this->conn->prepare($sql);
        foreach ($seats as $seatNum) {
            $stmt->bindValue(':sid', $showingId);
            $stmt->bindValue(':snum', $seatNum);
            $stmt->execute();
        }
    }

    private function processCardPayment($memberId, $amount) {
        $sql = "SELECT balance FROM memberCashCard WHERE memberId = :mid FOR UPDATE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':mid', $memberId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row || $row['balance'] < $amount) {
            return false;
        }

        $upd = "UPDATE memberCashCard SET balance = balance - :amt WHERE memberId = :mid";
        $stmtUpd = $this->conn->prepare($upd);
        $stmtUpd->bindParam(':amt', $amount);
        $stmtUpd->bindParam(':mid', $memberId);
        return $stmtUpd->execute();
    }

    private function generateOrderNumber() {
        return "ORD" . date("YmdHis") . rand(10, 99);
    }
}
?>