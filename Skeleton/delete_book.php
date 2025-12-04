<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$id = intval($id);

// Check if book is linked to loans or sales
$linkedLoan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM loan WHERE book_id=$id"))['cnt'];
$linkedSale = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM sale WHERE book_id=$id"))['cnt'];

if($linkedLoan > 0 || $linkedSale > 0){
    // Cannot delete, show alert
    echo "<script>
            alert('❌ Cannot delete this book because it is linked to sales or loans.');
            window.history.back();
          </script>";
    exit;
}

// Safe to delete
mysqli_query($conn, "DELETE FROM book WHERE book_id=$id");

// Show success alert
echo "<script>
        alert('✅ Book deleted successfully.');
        window.location.href='dashboard.php?table=book';
      </script>";
exit;
?>
