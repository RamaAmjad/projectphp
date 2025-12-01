<?php
include 'header.php';
require 'db.php';
?>

<h2>Authors</h2>

<?php
$sql = "SELECT * FROM author ORDER BY author_id DESC";
$r = mysqli_query($conn, $sql);
?>


<?php if (mysqli_num_rows($r) > 0): ?>
<table border=1 cellpadding=5>
    <tr>
        <th>ID</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Country</th>
        <th>Bio</th>
        <th>Actions</th>
    </tr>

      <?php while ($row = mysqli_fetch_assoc($r)) { ?>
    <tr>
        <td><?php echo $row['author_id']; ?></td>
        <td><?php echo htmlspecialchars($row['first_name']); ?></td>
        <td><?php echo htmlspecialchars($row['last_name']); ?></td>
        <td><?php echo htmlspecialchars($row['country']); ?></td>
        <td><?php echo htmlspecialchars($row['bio']); ?></td>
        <td>
            <a href="edit_author.php?id=<?php echo $row['author_id']; ?>">Edit</a> |
            <a onclick="return confirm('Are you sure?');"
                href="delete_author.php?id=<?php echo $row['author_id']; ?>">Delete</a>
        </td>
    </tr>
    <?php } ?>

</table>
<?php else: ?>
<p>No authors found.</p>
<?php endif; ?>

