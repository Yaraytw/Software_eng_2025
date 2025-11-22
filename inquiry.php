<?php
require_once 'includes/functions.php';
requireLogin();

$success = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = 'Booking confirmed successfully! Booking ID: #' . ($_GET['bookingID'] ?? 'N/A');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Cinema Ticketing System</title>
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
            <h2>My Bookings</h2>
            
            <?php if ($success): ?>
                <?php echo showSuccess($success); ?>
            <?php endif; ?>
            
            <?php
            $conn = getDBConnection();
            
            // Get all bookings for current user
            $sql = "SELECT b.*, s.showDate, s.showTime, s.price,
                           m.movieTitle, c.cinemaName, t.theaterName
                    FROM bookingRecord b
                    INNER JOIN showing s ON b.showingID = s.showingID
                    INNER JOIN movie m ON s.movieID = m.movieID
                    INNER JOIN cinema c ON s.cinemaID = c.cinemaID
                    INNER JOIN theater t ON s.theaterID = t.theaterID
                    WHERE b.memberID = ?
                    ORDER BY b.bookingDate DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['memberID']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($booking = $result->fetch_assoc()) {
                    // Get seat details for this booking
                    $seatQuery = "SELECT seatRow, seatNumber FROM seatCondition WHERE bookingID = ? ORDER BY seatRow, seatNumber";
                    $seatStmt = $conn->prepare($seatQuery);
                    $seatStmt->bind_param("i", $booking['bookingID']);
                    $seatStmt->execute();
                    $seatResult = $seatStmt->get_result();
                    $seatList = [];
                    while ($seat = $seatResult->fetch_assoc()) {
                        $seatList[] = $seat['seatRow'] . $seat['seatNumber'];
                    }
                    $seatStmt->close();
                    
                    $statusClass = '';
                    switch ($booking['bookingStatus']) {
                        case 'confirmed':
                            $statusClass = 'alert-success';
                            break;
                        case 'cancelled':
                            $statusClass = 'alert-error';
                            break;
                        case 'refunded':
                            $statusClass = 'alert-info';
                            break;
                    }
                    
                    echo '<div class="card">';
                    echo '<div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap;">';
                    echo '<div>';
                    echo '<h3>Booking #' . $booking['bookingID'] . '</h3>';
                    echo '<p><span class="alert ' . $statusClass . '" style="display: inline-block; padding: 0.25rem 0.5rem; margin: 0.5rem 0;">' . ucfirst($booking['bookingStatus']) . '</span></p>';
                    echo '</div>';
                    echo '<div style="text-align: right;">';
                    echo '<p><strong>Booked on:</strong> ' . date('M j, Y g:i A', strtotime($booking['bookingDate'])) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    
                    echo '<hr style="margin: 1rem 0; border: 0; border-top: 1px solid #ddd;">';
                    
                    echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">';
                    echo '<div>';
                    echo '<p><strong>Movie:</strong> ' . htmlspecialchars($booking['movieTitle']) . '</p>';
                    echo '<p><strong>Cinema:</strong> ' . htmlspecialchars($booking['cinemaName']) . '</p>';
                    echo '<p><strong>Theater:</strong> ' . htmlspecialchars($booking['theaterName']) . '</p>';
                    echo '</div>';
                    echo '<div>';
                    echo '<p><strong>Date:</strong> ' . date('l, F j, Y', strtotime($booking['showDate'])) . '</p>';
                    echo '<p><strong>Time:</strong> ' . date('g:i A', strtotime($booking['showTime'])) . '</p>';
                    echo '<p><strong>Seats:</strong> ' . implode(', ', $seatList) . '</p>';
                    echo '</div>';
                    echo '<div>';
                    echo '<p><strong>Total Seats:</strong> ' . $booking['totalSeats'] . '</p>';
                    echo '<p><strong>Total Amount:</strong> $' . number_format($booking['totalAmount'], 2) . '</p>';
                    echo '<p><strong>Payment:</strong> ' . ucfirst(str_replace('_', ' ', $booking['paymentMethod'])) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    
                    if ($booking['bookingStatus'] === 'confirmed') {
                        // Allow refund only for future showings
                        $showDateTime = strtotime($booking['showDate'] . ' ' . $booking['showTime']);
                        if ($showDateTime > time()) {
                            echo '<div style="margin-top: 1rem;">';
                            echo '<a href="refund.php?bookingID=' . $booking['bookingID'] . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to request a refund for this booking?\')">Request Refund</a>';
                            echo '</div>';
                        }
                    }
                    
                    if ($booking['bookingStatus'] === 'refunded' && $booking['refundDate']) {
                        echo '<p style="margin-top: 1rem; color: #666;"><em>Refunded on ' . date('M j, Y g:i A', strtotime($booking['refundDate'])) . ' - Amount: $' . number_format($booking['refundAmount'], 2) . '</em></p>';
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<div class="card">';
                echo '<p>You have no bookings yet.</p>';
                echo '<a href="select_cinema.php" class="btn">Book Your First Movie</a>';
                echo '</div>';
            }
            
            $stmt->close();
            closeDBConnection($conn);
            ?>
        </main>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Cinema Ticketing System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
