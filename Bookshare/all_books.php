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

// Define the default query
$booksQuery = "SELECT Books.bookid, Books.title, Books.authorid, Books.genre, Books.description, Books.publication_date, Books.file_location, Books.book_cover, Authors.username AS author_username
               FROM Books
               JOIN Authors ON Books.authorid = Authors.authorid";

// Check if a search term is provided
if (isset($_GET["search_title"])) {
    $searchTitle = $_GET["search_title"];
    // Modify the query to filter by book title
    $booksQuery .= " WHERE Books.title LIKE '%$searchTitle%'";
}

$booksResult = $conn->query($booksQuery);

// Query to get the top 5 highest-rated books
$topRatedQuery = "SELECT Books.bookid, Books.title, Books.authorid, Books.genre, Books.description, Books.publication_date, Books.file_location, Books.book_cover, Authors.username AS author_username, AVG(Review.rating) AS average_rating
                  FROM Books
                  JOIN Authors ON Books.authorid = Authors.authorid
                  LEFT JOIN Review ON Books.bookid = Review.bookid
                  GROUP BY Books.bookid
                  ORDER BY average_rating DESC
                  LIMIT 10";

$topRatedResult = $conn->query($topRatedQuery); // Execute the query to get top-rated books

// Function to calculate cosine similarity between two strings
function cosine_similarity($str1, $str2)
{
    $vector1 = explode(' ', $str1);
    $vector2 = explode(' ', $str2);

    $dotProduct = array_sum(array_map(function($a, $b) {
        return $a * $b;
    }, $vector1, $vector2));

    $magnitude1 = sqrt(array_sum(array_map(function($a) {
        return $a * $a;
    }, $vector1)));

    $magnitude2 = sqrt(array_sum(array_map(function($a) {
        return $a * $a;
    }, $vector2)));

    if ($magnitude1 == 0 || $magnitude2 == 0) {
        return 0; // To handle division by zero
    } else {
        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}

// Query to get the top 3 recommended books based on cosine similarity
$recommendedQuery = "SELECT Books.bookid, Books.title, Books.authorid, Books.genre, Books.description, Books.publication_date, Books.file_location, Books.book_cover, Authors.username AS author_username
                     FROM Books
                     JOIN Authors ON Books.authorid = Authors.authorid
                     WHERE Books.bookid != ?
                     ORDER BY (CASE WHEN Books.description IS NOT NULL THEN ? ELSE 0 END) * ? DESC
                     LIMIT 3";

// Get the first book in the top-rated books result to use for recommendation
$firstRecommendedBook = $topRatedResult->fetch_assoc();

// Prepare the recommendation statement
$recommendedStmt = $conn->prepare($recommendedQuery);
$bookid = $firstRecommendedBook['bookid'];
$description1 = '%' . $firstRecommendedBook['description'] . '%';
$description2 = '%' . $firstRecommendedBook['description'] . '%';
$recommendedStmt->bind_param("iss", $bookid, $description1, $description2);

$recommendedStmt->execute();
$recommendedResult = $recommendedStmt->get_result();

// Define the default sorting order (you can change this as needed)
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';

// Modify the query to handle sorting
$booksQuery = "SELECT Books.bookid, Books.title, Books.authorid, Books.genre, Books.description, Books.publication_date, Books.file_location, Books.book_cover, Authors.username AS author_username
               FROM Books
               JOIN Authors ON Books.authorid = Authors.authorid";

// Check if a search term is provided
if (isset($_GET["search_title"])) {
    $searchTitle = $_GET["search_title"];
    // Modify the query to filter by book title
    $booksQuery .= " WHERE Books.title LIKE '%$searchTitle%'";
}

// Add sorting criteria to the query
switch ($sort) {
    case 'author':
        $booksQuery .= " ORDER BY author_username ASC";
        break;
    case 'publication_date':
        $booksQuery .= " ORDER BY publication_date DESC";
        break;
    case 'average_rating':
        $booksQuery .= " LEFT JOIN Review ON Books.bookid = Review.bookid
                       GROUP BY Books.bookid
                       ORDER BY AVG(Review.rating) DESC";
        break;
    default:
        // Default sorting by title
        $booksQuery .= " ORDER BY title ASC";
        break;
}

$booksResult = $conn->query($booksQuery);
function getRandomColor() {
    // Generate a random color in hexadecimal format
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>সব বই</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="stylesheet" href="stylesheet/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .dark-background {
    background-color: #000; /* Dark background color */
    color: #fff; /* White text color */
}
 /* Style for the sorting form */
 #sort-form {
    display: inline-block;
    position: relative;
}

#sort {
    padding: 10px 25px;
    border: none;
    background-color: #fff;
    color: #333;
    border-radius: 5px;
    outline: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

#sort:hover {
    background-color: #f5f5f5;
}

#sort:focus {
    box-shadow: 0 0 5px #007bff;
}

/* Style for the label */
label {
    font-weight: bold;
    margin-right: 10px;
}
.book-details {
    background-color: #000;
    color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
    transition: transform 0.3s ease, opacity 0.3s ease;
    position: relative;
    animation: float 3s infinite;
    
}

.book-details:hover {
    transform: scale(1.05);
    opacity: 0.9;
}
@keyframes spin-horizontal {
    0% { transform: rotateY(0deg); }
    100% { transform: rotateY(360deg); }
}
.underlineEffects ul { 
margin: 0 auto; 
padding: 0; 
list-style: none; 
display: table;
text-align: center;
}
.underlineEffects li { 
display: table-cell; 
position: relative; 
padding: 15px 0;
}
.underlineEffects a {
color: #fff;
text-transform: uppercase;
text-decoration: none;
letter-spacing: 0.15em;
display: inline-block;
padding: 15px 20px;
position: relative;
}
.underlineEffects a:after {    
background: none repeat scroll 0 0 transparent;
bottom: 0;
content: "";
display: block;
height: 2px;
left: 50%;
position: absolute;
background: #fff;
transition: width 0.3s ease 0s, left 0.3s ease 0s;
width: 0;
}
.underlineEffects a:hover:after { 
width: 100%; 
left: 0; 
}
body,
canvas {
position: absolute;
width: 100%;
height: 100%;
margin: 0;
padding: 0;
}
        
    </style>

</head>
<body style="background-color: #000; color: #fff;" class="bd-dark text-white">
    <nav class="navbar fixed-top navbar-expand-lg navbar-dark nav-tabs" style="background-color: black !important;">
        <a class="navbar-brand" href="all_books.php" style="padding: 0;">
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
                            <a class="nav-link " href="notification.php">
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


    <!-- Recommended Books Carousel -->
    <?php if (isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "reader"): ?>
    <div class="container mb-3 book-details">
        <h2 class="book-details">প্রস্তাবিত বই</h2>
        <div id="recommendedBooksCarousel" class="carousel slide" style="max-width: 100%; max-height: 60vh;" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <li data-target="#recommendedBooksCarousel" data-slide-to="<?= $i ?>" <?= ($i === 0) ? 'class="active"' : '' ?>></li>
                    <?php endfor; ?>
                </ol>
                <div class="carousel-inner">
                    <?php $first = true; ?>
                    <?php while ($recommendedBook = $recommendedResult->fetch_assoc()): ?>
                        <div class="carousel-item <?= ($first) ? 'active' : '' ?>">
                            <a href="book_details.php?bookid=<?= $recommendedBook['bookid']; ?>" class="text-decoration-none">
                                <img src="<?= $recommendedBook['book_cover'] ?>" alt="Book Cover" class="d-block w-100">
                                <div class="carousel-caption">
                                    <h3><?= $recommendedBook['title'] ?></h3>
                                    <p>লেখক: <?= $recommendedBook['author_username'] ?></p>
                                </div>
                            </a>
                        </div>
                        <?php $first = false; ?>
                    <?php endwhile; ?>
                </div>
                <a class="carousel-control-prev" href="#recommendedBooksCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#recommendedBooksCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    <?php endif; ?>
    <!-- Add a styled search form and align it to the right -->
    <form method="GET" action="all_books.php" class="mb-4 ml-auto" style="width: 300px;">
        <div class="input-group input-group-sm rounded-pill">
            <input type="text" name="search_title" class="form-control border-0 rounded-pill" placeholder="Search by Title">
            <div class="input-group-append">
            <button type="submit" class="btn btn-light rounded-pill" style="background-image: url('search.png'); background-size: cover; background-repeat: no-repeat; text-indent: -9999px; width: 30px; height: 25px;"></button>
            </div>
        </div>
    </form>
    
    <!-- Sorting order -->
    <div class="container my-5 book-details">
        <form method="GET" action="all_books.php" id="sort-form">
            <label for="sort" class="">ক্রমানুসার:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="title" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'title') echo 'selected'; ?>>শিরোনাম</option>
                <option value="author" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'author') echo 'selected'; ?>>লেখক</option>
                <option value="publication_date" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'publication_date') echo 'selected'; ?>>প্রকাশনার তারিখ</option>
                <option value="average_rating" <?php if (isset($_GET['sort']) && $_GET['sort'] === 'average_rating') echo 'selected'; ?>>গড় রেটিং</option>
            </select>
        </form>
</div>


    <div class="container mt-4">
        <h5>&nbsp</h5>
    </div>
    <div class="container mt-4">
            <h2 class="book-details">সব বই</h2>
            <?php if (isset($_SESSION["user_type"]) && $_SESSION["user_type"] === "author"): ?>
                <div class="text-right mb-3">
                    <a href="add_book.php" class="btn btn-success book-details">বই যোগ করুন</a>
                </div>
                <div class="text-right mb-3">
                    <a href="handle_request.php" class="btn btn-primary book-details">অনুরোধ দেখুন</a>
                </div>
            <?php endif; ?>
            <div class="row">
                <?php while ($row = $booksResult->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-dark book-details mb-3" onmouseover="this.style.boxShadow='0 0 20px <?php echo getRandomColor(); ?>';" onmouseout="this.style.boxShadow='';">
                                <img src="<?php echo $row["book_cover"]; ?>" alt="Book Cover" class="card-img-top">
                                <div class="card-body">
                                    <h5 class="card-title text-white"><?php echo $row["title"]; ?></h5>
                                    <a href="book_details.php?bookid=<?php echo $row["bookid"]; ?>" class="btn btn-primary btn-transparent">বিস্তারিত দেখুন</a>
                                </div>
                            </div>
                    </div>
                <?php endwhile; ?>
            </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center " style="background-color: black !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-inline">
                        <li class="list-inline-item" onmouseover="changeColor(this, '#ffcc00');" onmouseout="changeColor(this, '');">
                            <a href="https://www.facebook.com/redrik278" target="_blank" class="btn btn-outline-primary border-0 text-white">
                                <i class="fab fa-facebook"></i> <!-- Facebook Icon -->
                            </a>
                        </li>
                        <li class="list-inline-item " onmouseover="changeColor(this, '#00ffcc');" onmouseout="changeColor(this, '');">
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
    </script>







    <!-- Add Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
