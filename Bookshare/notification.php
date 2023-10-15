<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookshare";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_SESSION["username"])) {
    // Get the user's ID using their username from the session
    $username = $_SESSION["username"];
    $userQuery = "SELECT readerid FROM Readers WHERE username = '$username'";
    $userResult = $conn->query($userQuery);

    if ($userResult && $userResult->num_rows > 0) {
        $userDetails = $userResult->fetch_assoc();
        $userid = $userDetails["readerid"];
        
        // Now, use the user's ID to retrieve the request information
        $requestQuery = "SELECT BookRequest.*, Books.title AS book_title, Books.file_location
                         FROM BookRequest
                         JOIN Books ON BookRequest.bookid = Books.bookid
                         WHERE readerid = $userid";
        $requestResult = $conn->query($requestQuery);

        if ($requestResult && $requestResult->num_rows > 0) {
            $requestDetails = $requestResult->fetch_assoc();

            // Check if the request is approved
            if ($requestDetails["status"] === "approved") {
                $file_location = $requestDetails["file_location"];
                $book_title = $requestDetails["book_title"];
            } else {
                // Request is not approved
                $deniedMessage = "'{$requestDetails["book_title"]}' বইটির জন্য আপনার অনুরোধ বাতিল করা হয়েছে.";
            }
        } else {
            // Request not found
            $errorMessage = "লগ-ইন করা ব্যবহারকারীর জন্য কোনো অনুমোদিত অনুরোধ পাওয়া যায়নি.";
        }
    } else {
        // User not found
        $errorMessage = "ব্যবহারকারী খুঁজে পাওয়া যায় না.";
    }
} else {
    // User not logged in
    $errorMessage = "আপনি লগইন নেই.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>বিজ্ঞপ্তি</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="stylesheet/styles.css">
</head>
<body style="background-color: #000; color: #fff;" class="bd-dark text-white">
    <nav class="navbar navbar-expand-lg navbar-dark nav-tabs" style="background-color: black !important;">
        <a class="navbar-brand book-details" href="all_books.php" style="padding: 0;">
            <span style="display: inline-block; width: 150px; height: 40px; background: url('cover.png') no-repeat center center; background-size: cover; border-radius: 90px; animation: spin-horizontal 10s linear infinite;"></span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                <a class="nav-link" href="all_books.php">
                        <img src="books.png" alt="books Icon" style="width: 30px; height: 25px;">
                    </a>
                </li>
                <?php if (isset($_SESSION["username"])): ?>
                    <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <img src="logout.png" alt="logout Icon" style="width: 30px; height: 25px;">
                    </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <div class="container mt-4">
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
        <?php elseif (isset($deniedMessage)): ?>
            <div class="alert alert-danger"><?php echo $deniedMessage; ?></div>
        <?php elseif (isset($file_location)): ?>
            <h2><?php echo $book_title; ?></h2>
            <?php if ($requestDetails["status"] === "approved"): ?>
                <p>আপনি বই ডাউনলোড করার জন্য অনুমোদিত হয়েছে "<?php echo $book_title; ?>".</p>
                <a href="<?php echo $file_location; ?>" class="btn btn-success">বই ডাউনলোড করুন</a>
            <?php else: ?>
                <p> আপনার অনুরোধ "<?php echo $book_title; ?>" বইটির জন্য অনুমোদন করা হয়নি.</p>
            <?php endif; ?>
        <?php endif; ?>
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
        function changeColor(element, color) {
            element.querySelector('i').style.color = color;
        }
        function getRandomColor() {
        // Generate a random color in hexadecimal format
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
        }
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

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
