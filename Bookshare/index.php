<!DOCTYPE html>
<html>
<head>
    <title>নিবন্ধন</title>
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
        <ul class="navbar-nav ml-auto"> <!-- Use ml-auto to push items to the right -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <img src="register.png" alt="Register Icon" style="width: 30px; height: 30px;">
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

    
    <!-- Registration Form -->
    <div class="container mt-4">
        <h2>নিবন্ধন</h2>
        <form method="post" action="register.php">
            <label for="user_type">ব্যবহারকারীর ধরন:</label>
            <select class="form-control" id="user_type" name="user_type">
                <option value="reader">Reader</option>
                <option value="author">Author</option>
            </select><br>
    
            <label for="username">ব্যবহারকারীর নাম:</label>
            <input type="text" class="form-control book-details" id="username" name="username" required><br>
            
            <label for="email">ইমেইল:</label>
            <input type="email" class="form-control book-details" id="email" name="email" required><br>
            
            <label for="password">পাসওয়ার্ড:</label>
            <input type="password" class="form-control book-details" id="password" name="password" required><br>
            
            <button type="submit" class="btn btn-primary book-details">নিবন্ধন</button>
        </form>
        
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
        <?php
        
        $servername = "localhost";
        $username = "root";  
        $password = "";      
        $dbname = "bookshare"; 
        
        // Create a connection
        $conn = new mysqli($servername, $username, $password);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Check if the database exists, create if not
        $createDbQuery = "CREATE DATABASE IF NOT EXISTS $dbname";
        if ($conn->query($createDbQuery) === TRUE) {
            echo '<div class="alert alert-danger" role="alert">Database Already Exist ' .  '</div>';
        } else {
            echo '<div class="alert alert-success" role="alert">Database Created Successfully ' . $conn->error . "<br>";
        }
        
        $conn->select_db($dbname);
        
        // Check if the tables exist, create if not
        if (!tableExists($conn, 'Readers') || !tableExists($conn, 'Authors') || !tableExists($conn, 'Books') || !tableExists($conn, 'BookRequest') || !tableExists($conn, 'BookFormat') || !tableExists($conn, 'Review') || !tableExists($conn, 'Cart')) {
            include 'create_tb.php'; 
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_type = $_POST["user_type"];
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = $_POST["password"];
        
            // Validate username using regex (alphanumeric characters and underscores)
            if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
                echo "<div class='alert alert-danger'>Invalid username format. Use only letters, numbers, and underscores.</div>";
            }elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
                // Validate email using regex
                echo "<div class='alert alert-danger'>Invalid email format.</div>";
            } else {
                // Hash the password using password_hash
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
                // Check if the email already exists in the database
                $checkEmailQuery = "SELECT * FROM Readers WHERE email = '$email'";
                $result = $conn->query($checkEmailQuery);
        
                if ($result && $result->num_rows > 0) {
                    // Email already exists, show a warning
                    echo "<div class='alert alert-danger'>Email already exists. Please use a different email.</div>";
                } else {
                    // Email is unique, proceed with registration
                    if ($user_type == "reader") {
                        $insertReaderQuery = "INSERT INTO Readers (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
                        if ($conn->query($insertReaderQuery) === TRUE) {
                            echo "<div class='alert alert-success'>Registration successful!</div>";
        
                            // Retrieve the newly inserted reader's ID
                            $readerId = $conn->insert_id;
                        } else {
                            echo "<div class='alert alert-danger'>Registration failed. Please try again later.</div>";
                        }
                    } elseif ($user_type == "author") {
                        $insertAuthorQuery = "INSERT INTO Authors (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
                        if ($conn->query($insertAuthorQuery) === TRUE) {
                            echo "<div class='alert alert-success'>Author registration successful!</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Registration failed. Please try again later.</div>";
                        }
                    }
                }
            }
        }
        
        // Close the connection
        $conn->close();
        
        function tableExists($conn, $tableName) {
            $result = $conn->query("SHOW TABLES LIKE '$tableName'");
            return $result && $result->num_rows > 0;
        }
        ?>
    </div>
    
    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
