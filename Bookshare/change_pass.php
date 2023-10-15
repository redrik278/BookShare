<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookshare";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in, and if not, redirect to the login page
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Initialize alert variables
$alertMessage = "";
$alertType = "";

// Handle password change form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve user input
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmNewPassword = $_POST["confirm_new_password"];

    // Fetch the user's current password hash from the database based on their role (reader or author)
    $username = $_SESSION["username"];
    $userType = $_SESSION["user_type"];

    $table = ($userType === "reader") ? "Readers" : "Authors";

    $fetchPasswordQuery = "SELECT password FROM $table WHERE username = '$username'";
    $result = $conn->query($fetchPasswordQuery);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $currentPasswordHash = $row["password"];

        // Verify the current password
        if (password_verify($currentPassword, $currentPasswordHash)) {
            // Passwords match, now validate and change the new password
            if ($newPassword === $confirmNewPassword) {
                // Hash the new password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the user's password in the database
                $updatePasswordQuery = "UPDATE $table SET password = '$hashedNewPassword' WHERE username = '$username'";
                if ($conn->query($updatePasswordQuery) === TRUE) {
                    // Password successfully changed
                    $alertType = "success";
                    $alertMessage = "Password changed successfully.";
                } else {
                    // Error updating password
                    $alertType = "danger";
                    $alertMessage = "Error updating password: " . $conn->error;
                }
            } else {
                // New passwords do not match
                $alertType = "danger";
                $alertMessage = "New passwords do not match.";
            }
        } else {
            // Current password is incorrect
            $alertType = "danger";
            $alertMessage = "Current password is incorrect.";
        }
    } else {
        // User not found
        $alertType = "danger";
        $alertMessage = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>পাসওয়ার্ড পরিবর্তন করুন</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="stylesheet/styles.css">
</head>
<body style="background-color: #000; color: #fff;" class="bd-dark text-white">
<nav class="navbar fixed-top navbar-expand-lg navbar-dark nav-tabs" style="background-color: black !important;">
        <a class="navbar-brand book-details" href="all_books.php" style="padding: 0;">
            <span style="display: inline-block; width: 150px; height: 40px; background: url('cover.png') no-repeat center center; background-size: cover; border-radius: 90px; animation: spin-horizontal 10s linear infinite;"></span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse badge text-bg-primary" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item" onmouseover="this.style.color=getRandomColor();" onmouseout="this.style.color='';">
                    <a class="nav-link" href="all_books.php">
                        <img src="books.png" alt="books Icon" style="width: 30px; height: 25px;">
                    </a>
                </li>
                <li class="nav-item" onmouseover="this.style.color=getRandomColor();" onmouseout="this.style.color='';">
                    <a class="nav-link" href="change_pass.php">
                        <img src="reset-password.png" alt="password Icon" style="width: 30px; height: 25px;">
                    </a>
                </li>
                <?php if (isset($_SESSION["username"])): ?>
                    <li class="nav-item" onmouseover="this.style.color=getRandomColor();" onmouseout="this.style.color='';">
                        <a class="nav-link" href="logout.php">
                            <img src="logout.png" alt="logout Icon" style="width: 30px; height: 25px;">
                        </a>
                    </li>
                    <?php if ($_SESSION["user_type"] === "reader"): ?>
                        <li class="nav-item" onmouseover="this.style.color=getRandomColor();" onmouseout="this.style.color='';">
                            <a class="nav-link" href="notification.php">
                                <img src="notify.png" alt="Notification Icon" style="width: 30px; height: 25px;">
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item" onmouseover="this.style.color=getRandomColor();" onmouseout="this.style.color='';">
                        <a class="nav-link" href="cart.php">
                            <img src="cart.png" alt="cart Icon" style="width: 30px; height: 25px;">
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row justify-content-center " style="background-color: black !important;>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center text-dark">পাসওয়ার্ড পরিবর্তন করুন</h1>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($alertMessage)): ?>
                            <div class="alert alert-<?php echo $alertType; ?>"><?php echo $alertMessage; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                                <label class="text-dark" for="current_password">বর্তমান পাসওয়ার্ড:</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label class="text-dark" for="new_password">নতুন পাসওয়ার্ড:</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label class="text-dark" for="confirm_new_password">নিশ্চিত কর নতুন পাসওয়ার্ড:</label>
                                <input type="password" class="form-control" name="confirm_new_password" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary underlineEffects">পাসওয়ার্ড পরিবর্তন করুন</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer  fixed-bottom  underlineEffects text-center" style="background-color: black !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-inline">
                        <li class="list-inline-item underlineEffects" onmouseover="changeColor(this, '#ffcc00');" onmouseout="changeColor(this, '');">
                            <a href="https://www.facebook.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-facebook"></i> <!-- Facebook Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item underlineEffects " onmouseover="changeColor(this, '#00ffcc');" onmouseout="changeColor(this, '');">
                            <a href="https://www.linkedin.com/in/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-linkedin"></i> <!-- LinkedIn Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item underlineEffects" onmouseover="changeColor(this, '#cc00ff');" onmouseout="changeColor(this, '');">
                            <a href="https://github.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-github"></i> <!-- GitHub Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item underlineEffects" onmouseover="changeColor(this, '#ff6600');" onmouseout="changeColor(this, '');">
                            <a href="https://twitter.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-twitter"></i> <!-- Twitter Icon -->
                            </a>
                        </li>
                    </ul>
                    <ul>
                        <li class="list-inline-item underlineEffects" onmouseover="changeColor(this, '#ff3333');" onmouseout="changeColor(this, '');">
                            <a href="chatbot.php"  class="btn btn-outline-primary border-0 text-white">
                                <i class="fas fa-question-circle"></i> <!-- Question Circle Icon -->
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <script>
        // init
var maxx = document.body.clientWidth;
var maxy = document.body.clientHeight;
var halfx = maxx / 2;
var halfy = maxy / 2;
var canvas = document.createElement("canvas");
document.body.appendChild(canvas);
canvas.width = maxx;
canvas.height = maxy;
var context = canvas.getContext("2d");
var dotCount = 200;
var dots = [];
// create dots
for (var i = 0; i < dotCount; i++) {
  dots.push(new dot());
}

// dots animation
function render() {
  context.fillStyle = "#000000";
  context.fillRect(0, 0, maxx, maxy);
  for (var i = 0; i < dotCount; i++) {
    dots[i].draw();
    dots[i].move();
  }
  requestAnimationFrame(render);
}

// dots class
// @constructor
function dot() {
  
  this.rad_x = 2 * Math.random() * halfx + 1;
  this.rad_y = 1.2 * Math.random() * halfy + 1;
  this.alpha = Math.random() * 360 + 1;
  this.speed = Math.random() * 100 < 50 ? 1 : -1;
  this.speed *= 0.1;
  this.size = Math.random() * 5 + 1;
  this.color = Math.floor(Math.random() * 256);
  
}

// drawing dot
dot.prototype.draw = function() {
  
  // calc polar coord to decart
  var dx = halfx + this.rad_x * Math.cos(this.alpha / 180 * Math.PI);
  var dy = halfy + this.rad_y * Math.sin(this.alpha / 180 * Math.PI);
  // set color
  context.fillStyle = "rgb(" + this.color + "," + this.color + "," + this.color + ")";
  // draw dot
  context.fillRect(dx, dy, this.size, this.size);
  
};

// calc new position in polar coord
dot.prototype.move = function() {
  
  this.alpha += this.speed;
  // change color
  if (Math.random() * 100 < 50) {
    this.color += 1;
  } else {
    this.color -= 1;
  }
  
};

// start animation
render();
    </script>
    <!-- Add Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
