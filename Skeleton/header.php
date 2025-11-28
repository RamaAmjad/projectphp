<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<p>
    Logged in as: <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['role']; ?>)
    | <a href="books.php">Books</a> | <a href="add_book.php">Add Book</a> | <a href="logout.php">Logout</a>
</p>
<hr>