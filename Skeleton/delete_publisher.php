<?php
require 'db.php';
session_start();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    mysqli_query($conn, "DELETE FROM publisher WHERE publisher_id=$id");
}

header("Location: dashboard.php");
exit;
