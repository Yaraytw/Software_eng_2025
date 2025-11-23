<?php
include_once 'DbConnect.php';

class BrowseSystem {
    private $conn;

    public function __construct() {
        $database = new DbConnect();
        $this->conn = $database->getConnection();
    }

    // Br1: MainBrowse (瀏覽查詢主模組)
    // 通常作為 Router 分發到下方各個 Getter

    // Br3: DisplayCinemaInfor
    public function GetCinemaInfo($cinemaId = null) {
        $sql = "SELECT * FROM cinema";
        if ($cinemaId) {
            $sql .= " WHERE cinemaId = :cid";
        }
        $stmt = $this->conn->prepare($sql);
        if ($cinemaId) $stmt->bindParam(':cid', $cinemaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Br4 ~ Br10: GetMovieChoose / DisplayMovie (整合)
    public function GetMovies($status = 'now') {
        // status 可以是 'now' (上映中) 或 'coming' (即將上映)
        // 這裡簡化為撈取所有
        $query = "SELECT m.*, g.gradeName 
                  FROM movie m 
                  LEFT JOIN grade g ON m.gradeId = g.gradeId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Br11: DisplayShowing (顯示場次)
    public function GetShowings($movieId, $cinemaId) {
        $query = "SELECT * FROM showing 
                  WHERE movieId = :mid AND cinemaId = :cid
                  ORDER BY showingDate, startTime";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mid', $movieId);
        $stmt->bindParam(':cid', $cinemaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Br12: DisplayMoreMovieInfo (詳細資訊)
    public function GetMovieDetail($movieId) {
        $query = "SELECT * FROM movie WHERE movieId = :mid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mid', $movieId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>