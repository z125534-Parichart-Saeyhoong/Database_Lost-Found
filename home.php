<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lost & Found</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- ======================= TOP BAR ======================= -->
    <div class="home-topbar">

        <?php if (!isset($_SESSION['userID'])): ?>
            <a href="#" class="signin" data-bs-toggle="modal" data-bs-target="#signinModal">
                Sign In
            </a>
            <a href="#" class="signup" data-bs-toggle="modal" data-bs-target="#signupModal">
                Sign Up
            </a>
        <?php else: ?>
            <span class="fw-bold">
                👋 <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-dark btn-sm">
                Logout
            </a>
        <?php endif; ?>

    </div>


    <!-- ======================= SIGN IN MODAL ======================= -->
    <div class="modal fade" id="signinModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Sign In</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="signinForm" action="signin.php" method="post">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">
                            Sign In
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </div>


    <!-- ======================= SIGN UP MODAL ======================= -->
    <div class="modal fade" id="signupModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Create your account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <form id="signupForm" action="signup.php" method="post">

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">
                            Create Account
                        </button>

                    </form>

                </div>
            </div>
        </div>
    </div>


    <!-- ======================= MAIN CONTENT ======================= -->
    <div class="content">

        <h1>Lost & Found</h1>

        <div class="search-box">
            <div class="input-group search-group">
                <input type="text" class="form-control" placeholder="Search lost items...">
                <button class="btn btn-dark">Search</button>
            </div>
        </div>

        <div class="filter-section">

            <div class="filter-item">
                <div class="icon-circle">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <span>search by location</span>
            </div>

            <div class="filter-item">
                <div class="icon-circle">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <span>search by category</span>
            </div>

            <div class="filter-item">
                <div class="icon-circle">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <span>search by time</span>
            </div>

        </div>

        <a href="report.php" class="report-btn">
            REPORT
        </a>

    </div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
