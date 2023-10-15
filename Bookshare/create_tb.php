<?php
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

// SQL queries to create tables
$queries = [
    "CREATE TABLE IF NOT EXISTS Readers (
        readerid INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS Authors (
        authorid INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(255) NOT NULL
    )",
    
    "CREATE TABLE IF NOT EXISTS Books (
        bookid INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        authorid INT NOT NULL,
        genre VARCHAR(100),
        description TEXT,
        publication_date DATE,
        file_location VARCHAR(255),
        book_cover VARCHAR(255),
        FOREIGN KEY (authorid) REFERENCES Authors(authorid)
    )",
    
    "CREATE TABLE IF NOT EXISTS BookRequest (
        requestid INT PRIMARY KEY AUTO_INCREMENT,
        readerid INT NOT NULL,
        bookid INT NOT NULL,
        request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('approved', 'denied', 'pending') NOT NULL,
        FOREIGN KEY (readerid) REFERENCES Readers(readerid),
        FOREIGN KEY (bookid) REFERENCES Books(bookid)
    )",
    
    "CREATE TABLE IF NOT EXISTS BookFormat (
        formatid INT PRIMARY KEY AUTO_INCREMENT,
        bookid INT NOT NULL,
        formattype VARCHAR(50) NOT NULL,
        FOREIGN KEY (bookid) REFERENCES Books(bookid)
    )",
    
    "CREATE TABLE IF NOT EXISTS Review (
        reviewid INT PRIMARY KEY AUTO_INCREMENT,
        bookid INT NOT NULL,
        readerid INT NOT NULL,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (bookid) REFERENCES Books(bookid),
        FOREIGN KEY (readerid) REFERENCES Readers(readerid)
    )",
    "CREATE TABLE IF NOT EXISTS Cart (
        cart_id INT AUTO_INCREMENT PRIMARY KEY,
        readerid INT NOT NULL,
        bookid INT NOT NULL,
        FOREIGN KEY (readerid) REFERENCES Readers(readerid),
        FOREIGN KEY (bookid) REFERENCES Books(bookid)
    )"        
];

// Execute each query
foreach ($queries as $query) {
    if ($conn->query($query) !== TRUE) {
        echo "Error creating table: " . $conn->error;
    }
}

echo "Tables created successfully";

// $conn->close();
?>
