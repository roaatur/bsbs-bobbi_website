<?php
// Connect to the database
$conn = new mysqli('localhost:3307', 'root', '', 'bsbs_and_bobbie');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------- Functions ---------------- //
function generateVisitorID() {
    return uniqid('visitor_', true);
}

function setVisitorIDCookie() {
    if (!isset($_COOKIE['visitorID'])) {
        $visitorID = generateVisitorID();
        setcookie('visitorID', $visitorID, time() + (30 * 24 * 60 * 60), "/");
    }
}

function getVisitorID() {
    return $_COOKIE['visitorID'] ?? null;
}

function setPurchaseHistoryCookie($purchaseData) {
    $purchaseHistory = json_encode($purchaseData);
    setcookie('purchaseHistory', $purchaseHistory, time() + (30 * 24 * 60 * 60), "/");
}

function getPurchaseHistory() {
    if (isset($_COOKIE['purchaseHistory'])) {
        return json_decode($_COOKIE['purchaseHistory'], true);
    }
    return [];
}

// Prepare visitor data
setVisitorIDCookie();
$visitorID = getVisitorID();
$purchaseHistory = getPurchaseHistory();

// Fetch new products
$newPurchases = [];
$result = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 3");

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $newPurchases[] = [
            'name' => $row['name'],
            'price' => $row['price'],
            'quantity' => 1
        ];
    }
}

// Update the cookie
$updatedHistory = array_merge($purchaseHistory, $newPurchases);
setPurchaseHistoryCookie($updatedHistory);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Purchases - BSBS & Bobbi</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php require 'include/header.php'; ?>

<main>
    <div class="container">
        <h2>Welcome!</h2>
        <?php if ($visitorID): ?>
            <p>We recognize you by your unique ID: <strong><?php echo htmlspecialchars($visitorID); ?></strong></p>
        <?php else: ?>
            <p>Hello! This is your first visit.</p>
        <?php endif; ?>

        <?php if (!empty($purchaseHistory)): ?>
            <h3>Your Previous Purchases:</h3>
            <?php foreach ($purchaseHistory as $purchase): ?>
                <div class="purchase">
                    <p>Product: <?php echo htmlspecialchars($purchase['name']); ?></p>
                    <p>Quantity: <?php echo htmlspecialchars($purchase['quantity']); ?></p>
                    <p>Price: $<?php echo htmlspecialchars($purchase['price']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No previous purchases found.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2025 BSBS & Bobbi PetFood Store. All rights reserved.</p>
</footer>

<script src="script.js"></script>
</body>
</html>
