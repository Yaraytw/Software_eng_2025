<?php
require_once 'includes/functions.php';
requireLogin();

$error = '';
$success = '';
$currentBalance = 0;

// Get current balance
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT balance FROM memberCashCard WHERE memberID = ?");
$stmt->bind_param("i", $_SESSION['memberID']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentBalance = $row['balance'];
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'] ?? 0;
    
    if (!is_numeric($amount) || $amount <= 0) {
        $error = 'Please enter a valid amount.';
    } elseif ($amount > 10000) {
        $error = 'Maximum top-up amount is $10,000 per transaction.';
    } else {
        // Update balance
        $stmt = $conn->prepare("UPDATE memberCashCard SET balance = balance + ?, lastTopUpDate = NOW(), lastTopUpAmount = ? WHERE memberID = ?");
        $stmt->bind_param("ddi", $amount, $amount, $_SESSION['memberID']);
        
        if ($stmt->execute()) {
            $success = 'Successfully topped up $' . number_format($amount, 2);
            $currentBalance += $amount;
        } else {
            $error = 'Top-up failed. Please try again.';
        }
        
        $stmt->close();
    }
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top-up - Cinema Ticketing System</title>
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
            <h2>Top-up Cash Card</h2>
            
            <div class="card">
                <h3>Current Balance</h3>
                <p style="font-size: 2rem; color: #667eea; font-weight: bold;">
                    $<?php echo number_format($currentBalance, 2); ?>
                </p>
            </div>
            
            <?php if ($error): ?>
                <?php echo showError($error); ?>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <?php echo showSuccess($success); ?>
            <?php endif; ?>
            
            <form method="POST" action="topup.php">
                <div class="form-group">
                    <label for="amount">Top-up Amount ($):</label>
                    <input type="number" id="amount" name="amount" min="1" max="10000" step="0.01" required>
                    <small>Minimum: $1, Maximum: $10,000 per transaction</small>
                </div>
                
                <button type="submit" class="btn">Top-up Now</button>
            </form>
            
            <div class="card" style="margin-top: 2rem; background: #f8f9fa;">
                <h3>Quick Top-up Options</h3>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <button onclick="document.getElementById('amount').value='100'" class="btn btn-secondary">$100</button>
                    <button onclick="document.getElementById('amount').value='500'" class="btn btn-secondary">$500</button>
                    <button onclick="document.getElementById('amount').value='1000'" class="btn btn-secondary">$1,000</button>
                    <button onclick="document.getElementById('amount').value='2000'" class="btn btn-secondary">$2,000</button>
                </div>
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
