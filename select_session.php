<?php
require_once 'includes/functions.php';
requireLogin();

$cinemaID = $_GET['cinemaID'] ?? null;
$movieID = $_GET['movieID'] ?? null;

if (!$cinemaID || !$movieID) {
    header('Location: select_cinema.php');
    exit;
}

// Get cinema and movie info
$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM cinema WHERE cinemaID = ?");
$stmt->bind_param("i", $cinemaID);
$stmt->execute();
$cinema = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM movie WHERE movieID = ?");
$stmt->bind_param("i", $movieID);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cinema || !$movie) {
    header('Location: select_cinema.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Session - Cinema Ticketing System</title>
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
            <h2>Select Session</h2>
            
            <div class="card">
                <h3>Booking Details</h3>
                <p><strong>Cinema:</strong> <?php echo htmlspecialchars($cinema['cinemaName']); ?></p>
                <p><strong>Movie:</strong> <?php echo htmlspecialchars($movie['movieTitle']); ?></p>
                <p><strong>Duration:</strong> <?php echo htmlspecialchars($movie['duration']); ?> minutes</p>
            </div>
            
            <?php
            // Get showings grouped by date
            $sql = "SELECT s.*, t.theaterName, t.theaterType 
                    FROM showing s
                    INNER JOIN theater t ON s.theaterID = t.theaterID
                    WHERE s.cinemaID = ? AND s.movieID = ? AND s.status = 'available'
                    AND s.showDate >= CURDATE()
                    ORDER BY s.showDate, s.showTime";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $cinemaID, $movieID);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $currentDate = null;
                
                while ($showing = $result->fetch_assoc()) {
                    // Display date header if it's a new date
                    if ($currentDate !== $showing['showDate']) {
                        if ($currentDate !== null) {
                            echo '</div>'; // Close previous date's sessions
                        }
                        $currentDate = $showing['showDate'];
                        echo '<h3 style="margin-top: 2rem;">' . date('l, F j, Y', strtotime($showing['showDate'])) . '</h3>';
                        echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">';
                    }
                    
                    echo '<div class="card" style="cursor: pointer; transition: transform 0.2s;" onclick="window.location.href=\'select_seat.php?showingID=' . $showing['showingID'] . '\'">';
                    echo '<h4>' . date('g:i A', strtotime($showing['showTime'])) . '</h4>';
                    echo '<p><strong>Theater:</strong> ' . htmlspecialchars($showing['theaterName']) . ' (' . ucfirst($showing['theaterType']) . ')</p>';
                    echo '<p><strong>Price:</strong> $' . number_format($showing['price'], 2) . '</p>';
                    echo '<p><strong>Available Seats:</strong> ' . $showing['availableSeats'] . '</p>';
                    echo '<div style="margin-top: 1rem;">';
                    echo '<span class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Select</span>';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>'; // Close last date's sessions
            } else {
                echo '<p>No sessions available for this movie at this cinema.</p>';
            }
            
            $stmt->close();
            closeDBConnection($conn);
            ?>
            
            <div style="margin-top: 2rem;">
                <a href="select_movie.php?cinemaID=<?php echo $cinemaID; ?>" class="btn btn-secondary">← Change Movie</a>
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
