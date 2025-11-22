<?php
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema Ticketing System</title>
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
            <?php if (isLoggedIn()): ?>
                <li><a href="select_cinema.php">Book Tickets</a></li>
                <li><a href="inquiry.php">My Bookings</a></li>
                <li><a href="topup.php">Top-up</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars(getCurrentUser()['username']); ?>)</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="container">
        <main>
            <h2>Welcome to Cinema Ticketing System</h2>
            
            <?php if (isLoggedIn()): ?>
                <div class="card">
                    <h3>Hello, <?php echo htmlspecialchars(getCurrentUser()['fullName']); ?>!</h3>
                    <p>Ready to watch a movie? Start by selecting a cinema or browse our current showings.</p>
                    <a href="select_cinema.php" class="btn">Book Tickets Now</a>
                </div>
            <?php else: ?>
                <div class="card">
                    <h3>Get Started</h3>
                    <p>Please login or register to book movie tickets.</p>
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div>
            <?php endif; ?>
            
            <h2>Now Showing</h2>
            <div class="grid">
                <?php
                $conn = getDBConnection();
                $sql = "SELECT * FROM movie WHERE status = 'now_showing' ORDER BY releaseDate DESC LIMIT 6";
                $result = $conn->query($sql);
                
                if ($result && $result->num_rows > 0) {
                    while ($movie = $result->fetch_assoc()) {
                        echo '<div class="movie-card">';
                        echo '<h3>' . htmlspecialchars($movie['movieTitle']) . '</h3>';
                        echo '<p><strong>Genre:</strong> ' . htmlspecialchars($movie['genre']) . '</p>';
                        echo '<p><strong>Duration:</strong> ' . htmlspecialchars($movie['duration']) . ' mins</p>';
                        echo '<p><strong>Rating:</strong> ' . htmlspecialchars($movie['rating']) . '</p>';
                        echo '<p>' . htmlspecialchars(substr($movie['description'], 0, 100)) . '...</p>';
                        if (isLoggedIn()) {
                            echo '<a href="select_cinema.php?movieID=' . $movie['movieID'] . '" class="btn">Book Now</a>';
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<p>No movies currently showing.</p>';
                }
                
                closeDBConnection($conn);
                ?>
            </div>
            
            <div class="card" style="margin-top: 2rem;">
                <h3>Features</h3>
                <ul style="padding-left: 2rem;">
                    <li>Easy online ticket booking</li>
                    <li>Multiple cinema locations</li>
                    <li>Flexible seat selection</li>
                    <li>Cash card top-up system</li>
                    <li>Booking inquiry and management</li>
                    <li>Refund support</li>
                </ul>
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
