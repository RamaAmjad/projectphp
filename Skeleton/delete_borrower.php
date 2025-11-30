<?php
require 'db.php';
session_start();

$id = $_GET['id'] ?? 0;
mysqli_query($conn, "DELETE FROM borrower WHERE borrower_id=$id");
header("Location: dashboard.php?table=borrower");
exit;
