<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "lost_and_found");

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Handle CREATE
if (isset($_POST['add'])) {
  $name = $_POST['name'];
  $category = $_POST['category'];
  $location = $_POST['location'];
  $date = $_POST['date_found'];
  $status = $_POST['status'];
  
  // Insert into item table (userID 1 as default admin)
  mysqli_query($conn, "INSERT INTO item (userID, name, category, status) VALUES (1, '$name', '$category', '$status')");
  $itemID = mysqli_insert_id($conn);
  
  // Insert into report table
  mysqli_query($conn, "INSERT INTO report (userID, itemID, reportType, reportDate, location, picture, detail) 
                       VALUES (1, $itemID, '$status', '$date', '$location', '', '')");
  
  header("Location: admin-static.php");
  exit();
}

// Handle UPDATE
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $category = $_POST['category'];
  $location = $_POST['location'];
  $date = $_POST['date_found'];
  $status = $_POST['status'];
  
  mysqli_query($conn, "UPDATE item SET name='$name', category='$category', status='$status' WHERE itemID=$id");
  mysqli_query($conn, "UPDATE report SET location='$location', reportDate='$date', reportType='$status' WHERE itemID=$id");
  
  header("Location: admin-static.php");
  exit();
}

// Handle DELETE
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  mysqli_query($conn, "DELETE FROM report WHERE itemID=$id");
  mysqli_query($conn, "DELETE FROM handover WHERE itemID=$id");
  mysqli_query($conn, "DELETE FROM item WHERE itemID=$id");
  header("Location: admin-static.php");
  exit();
}

// Handle RETURN
if (isset($_GET['return'])) {
  $id = $_GET['return'];
  mysqli_query($conn, "UPDATE item SET status='Returned' WHERE itemID=$id");
  header("Location: admin-static.php");
  exit();
}

// Get item for editing
$edit_item = null;
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $edit_query = "SELECT item.*, report.location, report.reportDate 
                 FROM item 
                 LEFT JOIN report ON item.itemID = report.itemID 
                 WHERE item.itemID=$id";
  $edit_result = mysqli_query($conn, $edit_query);
  $edit_item = mysqli_fetch_assoc($edit_result);
}

// Get all items
$query = "SELECT item.itemID, item.name, item.category, item.status, 
                 report.reportDate, report.location,
                 user.username
          FROM item
          LEFT JOIN report ON item.itemID = report.itemID
          LEFT JOIN user ON item.userID = user.userID
          ORDER BY item.itemID DESC";
$result = mysqli_query($conn, $query);

// Count stats
$total = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM item"));
$lost = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM item WHERE status='Lost'"));
$found = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM item WHERE status='Found'"));
$returned = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM item WHERE status='Returned'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lost & Found Admin</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f9fafb; min-height: 100vh; padding: 24px; }
    .container { max-width: 1200px; margin: 0 auto; }
    .header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; }
    .header-icon { background-color: #2563eb; color: white; width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .header h1 { font-size: 24px; color: #1f2937; }
    .header p { color: #6b7280; font-size: 14px; }
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; }
    .stat-card p { color: #6b7280; font-size: 14px; margin-bottom: 4px; }
    .stat-card .number { font-size: 28px; font-weight: 700; }
    .stat-card .number.total { color: #1f2937; }
    .stat-card .number.lost { color: #dc2626; }
    .stat-card .number.found { color: #d97706; }
    .stat-card .number.returned { color: #16a34a; }
    
    .form-container { background: white; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 24px; }
    .form-container h2 { font-size: 16px; color: #1f2937; margin-bottom: 16px; }
    .form-row { display: flex; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
    .form-group { flex: 1; min-width: 150px; }
    .form-group label { display: block; font-size: 13px; color: #4b5563; margin-bottom: 6px; }
    .form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; }
    .form-group input:focus, .form-group select:focus { border-color: #2563eb; }
    .form-buttons { margin-top: 16px; }
    .btn-submit { padding: 10px 20px; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; }
    .btn-submit:hover { background-color: #1d4ed8; }
    .btn-cancel { padding: 10px 20px; background-color: #6b7280; color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; text-decoration: none; margin-left: 10px; }
    
    .table-container { background: white; border-radius: 8px; border: 1px solid #e5e7eb; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    thead { background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    th { padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #4b5563; }
    td { padding: 12px 16px; font-size: 14px; color: #374151; }
    tr:nth-child(even) { background-color: #f9fafb; }
    .item-name { font-weight: 500; color: #111827; }
    .item-id { color: #9ca3af; }
    .status { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
    .status.Lost { background-color: #fee2e2; color: #dc2626; }
    .status.Found { background-color: #fef3c7; color: #b45309; }
    .status.Returned { background-color: #dcfce7; color: #15803d; }
    .actions { display: flex; gap: 8px; }
    .btn { display: inline-flex; align-items: center; padding: 6px 12px; border: none; border-radius: 6px; font-size: 12px; font-weight: 500; text-decoration: none; }
    .btn-return { background-color: #16a34a; color: white; }
    .btn-edit { background-color: #f59e0b; color: white; }
    .btn-delete { background-color: #ef4444; color: white; }
    .empty { text-align: center; padding: 40px; color: #6b7280; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="header-icon">📦</div>
      <div>
        <h1>Lost & Found Admin</h1>
        <p>Manage items and returns</p>
      </div>
    </div>

    <div class="stats">
      <div class="stat-card"><p>Total Items</p><div class="number total"><?php echo $total; ?></div></div>
      <div class="stat-card"><p>Lost</p><div class="number lost"><?php echo $lost; ?></div></div>
      <div class="stat-card"><p>Found</p><div class="number found"><?php echo $found; ?></div></div>
      <div class="stat-card"><p>Returned</p><div class="number returned"><?php echo $returned; ?></div></div>
    </div>

    <!-- Add/Edit Form -->
    <div class="form-container">
      <?php if ($edit_item): ?>
        <h2>✏️ Edit Item</h2>
        <form method="POST">
          <input type="hidden" name="id" value="<?php echo $edit_item['itemID']; ?>">
          <div class="form-row">
            <div class="form-group">
              <label>Item Name</label>
              <input type="text" name="name" value="<?php echo $edit_item['name']; ?>" required>
            </div>
            <div class="form-group">
              <label>Category</label>
              <input type="text" name="category" value="<?php echo $edit_item['category']; ?>" required>
            </div>
            <div class="form-group">
              <label>Location</label>
              <input type="text" name="location" value="<?php echo $edit_item['location']; ?>" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Date</label>
              <input type="date" name="date_found" value="<?php echo $edit_item['reportDate']; ?>" required>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status">
                <option value="Lost" <?php if($edit_item['status'] == 'Lost') echo 'selected'; ?>>Lost</option>
                <option value="Found" <?php if($edit_item['status'] == 'Found') echo 'selected'; ?>>Found</option>
                <option value="Returned" <?php if($edit_item['status'] == 'Returned') echo 'selected'; ?>>Returned</option>
              </select>
            </div>
          </div>
          <div class="form-buttons">
            <button type="submit" name="update" class="btn-submit">Update Item</button>
            <a href="admin-static.php" class="btn-cancel">Cancel</a>
          </div>
        </form>
      <?php else: ?>
        <h2>➕ Add New Item</h2>
        <form method="POST">
          <div class="form-row">
            <div class="form-group">
              <label>Item Name</label>
              <input type="text" name="name" placeholder="e.g. iPhone 15" required>
            </div>
            <div class="form-group">
              <label>Category</label>
              <input type="text" name="category" placeholder="e.g. Electronics" required>
            </div>
            <div class="form-group">
              <label>Location</label>
              <input type="text" name="location" placeholder="e.g. Shibuya Station" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Date</label>
              <input type="date" name="date_found" required>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select name="status">
                <option value="Lost">Lost</option>
                <option value="Found">Found</option>
              </select>
            </div>
          </div>
          <div class="form-buttons">
            <button type="submit" name="add" class="btn-submit">Add Item</button>
          </div>
        </form>
      <?php endif; ?>
    </div>

    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Category</th>
            <th>Location</th>
            <th>Date</th>
            <th>Reported By</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td class="item-id">#<?php echo $row['itemID']; ?></td>
              <td class="item-name"><?php echo $row['name']; ?></td>
              <td><?php echo $row['category']; ?></td>
              <td><?php echo $row['location'] ?? '-'; ?></td>
              <td><?php echo $row['reportDate'] ?? '-'; ?></td>
              <td><?php echo $row['username'] ?? '-'; ?></td>
              <td><span class="status <?php echo $row['status']; ?>"><?php echo $row['status']; ?></span></td>
              <td>
                <div class="actions">
                  <?php if ($row['status'] != 'Returned') { ?>
                    <a href="?return=<?php echo $row['itemID']; ?>" class="btn btn-return" onclick="return confirm('Mark as returned?')">✓ Return</a>
                  <?php } ?>
                  <a href="?edit=<?php echo $row['itemID']; ?>" class="btn btn-edit">✎ Edit</a>
                  <a href="?delete=<?php echo $row['itemID']; ?>" class="btn btn-delete" onclick="return confirm('Delete this item?')">✕ Delete</a>
                </div>
              </td>
            </tr>
            <?php } ?>
          <?php else: ?>
            <tr><td colspan="8" class="empty">No items found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
<?php mysqli_close($conn); ?>