<?php
include 'header.php';
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $book_id = $_POST['book_id'];
    $borrower_id = $_POST['borrower_id'];
    $sale_date = $_POST['sale_date'];
    $sale_price = $_POST['sale_price'];

    $sql = "INSERT INTO sale(book_id, borrower_id, sale_date, sale_price)
            VALUES('$book_id', '$borrower_id', '$sale_date', '$sale_price')";

    mysqli_query($conn, $sql);

    header("Location: dashboard.php?table=sale");
}
?>

<style>
body { font-family: Arial, sans-serif; background: #f7f7f7; padding: 20px; }
h2 { color: #333; }
form { background: #fff; padding: 20px; border-radius: 8px; width: 300px; }
input { display: block; width: 100%; margin: 10px 0; padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
button { padding: 10px 15px; border: none; border-radius: 4px; background: #2196F3; color: white; cursor: pointer; }
button:hover { background: #0b7dda; }
</style>

<div class="container">
   <h2>Add Sale</h2>

<form method="post">
    Book ID:<input name="book_id"><br>
    Borrower ID:<input name="borrower_id"><br>
    Sale Date:<input type="date" name="sale_date"><br>
    Sale Price:<input name="sale_price"><br>
    <button>Add</button>
</form>
</div>