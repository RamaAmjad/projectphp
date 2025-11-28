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

      $m = mysqli_query($conn, $sql);
      header("Location: books.php");
}
?>

<h2>Add Book</h2>

<form method="post">
      Title:<input name="title"><br>
      Category:<input name="category"><br>
      Type:<input name="book_type"><br>
      Price:<input name="original_price"><br>
      <button>Add</button>
</form>