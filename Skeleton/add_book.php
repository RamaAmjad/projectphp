<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $t = $_POST['title'];
      $c = $_POST['category'];
      $ty = $_POST['book_type'];
      $p = $_POST['original_price'];

      $sql = "INSERT INTO book(title,category,book_type,original_price)
       VALUES('$t','$c','$ty','$p')";

      mysqli_query($conn, $sql);
    header("Location: dashboard.php?table=book");
}
?>

<style>
body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
h2 { color: #333; }
form { background: #fff; padding: 20px; border-radius: 8px; width: 350px; }
input { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #9C27B0; color: white; cursor: pointer; }
button:hover { background: #7b1fa2; }
</style>

<h2>Add Book</h2>

<form method="post">
      Title:<input name="title">
      Category:<input name="category">
      Type:<input name="book_type">
      Price:<input name="original_price">
      <button>Add</button>
</form>
