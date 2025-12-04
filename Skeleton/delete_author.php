<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$id = intval($id);

// تحقق إذا المؤلف مرتبط بأي كتاب
$linkedBooks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM bookauthor WHERE author_id=$id"))['cnt'];

if($linkedBooks > 0){
    // لا يمكن الحذف
    echo "<script>
            alert('❌ Cannot delete this author because it is linked to books.');
            window.history.back();
          </script>";
    exit;
}

// آمن للحذف
mysqli_query($conn, "DELETE FROM author WHERE author_id=$id");

echo "<script>
        alert('✅ Author deleted successfully.');
        window.location.href='dashboard.php?table=author';
      </script>";
exit;
?>
