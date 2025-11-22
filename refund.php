<?php
require_once 'includes/functions.php';
requireLogin();

$error = '';
$success = '';
$bookingID = $_GET['bookingID'] ?? null;

if (!$bookingID) {
    header('Location: inquiry.php');
    exit;
}

$conn = getDBConnection();

// Get booking details
$sql = "SELECT b.*, s.showDate, s.showTime, m.movieTitle, c.cinemaName
        FROM bookingRecord b
        INNER JOIN showing s ON b.showingID = s.showingID
        INNER JOIN movie m ON s.movieID = m.movieID
        INNER JOIN cinema c ON s.cinemaID = c.cinemaID
        WHERE b.bookingID = ? AND b.memberID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bookingID, $_SESSION['memberID']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: inquiry.php');
    exit;
}

$booking = $result->fetch_assoc();
$stmt->close();

// Check if booking can be refunded
if ($booking['bookingStatus'] !== 'confirmed') {
    $error = 'This booking cannot be refunded. Status: ' . $booking['bookingStatus'];
}

$showDateTime = strtotime($booking['showDate'] . ' ' . $booking['showTime']);
if ($showDateTime <= time()) {
    $error = 'Cannot refund past bookings.';
}

// Handle refund request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update booking status
        $updateBookingStmt = $conn->prepare("UPDATE bookingRecord SET bookingStatus = 'refunded', refundDate = NOW(), refundAmount = totalAmount WHERE bookingID = ?");
        $updateBookingStmt->bind_param("i", $bookingID);
        $updateBookingStmt->execute();
        $updateBookingStmt->close();
        
        // Release seats
        $updateSeatsStmt = $conn->prepare("UPDATE seatCondition SET seatStatus = 'available', bookingID = NULL WHERE bookingID = ?");
        $updateSeatsStmt->bind_param("i", $bookingID);
        $updateSeatsStmt->execute();
        $updateSeatsStmt->close();
        
        // Update showing available seats
        $updateShowingStmt = $conn->prepare("UPDATE showing SET availableSeats = availableSeats + ? WHERE showingID = ?");
        $updateShowingStmt->bind_param("ii", $booking['totalSeats'], $booking['showingID']);
        $updateShowingStmt->execute();
        $updateShowingStmt->close();
        
        // Refund to cash card
        $refundStmt = $conn->prepare("UPDATE memberCashCard SET balance = balance + ? WHERE memberID = ?");
        $refundStmt->bind_param("di", $booking['totalAmount'], $_SESSION['memberID']);
        $refundStmt->execute();
        $refundStmt->close();
        
        $conn->commit();
        
        // Redirect to inquiry page with success message
        header('Location: inquiry.php?refunded=1');
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Refund failed. Please try again or contact support.';
    }
}

// Get seat details
$seatQuery = "SELECT seatRow, seatNumber FROM seatCondition WHERE bookingID = ? ORDER BY seatRow, seatNumber";
$seatStmt = $conn->prepare($seatQuery);
$seatStmt->bind_param("i", $bookingID);
$seatStmt->execute();
$seatResult = $seatStmt->get_result();
$seatList = [];
while ($seat = $seatResult->fetch_assoc()) {
    $seatList[] = $seat['seatRow'] . $seat['seatNumber'];
}
$seatStmt->close();

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Refund - Cinema Ticketing System</title>
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
            <h2>Request Refund</h2>
            
            <?php if ($error): ?>
                <?php echo showError($error); ?>
                <a href="inquiry.php" class="btn btn-secondary">Back to My Bookings</a>
            <?php else: ?>
            
            <div class="card">
                <h3>Booking Details</h3>
                <p><strong>Booking ID:</strong> #<?php echo $booking['bookingID']; ?></p>
                <p><strong>Movie:</strong> <?php echo htmlspecialchars($booking['movieTitle']); ?></p>
                <p><strong>Cinema:</strong> <?php echo htmlspecialchars($booking['cinemaName']); ?></p>
                <p><strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($booking['showDate'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($booking['showTime'])); ?></p>
                <p><strong>Seats:</strong> <?php echo implode(', ', $seatList); ?></p>
                <p><strong>Total Seats:</strong> <?php echo $booking['totalSeats']; ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($booking['totalAmount'], 2); ?></p>
            </div>
            
            <div class="card" style="background: #fff3cd; border: 1px solid #ffc107;">
                <h3>⚠️ Refund Policy</h3>
                <ul style="padding-left: 2rem;">
                    <li>Refunds are only available for confirmed bookings</li>
                    <li>You can only request a refund before the show time</li>
                    <li>The full amount will be refunded to your cash card</li>
                    <li>Seats will be released and made available for other customers</li>
                    <li>This action cannot be undone</li>
                </ul>
            </div>
            
            <form method="POST" action="refund.php?bookingID=<?php echo $bookingID; ?>" onsubmit="return confirm('Are you sure you want to proceed with the refund? This action cannot be undone.');">
                <button type="submit" class="btn btn-danger">Confirm Refund ($<?php echo number_format($booking['totalAmount'], 2); ?>)</button>
                <a href="inquiry.php" class="btn btn-secondary">Cancel</a>
            </form>
            
            <?php endif; ?>
        </main>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Cinema Ticketing System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
