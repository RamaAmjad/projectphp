<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$id = intval($id);


mysqli_query($conn, "DELETE FROM sale WHERE sale_id=$id");

echo "<script>
        alert('✅ Sale deleted successfully.');
        window.location.href='dashboard.php?table=sale';
      </script>";
exit;
?>
