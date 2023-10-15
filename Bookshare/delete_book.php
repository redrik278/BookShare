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

if (!isset($_SESSION["username"]) || $_SESSION["user_type"] !== "author") {
    // Only authors are allowed to delete books
    header("Location: login.php");
    exit();
}

if (isset($_GET["bookid"])) {
    $bookid = $_GET["bookid"];

    // Delete related records in the review table first
    $deleteReviewsQuery = "DELETE FROM review WHERE bookid = $bookid";
    $deleteReviewsResult = $conn->query($deleteReviewsQuery);

    if ($deleteReviewsResult) {
        // Related records deleted successfully, proceed to delete the book
        $deleteBookQuery = "DELETE FROM Books WHERE bookid = $bookid";
        $deleteBookResult = $conn->query($deleteBookQuery);

        if ($deleteBookResult) {
            // Book deleted successfully
            $_SESSION["delete_message"] = "বই সফলভাবে মুছে ফেলা হয়েছে.";
        } else {
            // Error deleting book
            $_SESSION["delete_message"] = "বই মুছে ফেলার সময় ত্রুটি.";
        }
    } else {
        // Error deleting related records
        $_SESSION["delete_message"] = "সম্পর্কিত রেকর্ড মুছে ফেলার সময় ত্রুটি.";
    }

    // Redirect back to the book details page
    header("Location: book_details.php?bookid=$bookid");
    exit();
}

$conn->close();
?>
