<?php
include 'header.php';
require 'db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM sale WHERE sale_id = $id";
$result = mysqli_query($conn, $sql);
$sale = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $book_id = $_POST['book_id'];
    $borrower_id = $_POST['borrower_id'];
    $sale_date = $_POST['sale_date'];
    $sale_price = $_POST['sale_price'];

    $sql = "
        UPDATE sale
        SET 
            book_id = '$book_id',
            borrower_id = '$borrower_id',
            sale_date = '$sale_date',
            sale_price = '$sale_price'
        WHERE sale_id = $id
    ";

    mysqli_query($conn, $sql);

    header("Location: dashboard.php?table=sale");
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



<div class="container">
   <h2>Edit Sale</h2>

<form method="post">
    Book ID:<input name="book_id" value="<?php echo $sale['book_id']; ?>"><br>
    Borrower ID:<input name="borrower_id" value="<?php echo $sale['borrower_id']; ?>"><br>
    Sale Date:<input type="date" name="sale_date" value="<?php echo $sale['sale_date']; ?>"><br>
    Sale Price:<input name="sale_price" value="<?php echo $sale['sale_price']; ?>"><br>
    <button>Save</button>
</form>
</div>

