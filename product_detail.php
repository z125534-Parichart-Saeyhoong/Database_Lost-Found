<?php
session_start();
// =================================================================
// 1. Database Connection Settings (Update with your team's DB info)
// =================================================================
$servername = "localhost"; // Usually localhost
$username = "root";        // DB Username
$password = "";            // DB Password
$dbname = "LostandFound"; // Database Name

// Create DB connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection (Output message on error)
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =================================================================
// 2. Get Item ID from URL (e.g., product_detail.php?id=1)
// =================================================================
// Set default to 0 if ID is missing (Prevent errors)
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// =================================================================
// 3. Write SQL Query (JOIN query based on ER Diagram)
// =================================================================
// Join ITEM (Item Info), REPORT (Report Details), and USER (Reporter Info) tables.
$sql = "
    SELECT 
        I.name AS item_name,
        I.category,
        I.status,
        R.reportDate,
        R.location,
        R.detail,
        R.picture,
        U.username,
        U.tel,
        U.email
    FROM item I
    JOIN report R ON I.itemID = R.itemID
    JOIN User U ON I.userID = U.userID
    WHERE I.itemID = ?
";

// Use Prepared Statement for security (Prevent SQL Injection)
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $item_id); // "i" means integer
$stmt->execute();
$result = $stmt->get_result();

// Check if data exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc(); // Fetch data as an associative array
} else {
    // Alert if item is not found and go back
    echo "<script>alert('Item not found.'); history.back();</script>";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found - <?php echo htmlspecialchars($row['item_name']); ?></title>
    <style>
    /* Keep existing design style */
    :root {
        --primary-color: #2F80ED;
        --text-dark: #333;
        --text-grey: #666;
        --bg-light: #f9f9f9;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
        background-color: var(--bg-light);
        color: var(--text-dark);
    }

    header {
        background: white;
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    header h1 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
    }

    header .login-btn {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: bold;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .image-section {
        flex: 1;
        min-width: 300px;
    }

    .image-section img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        border: 1px solid #eee;
        object-fit: cover;
    }

    .info-section {
        flex: 1.2;
        min-width: 300px;
    }

    .status-badge {
        display: inline-block;
        background-color: var(--primary-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-weight: bold;
    }

    /* Logic for changing color based on status (Green for Found) */
    .status-badge.found {
        background-color: #27ae60;
    }

    .item-title {
        font-size: 32px;
        margin: 10px 0 20px 0;
        color: var(--text-dark);
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .info-table th {
        text-align: left;
        color: var(--text-grey);
        font-weight: normal;
        padding: 10px 0;
        width: 120px;
        border-bottom: 1px solid #eee;
    }

    .info-table td {
        text-align: left;
        padding: 10px 0;
        font-weight: 500;
        border-bottom: 1px solid #eee;
    }

    .description-box {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        color: var(--text-grey);
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 15px 30px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        font-weight: bold;
        flex: 1;
        text-align: center;
        transition: 0.3s;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: #1a6fd3;
    }

    .btn-secondary {
        background-color: #e2e6ea;
        color: var(--text-dark);
    }

    .btn-secondary:hover {
        background-color: #dbe0e5;
    }
    </style>
</head>

<body>

    <header>
        <h1>Lost & Found</h1>
        <div>
            <?php if (!isset($_SESSION['userID'])): ?>
            <a href="signin.php" class="login-btn">Login</a>
            <?php else: ?>
            <span>👋 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="login-btn">Logout</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">

        <div class="image-section">
            <?php 
                // Show placeholder image if no image exists in DB
                $img_src = !empty($row['picture']) ? "uploads/" . htmlspecialchars($row['picture']) : "https://via.placeholder.com/500x500?text=No+Image";
            ?>
            <img src="<?php echo $img_src; ?>" alt="Item Image">
        </div>

        <div class="info-section">

            <?php 
                // If status is 'Found', add .found class (green), otherwise default blue
                $status_class = (strtolower($row['status']) == 'found') ? 'status-badge found' : 'status-badge';
            ?>
            <span class="<?php echo $status_class; ?>">
                <?php echo htmlspecialchars($row['status']); ?>
            </span>

            <h2 class="item-title"><?php echo htmlspecialchars($row['item_name']); ?></h2>

            <table class="info-table">
                <tr>
                    <th>Category</th>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><?php echo htmlspecialchars($row['reportDate']); ?></td>
                </tr>
                <tr>
                    <th>Reporter</th>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td><?php echo htmlspecialchars($row['tel']); ?> / <?php echo htmlspecialchars($row['email']); ?>
                    </td>
                </tr>
            </table>

            <h3>Details</h3>
            <div class="description-box">
                <?php echo nl2br(htmlspecialchars($row['detail'])); ?>
            </div>

            <div class="action-buttons">
                <button class="btn btn-primary">Contact Reporter</button>
                <button class="btn btn-secondary" onclick="history.back()">Back to List</button>
            </div>
        </div>
    </div>

</body>

</html>
