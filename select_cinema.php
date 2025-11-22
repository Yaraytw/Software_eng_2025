<?php
require_once 'includes/functions.php';
requireLogin();

$selectedMovieID = $_GET['movieID'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Cinema - Cinema Ticketing System</title>
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
            <h2>Select Cinema</h2>
            
            <?php if ($selectedMovieID): ?>
                <div class="alert alert-info">
                    You have pre-selected a movie. Please choose a cinema to continue.
                </div>
            <?php endif; ?>
            
            <div class="grid">
                <?php
                $conn = getDBConnection();
                $sql = "SELECT * FROM cinema WHERE status = 'open' ORDER BY city, cinemaName";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while ($cinema = $result->fetch_assoc()) {
                        $url = 'select_movie.php?cinemaID=' . $cinema['cinemaID'];
                        if ($selectedMovieID) {
                            $url .= '&movieID=' . $selectedMovieID;
                        }
                        
                        echo '<div class="cinema-card" onclick="window.location.href=\'' . $url . '\'">';
                        echo '<h3>' . htmlspecialchars($cinema['cinemaName']) . '</h3>';
                        echo '<p><strong>📍 Location:</strong> ' . htmlspecialchars($cinema['city']) . '</p>';
                        echo '<p>' . htmlspecialchars($cinema['address']) . '</p>';
                        echo '<p><strong>📞 Phone:</strong> ' . htmlspecialchars($cinema['phoneNumber']) . '</p>';
                        echo '<p><strong>🕐 Hours:</strong> ' . htmlspecialchars($cinema['openingHours']) . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No cinemas available at the moment.</p>';
                }
                
                closeDBConnection($conn);
                ?>
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
