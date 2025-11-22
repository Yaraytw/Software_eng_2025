<?php
require_once 'includes/functions.php';
requireLogin();

$showingID = $_GET['showingID'] ?? null;
$error = '';
$success = '';

if (!$showingID) {
    header('Location: select_cinema.php');
    exit;
}

// Get showing info
$conn = getDBConnection();
$sql = "SELECT s.*, m.movieTitle, c.cinemaName, t.theaterName, t.totalSeats, t.theaterType
        FROM showing s
        INNER JOIN movie m ON s.movieID = m.movieID
        INNER JOIN cinema c ON s.cinemaID = c.cinemaID
        INNER JOIN theater t ON s.theaterID = t.theaterID
        WHERE s.showingID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $showingID);
$stmt->execute();
$showing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$showing) {
    header('Location: select_cinema.php');
    exit;
}

// Generate seats if not exist
$checkSeats = $conn->prepare("SELECT COUNT(*) as count FROM seatCondition WHERE showingID = ?");
$checkSeats->bind_param("i", $showingID);
$checkSeats->execute();
$seatCount = $checkSeats->get_result()->fetch_assoc()['count'];
$checkSeats->close();

if ($seatCount == 0) {
    // Generate seats (10 seats per row)
    $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    $seatsPerRow = 10;
    $totalSeats = $showing['totalSeats'];
    
    $insertStmt = $conn->prepare("INSERT INTO seatCondition (showingID, seatRow, seatNumber, seatStatus) VALUES (?, ?, ?, 'available')");
    
    $seatsCreated = 0;
    for ($r = 0; $r < count($rows) && $seatsCreated < $totalSeats; $r++) {
        $seatsInThisRow = min($seatsPerRow, $totalSeats - $seatsCreated);
        for ($s = 1; $s <= $seatsInThisRow; $s++) {
            $insertStmt->bind_param("isi", $showingID, $rows[$r], $s);
            $insertStmt->execute();
            $seatsCreated++;
        }
    }
    $insertStmt->close();
}

// Handle seat booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedSeats = $_POST['seats'] ?? [];
    
    if (empty($selectedSeats)) {
        $error = 'Please select at least one seat.';
    } else {
        // Check balance
        $balanceStmt = $conn->prepare("SELECT balance FROM memberCashCard WHERE memberID = ?");
        $balanceStmt->bind_param("i", $_SESSION['memberID']);
        $balanceStmt->execute();
        $balance = $balanceStmt->get_result()->fetch_assoc()['balance'];
        $balanceStmt->close();
        
        $totalAmount = count($selectedSeats) * $showing['price'];
        
        if ($balance < $totalAmount) {
            $error = 'Insufficient balance. Please top-up your cash card. Required: $' . number_format($totalAmount, 2) . ', Available: $' . number_format($balance, 2);
        } else {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Create booking record
                $bookingStmt = $conn->prepare("INSERT INTO bookingRecord (memberID, showingID, totalSeats, totalAmount, paymentMethod, bookingStatus) VALUES (?, ?, ?, ?, 'cash_card', 'confirmed')");
                $totalSeats = count($selectedSeats);
                $bookingStmt->bind_param("iiid", $_SESSION['memberID'], $showingID, $totalSeats, $totalAmount);
                $bookingStmt->execute();
                $bookingID = $conn->insert_id;
                $bookingStmt->close();
                
                // Update seats
                $updateSeatStmt = $conn->prepare("UPDATE seatCondition SET seatStatus = 'sold', bookingID = ? WHERE showingID = ? AND seatID = ? AND seatStatus = 'available'");
                $seatsUpdated = 0;
                foreach ($selectedSeats as $seatID) {
                    $updateSeatStmt->bind_param("iii", $bookingID, $showingID, $seatID);
                    $updateSeatStmt->execute();
                    $seatsUpdated += $updateSeatStmt->affected_rows;
                }
                $updateSeatStmt->close();
                
                // Verify all seats were successfully reserved
                if ($seatsUpdated !== count($selectedSeats)) {
                    throw new Exception('Some seats are no longer available');
                }
                
                // Update showing available seats
                $updateShowingStmt = $conn->prepare("UPDATE showing SET availableSeats = availableSeats - ? WHERE showingID = ?");
                $updateShowingStmt->bind_param("ii", $totalSeats, $showingID);
                $updateShowingStmt->execute();
                $updateShowingStmt->close();
                
                // Deduct from cash card
                $deductStmt = $conn->prepare("UPDATE memberCashCard SET balance = balance - ? WHERE memberID = ?");
                $deductStmt->bind_param("di", $totalAmount, $_SESSION['memberID']);
                $deductStmt->execute();
                $deductStmt->close();
                
                $conn->commit();
                
                // Redirect to inquiry page
                header('Location: inquiry.php?success=1&bookingID=' . $bookingID);
                exit;
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Booking failed. Please try again.';
            }
        }
    }
}

// Get seats for this showing
$seatsQuery = "SELECT * FROM seatCondition WHERE showingID = ? ORDER BY seatRow, seatNumber";
$seatsStmt = $conn->prepare($seatsQuery);
$seatsStmt->bind_param("i", $showingID);
$seatsStmt->execute();
$seatsResult = $seatsStmt->get_result();
$seats = [];
while ($seat = $seatsResult->fetch_assoc()) {
    $seats[] = $seat;
}
$seatsStmt->close();

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - Cinema Ticketing System</title>
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
            <h2>Select Seats</h2>
            
            <div class="card">
                <h3>Booking Details</h3>
                <p><strong>Cinema:</strong> <?php echo htmlspecialchars($showing['cinemaName']); ?></p>
                <p><strong>Movie:</strong> <?php echo htmlspecialchars($showing['movieTitle']); ?></p>
                <p><strong>Theater:</strong> <?php echo htmlspecialchars($showing['theaterName']); ?> (<?php echo ucfirst($showing['theaterType']); ?>)</p>
                <p><strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($showing['showDate'])); ?></p>
                <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($showing['showTime'])); ?></p>
                <p><strong>Price per seat:</strong> $<?php echo number_format($showing['price'], 2); ?></p>
            </div>
            
            <?php if ($error): ?>
                <?php echo showError($error); ?>
            <?php endif; ?>
            
            <div class="seat-legend">
                <div class="seat-legend-item">
                    <div class="seat-legend-color" style="background: #28a745;"></div>
                    <span>Available</span>
                </div>
                <div class="seat-legend-item">
                    <div class="seat-legend-color" style="background: #667eea;"></div>
                    <span>Selected</span>
                </div>
                <div class="seat-legend-item">
                    <div class="seat-legend-color" style="background: #dc3545;"></div>
                    <span>Sold</span>
                </div>
            </div>
            
            <form method="POST" action="select_seat.php?showingID=<?php echo $showingID; ?>" id="seatForm">
                <div style="text-align: center; margin: 2rem 0; padding: 1rem; background: #333; color: white; border-radius: 8px;">
                    SCREEN
                </div>
                
                <div class="seat-grid">
                    <?php foreach ($seats as $seat): ?>
                        <?php
                        $seatClass = 'seat';
                        $disabled = '';
                        if ($seat['seatStatus'] === 'sold') {
                            $seatClass .= ' sold';
                            $disabled = 'disabled';
                        } else {
                            $seatClass .= ' available';
                        }
                        ?>
                        <label class="<?php echo $seatClass; ?>" id="seat-<?php echo $seat['seatID']; ?>">
                            <input type="checkbox" name="seats[]" value="<?php echo $seat['seatID']; ?>" 
                                   style="display: none;" <?php echo $disabled; ?> 
                                   onchange="toggleSeat(<?php echo $seat['seatID']; ?>)">
                            <?php echo $seat['seatRow'] . $seat['seatNumber']; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <div class="card">
                    <h3>Booking Summary</h3>
                    <p><strong>Selected Seats:</strong> <span id="selectedSeatsText">None</span></p>
                    <p><strong>Total Amount:</strong> $<span id="totalAmount">0.00</span></p>
                    <button type="submit" class="btn" id="bookButton" disabled>Confirm Booking</button>
                </div>
            </form>
            
            <div style="margin-top: 2rem;">
                <a href="select_session.php?cinemaID=<?php echo $showing['cinemaID']; ?>&movieID=<?php echo $showing['movieID']; ?>" class="btn btn-secondary">← Change Session</a>
            </div>
        </main>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2025 Cinema Ticketing System. All rights reserved.</p>
        </div>
    </footer>
    
    <script>
        const pricePerSeat = <?php echo $showing['price']; ?>;
        let selectedSeats = [];
        
        function toggleSeat(seatID) {
            const seatElement = document.getElementById('seat-' + seatID);
            const checkbox = seatElement.querySelector('input[type="checkbox"]');
            
            if (checkbox.checked) {
                seatElement.classList.add('selected');
                seatElement.classList.remove('available');
                selectedSeats.push(seatID);
            } else {
                seatElement.classList.remove('selected');
                seatElement.classList.add('available');
                selectedSeats = selectedSeats.filter(id => id !== seatID);
            }
            
            updateSummary();
        }
        
        function updateSummary() {
            const count = selectedSeats.length;
            const total = count * pricePerSeat;
            
            document.getElementById('selectedSeatsText').textContent = count > 0 ? count + ' seat(s)' : 'None';
            document.getElementById('totalAmount').textContent = total.toFixed(2);
            document.getElementById('bookButton').disabled = count === 0;
        }
    </script>
</body>
</html>
