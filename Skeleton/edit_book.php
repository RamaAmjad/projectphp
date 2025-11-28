<?php include 'header.php';
require 'db.php';

$id = $_GET['id'];

$sql = "SELECT * FROM book WHERE book_id = $id";

$result = mysqli_query($conn, $sql);

$bk = mysqli_fetch_assoc($result);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Read form data
    $t = $_POST['title'];
    $c = $_POST['category'];
    $ty = $_POST['book_type'];
    $p = $_POST['original_price'];

    // 2. Build the update query
    $sql = "
        UPDATE book 
        SET 
            title = '$t',
            category = '$c',
            book_type = '$ty',
            original_price = '$p'
        WHERE book_id = $id
    ";

    $result = mysqli_query($conn, $sql);

    // 5. Fetch updated record
    $q = "SELECT * FROM book WHERE book_id = $id";
    $r = mysqli_query($conn, $q);
    $bk = mysqli_fetch_assoc($r);

    header("Location: books.php");
}

?>
<h2>Edit Book</h2>
<form method="post">
    Title:<input name="title" value="<?php echo $bk['title']; ?>"><br>
    Category:<input name="category" value="<?php echo $bk['category']; ?>"><br>
    Type:<input name="book_type" value="<?php echo $bk['book_type']; ?>"><br>
    Price:<input name="original_price" value="<?php echo $bk['original_price']; ?>"><br>
    <button>Save</button>
</form>