<?php
session_start();

//if not login come -> Home
if (!isset($_SESSION['userID']))
{
    header("Location: home.php");
    exit();
}

//connect database
$conn = new mysqli("localhost", "root", "", "LostandFound");
if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

//data from database
$userID = $_SESSION['userID'];
$sql = "SELECT email, tel FROM User WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Report Item - Lost & Found</title>

    <link rel="stylesheet" href="style.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

    <div class="container">
        <div class="report-navbar">

            <a href="home.php" class="text-decoration-none text-black">
                <h1 class="fw-bold m-0">Lost & Found</h1>
            </a>

            <div class="d-flex align-items-center gap-3">
                <?php if (!isset($_SESSION['userID'])): ?>
                <a href="home.php" class="signin">Back to Home</a>
                <?php else: ?>
                <span class="fw-bold">
                    👋 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-dark btn-sm">Logout</a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <div class="container mb-5 mt-4">

        <div class="row justify-content-center">

            <div class="col-lg-10">
                <div class="p-4 p-md-5 rounded-3" style="background-color: #f8f9fa;">

                    <form action="submit_report_action.php" method="post" enctype="multipart/form-data">

                        <div class="row g-4">
                            <div class="col-md-6">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">What was Lost</label>
                                    <input type="text" name="item_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Category</label>
                                    <select name="category" class="form-select" required>
                                        <option value="">-- Select category --</option>
                                        <option value="Electronics">Electronics</option>
                                        <option value="Wallet">Wallet</option>
                                        <option value="Bag">Bag</option>
                                        <option value="Clothing">Clothing</option>
                                        <option value="Document">Document</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Where</label>
                                    <input type="text" name="location" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">When</label>
                                    <input type="date" name="reportDate" class="form-control" required>
                                </div>

                                <div class="mt-4 mb-2">
                                    <label class="form-label fw-bold">Person who found</label>
                                </div>

                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-3 col-form-label">tel</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-plaintext">
                                            <?php echo htmlspecialchars($user['tel']); ?>
                                        </div>

                                        <input type="hidden" name="contact_tel"
                                            value="<?php echo htmlspecialchars($user['tel']); ?>">
                                    </div>
                                </div>

                                <div class="mb-3 row align-items-center">
                                    <label class="col-sm-3 col-form-label">email</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-plaintext">
                                            <?php echo htmlspecialchars($user['email']); ?>
                                        </div>

                                        <input type="hidden" name="contact_email"
                                            value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 d-flex flex-column">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Picture</label>
                                    <label class="d-block w-100 p-5 text-center border rounded-3 cursor-pointer"
                                        style="background-color: #e9ecef; border: 2px dashed #ced4da; cursor: pointer;">
                                        <i class="fa-solid fa-arrow-up-from-bracket fs-1 text-secondary mb-2"></i>
                                        <div class="bg-white rounded-pill mx-auto mt-2"
                                            style="width: 100px; height: 10px;"></div>
                                        <input type="file" name="picture" class="d-none" accept="image/*" required>


                                    </label>
                                </div>

                                <div class="mb-3 flex-grow-1 d-flex flex-column">
                                    <label class="form-label fw-bold">Details</label>
                                    <textarea name="detail" class="form-control flex-grow-1" rows="4"
                                        style="resize: none;"></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-dark px-5 py-2 fw-bold fs-5" style="min-width: 200px;">
                                Submit
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.querySelector('input[type="file"]').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const icon = this.previousElementSibling.previousElementSibling;
            icon.className = "fa-solid fa-check fs-1 text-success mb-2";
        }
    });
    </script>
</body>

</html>
