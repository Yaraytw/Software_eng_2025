<?php
include_once 'DbConnect.php';

class AdminSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // 對應 admin.html 的 addMovie()
    public function AddMovie($name, $gradeId) {
        // 這裡簡化處理，實際可增加更多欄位如上映日期、介紹等
        $query = "INSERT INTO movie (movieName, gradeId, movieTime, movieStart) VALUES (:name, :grade, 120, '2025-01-01')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':grade', $gradeId);
        
        if ($stmt->execute()) {
            return ["status" => true, "message" => "電影新增成功"];
        }
        return ["status" => false, "message" => "新增失敗"];
    }

    // 對應 admin.html 的 addShowing()
    public function AddShowing($movieId, $date, $time) {
        // 假設預設影城 ID 為 1，影廳 A 廳
        $cinemaId = "1"; 
        $theaterId = "A"; 

        $query = "INSERT INTO showing (movieId, showingDate, startTime, cinemaId, theaterId) 
                  VALUES (:mid, :date, :time, :cid, :tid)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mid', $movieId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':cid', $cinemaId);
        $stmt->bindParam(':tid', $theaterId);

        if ($stmt->execute()) {
            // 自動為該場次產生 20 個空座位 (對應 seatCondition)
            $showingId = $this->conn->lastInsertId();
            $this->initSeats($showingId);
            return ["status" => true, "message" => "場次新增成功"];
        }
        return ["status" => false, "message" => "新增失敗"];
    }

    // 初始化座位
    private function initSeats($showingId) {
        $sql = "INSERT INTO seatCondition (showingId, seatNumber, seatEmpty) VALUES (:sid, :num, 1)";
        $stmt = $this->conn->prepare($sql);
        for ($i = 1; $i <= 20; $i++) {
            $stmt->bindValue(':sid', $showingId);
            $stmt->bindValue(':num', $i);
            $stmt->execute();
        }
    }
}
?>