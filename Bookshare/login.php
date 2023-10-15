<?php
session_start(); // Start the session

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if user exists in Readers table
    $checkReaderQuery = "SELECT * FROM Readers WHERE username='$username'";
    $readerResult = $conn->query($checkReaderQuery);

    // Check if user exists in Authors table
    $checkAuthorQuery = "SELECT * FROM Authors WHERE username='$username'";
    $authorResult = $conn->query($checkAuthorQuery);

    if ($readerResult->num_rows > 0) {
        // Reader login successful
        $readerRow = $readerResult->fetch_assoc();
        if (password_verify($password, $readerRow["password"])) {
            $_SESSION["user_type"] = "reader";
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] = $readerRow["readerid"]; // Set user_id in the session

            // Set cookies
            setcookie("user_type", "reader", time() + (86400 * 30), "/");
            setcookie("username", $username, time() + (86400 * 30), "/");
            setcookie("user_id", $readerRow["readerid"], time() + (86400 * 30), "/"); // Set user_id in cookies

            header("Location: all_books.php"); // Redirect to all_books.php
            exit();
        } else {
            $loginError = true; // Flag to show login error message
        }
    } elseif ($authorResult->num_rows > 0) {
        // Author login successful
        $authorRow = $authorResult->fetch_assoc();
        if (password_verify($password, $authorRow["password"])) {
            $_SESSION["user_type"] = "author";
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] = $authorRow["authorid"]; // Set user_id in the session

            // Set cookies
            setcookie("user_type", "author", time() + (86400 * 30), "/");
            setcookie("username", $username, time() + (86400 * 30), "/");
            setcookie("user_id", $authorRow["authorid"], time() + (86400 * 30), "/"); // Set user_id in cookies

            header("Location: all_books.php"); // Redirect to all_books.php
            exit();
        } else {
            $loginError = true; // Flag to show login error message
        }
    } else {
        $loginError = true; // Flag to show login error message
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন - বুকশেয়ার</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="stylesheet/styles.css">
</head>
<body style="background-color: #000; color: #fff;" class="bd-dark text-white">
    <!-- Bootstrap Navbar -->
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark nav-tabs" style="background-color: black !important;">
        <a class="navbar-brand book-details" href="all_books.php" style="padding: 0;">
            <span style="display: inline-block; width: 150px; height: 40px; background: url('cover.png') no-repeat center center; background-size: cover; border-radius: 90px; animation: spin-horizontal 10s linear infinite;"></span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                            <img src="register.png" alt="Register Icon" style="width: 30px; height: 25px;">
                    </a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="login.php">
                            <img src="login.png" alt="Login Icon" style="width: 30px; height: 30px;">
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Login Form -->
    <div class="container mt-4">
    <h2>প্রবেশ করুন</h2>
    <?php if (isset($loginError) && $loginError) : ?>
        <div class="alert alert-danger">অবৈধ লগইন শংসাপত্রের</div>
    <?php endif; ?>
    <div class="form-container"> <!-- Add this div container -->
        <form method="post" action="">
            <div class="form-group">
                <label for="username">ব্যবহারকারীর নাম:</label>
                <input type="text" class="form-control book-details" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">পাসওয়ার্ড:</label>
                <input type="password" class="form-control book-details" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn book-details btn-primary ">প্রবেশ করুন</button>
        </form>
    </div>
</div>

    <!-- Footer -->
    <footer class="footer fixed-bottom underlineEffects text-center" style="background-color: black !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-inline">
                        <li class="list-inline-item" onmouseover="changeColor(this, '#ffcc00');" onmouseout="changeColor(this, '');">
                            <a href="https://www.facebook.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-facebook"></i> <!-- Facebook Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item" onmouseover="changeColor(this, '#00ffcc');" onmouseout="changeColor(this, '');">
                            <a href="https://www.linkedin.com/in/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-linkedin"></i> <!-- LinkedIn Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item" onmouseover="changeColor(this, '#cc00ff');" onmouseout="changeColor(this, '');">
                            <a href="https://github.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-github"></i> <!-- GitHub Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item" onmouseover="changeColor(this, '#ff6600');" onmouseout="changeColor(this, '');">
                            <a href="https://twitter.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-twitter"></i> <!-- Twitter Icon -->
                            </a>
                        </li>
                    </ul>
                    <ul>
                        <li class="list-inline-item" onmouseover="changeColor(this, '#ff3333');" onmouseout="changeColor(this, '');">
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
        function changeColor(element, color) {
            element.querySelector('i').style.color = color;
        }
        function getRandomColor() {
        // Generate a random color in hexadecimal format
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
        // Trigger the fade-in animation for the login form
        document.addEventListener("DOMContentLoaded", function () {
            const formContainer = document.querySelector(".form-container");
            formContainer.classList.add("active");
        });
    </script>
    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
