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

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page or display an error message
    header("Location: login.php");
    exit();
}

// Retrieve user_id from the session
$user_id = $_SESSION["user_id"];

// Handle removing books from the cart
if (isset($_GET["remove"])) {
    $bookToRemove = $_GET["remove"];
    
    // Ensure that the book to remove belongs to the current user
    $checkOwnershipQuery = "SELECT * FROM Cart WHERE readerid = $user_id AND bookid = $bookToRemove";
    $checkOwnershipResult = $conn->query($checkOwnershipQuery);
    
    if ($checkOwnershipResult && $checkOwnershipResult->num_rows > 0) {
        // The book belongs to the user, so remove it from the cart
        $removeQuery = "DELETE FROM Cart WHERE readerid = $user_id AND bookid = $bookToRemove";
        $removeResult = $conn->query($removeQuery);
        
        if ($removeResult) {
            // Book removed successfully
            header("Location: cart.php");
            exit();
        } else {
            // Error removing book
            echo "Error removing book from the cart.";
        }
    } else {
        // Book does not belong to the user, or it doesn't exist in the cart
        echo "Book not found in your cart.";
    }
}

// Retrieve user's cart information
$cartQuery = "SELECT Books.* FROM Cart JOIN Books ON Cart.bookid = Books.bookid WHERE Cart.readerid = $user_id";
$cartResult = $conn->query($cartQuery);

if (!$cartResult) {
    die("Error fetching cart items: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
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
                    <?php if ($_SESSION["user_type"] === "reader"): ?>
                        <li class="nav-item">
                        <a class="nav-link" href="notification.php">
                        <img src="notify.png" alt="notification Icon" style="width: 30px; height: 25px;">
                    </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <img src="cart.png" alt="cart Icon" style="width: 30px; height: 25px;">
                    </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <!-- Add a styled search form and align it to the right -->
    <form method="GET" action="all_books.php" class="mb-4 ml-auto" style="width: 300px;">
        <div class="input-group input-group-sm rounded-pill">
            <input type="text" name="search_title" class="form-control border-0 rounded-pill text-white" placeholder="Search by Title">
            <div class="input-group-append">
            <button type="submit" class="btn btn-light rounded-pill" style="background-image: url('search.png'); background-size: cover; background-repeat: no-repeat; text-indent: -9999px; width: 30px; height: 25px;"></button>
            </div>
        </div>
    </form>
    <div class="container mt-4">
        <h2 class="text-white">কার্ট</h2>
        <table class="table text-white"> <!-- Apply the text-white class to the table -->
            <thead>
                <tr>
                    <th>লেখক</th>
                    <th>ধরণ</th>
                    <th>বর্ণনা</th>
                    <th>প্রকাশনার তারিখ</th>
                    <th>কর্ম পরিকল্পনা</th>
                    <th>অনুরোধ</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $cartResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo isset($row["title"]) ? $row["title"] : ''; ?></td>
                    <td><?php echo isset($row["genre"]) ? $row["genre"] : ''; ?></td>
                    <td><?php echo isset($row["description"]) ? $row["description"] : ''; ?></td>
                    <td><?php echo isset($row["publication_date"]) ? $row["publication_date"] : ''; ?></td>
                    <td>
                        <a href="cart.php?remove=<?php echo $row["bookid"]; ?>" class="btn btn-danger">অপসারণ</a>
                    </td>
                    <td>
                        <a href="request.php?bookid=<?php echo $row["bookid"]; ?>" class="btn btn-primary text-white">অনুরোধ</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
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

