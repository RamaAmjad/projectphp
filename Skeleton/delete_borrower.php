<?php
session_start();
require 'db.php';

$id = $_GET['id'] ?? 0;
$id = intval($id);

// Check if borrower is linked to loans or sales
$linkedLoan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM loan WHERE borrower_id=$id"))['cnt'];
$linkedSale = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM sale WHERE borrower_id=$id"))['cnt'];

if($linkedLoan > 0 || $linkedSale > 0){
    // Cannot delete, show alert
    echo "<script>
            alert('❌ Cannot delete this borrower because they are linked to loans or sales.');
            window.history.back();
          </script>";
    exit;
}

// Safe to delete
mysqli_query($conn, "DELETE FROM borrower WHERE borrower_id=$id");

// Show success alert
echo "<script>
        alert('✅ Borrower deleted successfully.');
        window.location.href='dashboard.php?table=borrower';
      </script>";
exit;
?>
