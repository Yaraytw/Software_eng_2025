<?php
require_once 'includes/functions.php';
requireLogin();

$cinemaID = $_GET['cinemaID'] ?? null;
$selectedMovieID = $_GET['movieID'] ?? null;

if (!$cinemaID) {
    header('Location: select_cinema.php');
    exit;
}

// Get cinema info
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM cinema WHERE cinemaID = ?");
$stmt->bind_param("i", $cinemaID);
$stmt->execute();
$cinemaResult = $stmt->get_result();
$cinema = $cinemaResult->fetch_assoc();
$stmt->close();

if (!$cinema) {
    header('Location: select_cinema.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Movie - Cinema Ticketing System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>🎬 Cinema Ticketing System</h1>
        </div>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="select_cinema.php">Book Tickets</a></li>
            <li><a href="inquiry.php">My Bookings</a></li>
            <li><a href="topup.php">Top-up</a></li>
            <li><a href="logout.php">Logout (<?php echo htmlspecialchars(getCurrentUser()['username']); ?>)</a></li>
        </ul>
    </nav>
    
    <div class="container">
        <main>
            <h2>Select Movie</h2>
            
            <div class="card">
                <h3>Selected Cinema</h3>
                <p><strong><?php echo htmlspecialchars($cinema['cinemaName']); ?></strong></p>
                <p><?php echo htmlspecialchars($cinema['address']); ?>, <?php echo htmlspecialchars($cinema['city']); ?></p>
            </div>
            
            <div class="grid">
                <?php
                // Get movies available at this cinema
                $sql = "SELECT DISTINCT m.* FROM movie m 
                        INNER JOIN showing s ON m.movieID = s.movieID 
                        WHERE s.cinemaID = ? AND m.status = 'now_showing' AND s.status = 'available'
                        ORDER BY m.movieTitle";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $cinemaID);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows > 0) {
                    while ($movie = $result->fetch_assoc()) {
                        $url = 'select_session.php?cinemaID=' . $cinemaID . '&movieID=' . $movie['movieID'];
                        
                        echo '<div class="movie-card" onclick="window.location.href=\'' . $url . '\'"';
                        if ($selectedMovieID == $movie['movieID']) {
                            echo ' style="border: 3px solid #667eea;"';
                        }
                        echo '>';
                        echo '<h3>' . htmlspecialchars($movie['movieTitle']) . '</h3>';
                        echo '<p><strong>Genre:</strong> ' . htmlspecialchars($movie['genre']) . '</p>';
                        echo '<p><strong>Duration:</strong> ' . htmlspecialchars($movie['duration']) . ' mins</p>';
                        echo '<p><strong>Rating:</strong> ' . htmlspecialchars($movie['rating']) . '</p>';
                        echo '<p><strong>Director:</strong> ' . htmlspecialchars($movie['director']) . '</p>';
                        echo '<p>' . htmlspecialchars(substr($movie['description'], 0, 150)) . '...</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No movies available at this cinema.</p>';
                }
                
                $stmt->close();
                closeDBConnection($conn);
                ?>
            </div>
            
            <div style="margin-top: 2rem;">
                <a href="select_cinema.php" class="btn btn-secondary">← Change Cinema</a>
            </div>
        </main>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Cinema Ticketing System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
