<?php
include 'header.php';
require 'db.php';
?>

<h2>Borrowers</h2>

<?php
$sql = "SELECT * FROM borrower ORDER BY borrower_id DESC";
$r = mysqli_query($conn, $sql);
?>

<?php if (mysqli_num_rows($r) > 0): ?>
<table border=1 cellpadding=5>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Type ID</th>
        <th>Contact Info</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($r)) { ?>
    <tr>
        <td><?php echo $row['borrower_id']; ?></td>
        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['type_id']); ?></td>
        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
        <td>
            <a href="edit_borrower.php?id=<?php echo $row['borrower_id']; ?>">Edit</a> |
            <a onclick="return confirm('Are you sure?');"
               href="delete_borrower.php?id=<?php echo $row['borrower_id']; ?>">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
<?php else: ?>
<p>No borrowers found.</p>
<?php endif; ?>


