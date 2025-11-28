<?php
include 'header.php';
require 'db.php';
?>

<h2>Books</h2>

<?php
$sql = "SELECT * FROM book ORDER BY book_id DESC LIMIT 10";
$r = mysqli_query($conn, $sql);

?>

<table border=1 cellpadding=5>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Category</th>
        <th>Type</th>
        <th>Price</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($r)) { ?>
<tr>
    <td><?php echo $row['book_id']; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['category']; ?></td>
    <td><?php echo $row['book_type']; ?></td>
    <td><?php echo $row['original_price']; ?></td>
    <td>
        <a href="edit_book.php?id=<?php echo $row['book_id']; ?>">Edit</a> |
        <a onclick="return confirm('Are you sure?');"
            href="delete_book.php?id=<?php echo $row['book_id']; ?>">Delete</a>
    </td>
</tr>

<?php } ?>

</table>