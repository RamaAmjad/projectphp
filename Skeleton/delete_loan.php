<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$id = intval($id);

// Check if loan exists
$loanExists = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM loan WHERE loan_id=$id"))['cnt'];

if($loanExists == 0){
    echo "<script>
            alert('❌ Loan not found.');
            window.history.back();
          </script>";
    exit;
}

// Safe to delete
mysqli_query($conn, "DELETE FROM loan WHERE loan_id=$id");

// Show success alert
echo "<script>
        alert('✅ Loan deleted successfully.');
        window.location.href='dashboard.php?table=loan';
      </script>";
exit;
?>
