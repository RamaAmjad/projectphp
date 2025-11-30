<?php
session_start();
require 'db.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Tables and Reports
$tables = ['book', 'author', 'borrower', 'loan', 'sale'];
$reports = [
    'Total value of all books' => 'report_total_books.php',
    'Books borrowed or bought by a specific borrower' => 'report_borrower.php',
    'Books currently available for borrowing' => 'report_available_books.php'
];

$action = $_GET['action'] ?? '';
$selectedTable = $_GET['table'] ?? '';
$id = $_GET['id'] ?? '';
   
$search = $_GET['search'] ?? '';

// Map table to primary key column
$column_id_map = [
    'book'     => 'book_id',
    'author'   => 'author_id',
    'borrower' => 'borrower_id',
    'loan'     => 'loan_id',
    'sale'     => 'sale_id'
];

// Handle delete
if ($action === 'delete' && $selectedTable && $id) {
    if (isset($column_id_map[$selectedTable])) {
        $col = $column_id_map[$selectedTable];
        $id = intval($id);
        mysqli_query($conn, "DELETE FROM $selectedTable WHERE $col=$id");
    }
    header("Location: dashboard.php?table=$selectedTable");
    exit;
}

// Handle Add/Edit inline
$form_data = [];
$form_action = '';
if ($action === 'edit' && $selectedTable && $id && isset($column_id_map[$selectedTable])) {
    $col = $column_id_map[$selectedTable];
    $id = intval($id);
    $res = mysqli_query($conn, "SELECT * FROM $selectedTable WHERE $col=$id");
    $form_data = mysqli_fetch_assoc($res);
    $form_action = 'edit';
} elseif ($action === 'add' && $selectedTable) {
    $form_action = 'add';
}

// Enable MySQLi exceptions
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedTable = $_POST['table'] ?? $selectedTable;

    try {
        if ($selectedTable === 'book') {
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? '';
            $type = $_POST['book_type'] ?? '';
            $price = $_POST['original_price'] ?? '';

            if ($form_action === 'edit') {
                $id = intval($_POST['id']);
                mysqli_query($conn, "UPDATE book SET title='$title', category='$category', book_type='$type', original_price='$price' WHERE book_id=$id");
            } else {
                mysqli_query($conn, "INSERT INTO book(title, category, book_type, original_price) VALUES('$title', '$category', '$type', '$price')");
            }

        } elseif ($selectedTable === 'author') {
            $first = $_POST['first_name'] ?? '';
            $last  = $_POST['last_name'] ?? '';
            $country = $_POST['country'] ?? '';
            $bio = $_POST['bio'] ?? '';

            if ($form_action === 'edit') {
                $id = intval($_POST['id']);
                mysqli_query($conn, "UPDATE author SET first_name='$first', last_name='$last', country='$country', bio='$bio' WHERE author_id=$id");
            } else {
                mysqli_query($conn, "INSERT INTO author(first_name,last_name,country,bio) VALUES('$first','$last','$country','$bio')");
            }

        } elseif ($selectedTable === 'borrower') {
            $first = $_POST['first_name'] ?? '';
            $last  = $_POST['last_name'] ?? '';
            $type  = $_POST['type_id'] ?? '';
            $contact = $_POST['contact_info'] ?? '';

            if ($form_action === 'edit') {
                $id = intval($_POST['id']);
                mysqli_query($conn, "UPDATE borrower SET first_name='$first', last_name='$last', type_id='$type', contact_info='$contact' WHERE borrower_id=$id");
            } else {
                mysqli_query($conn, "INSERT INTO borrower(first_name,last_name,type_id,contact_info) VALUES('$first','$last','$type','$contact')");
            }
        }

        // Success: go back to table view
        header("Location: dashboard.php?table=$selectedTable");
        exit;

    } catch (mysqli_sql_exception $e) {
        // Catch any SQL errors (foreign key, etc.) and show as popup
        $errors[] = $e->getMessage();
        echo "<script>alert('Error: ".implode('\\n', array_map('addslashes', $errors))."');</script>";
    }
}







?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Dashboard</title>
<style>
body {
    margin: 0; padding: 0;
    font-family: Arial, sans-serif;
    background-color: #E3F2FD;
}
.error-popup {
    background-color: #f44336;
    color: white;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

.header {
    text-align: center;
    padding: 25px 20px;
    background-color: #1565C0;
    color: white;
}
.header h1 { margin:0; }
.header p { margin: 5px 0 0 0; font-size: 16px; }

.container { padding: 20px; max-width: 1200px; margin: auto; }
.table-selector { margin-bottom: 20px; }
button, input[type="text"], select { padding: 10px; border-radius: 6px; border: 1px solid #aaa; font-size: 14px; }
button { background-color: #1565C0; color:white; border:none; cursor:pointer; }
button:hover { background-color:#0D47A1; }

table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #aaa; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }

a.logout { display:block; text-align:center; margin:20px; font-weight:bold; color:#1565C0; text-decoration:none;}
a.logout:hover { text-decoration:underline; }

/* Add New Button */
.add-btn {
    display: inline-block;
    width: auto;
    padding: 12px 20px;
    background: linear-gradient(45deg, #4CAF50, #66BB6A);
    color: white;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    border-radius: 10px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}
.add-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 10px rgba(0,0,0,0.3);
    background: linear-gradient(45deg, #43A047, #5DBB63);
}

/* Edit/Delete Buttons */
.edit-btn { background-color:#FF9800; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer; }
.delete-btn { background-color:#f44336; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer; }
.edit-btn:hover { background-color:#e67e22; }
.delete-btn:hover { background-color:#d32f2f; }

/* Reports buttons/cards */
.reports {
    margin-top: 30px;
}
.reports a {
    display: inline-block;
    width: 220px;
    padding: 15px 20px;
    background: linear-gradient(45deg, #1565C0, #1976D2);
    color: white;
    font-size: 15px;
    font-weight: bold;
    text-align: center;
    border-radius: 12px;
    text-decoration: none;
    margin: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: transform 0.2s, box-shadow 0.2s;
}
.reports a:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    background: linear-gradient(45deg, #0D47A1, #1565C0);
}
</style>
</head>
<body>

<div class="header">
    <h1>Library Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($role); ?>)</p>
</div>

<div class="container">

    <!-- Table Selector -->
    <div class="table-selector">
        <form method="get">
            <label>Select Table: </label>
            <select name="table">
                <option value="">--Choose Table--</option>
                <?php foreach($tables as $table): ?>
                    <option value="<?php echo $table; ?>" <?php if($selectedTable==$table) echo 'selected'; ?>>
                        <?php echo ucfirst($table); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">View</button>
        </form>
    </div>

  <?php if ($form_action === 'add' || $form_action === 'edit'): ?>
<h3><?php echo $form_action === 'edit' ? 'Edit ' . ucfirst($selectedTable) : 'Add ' . ucfirst($selectedTable); ?></h3>
<form method="post" style="margin-bottom:20px;">
    <?php if ($form_action === 'edit'): ?>
        <input type="hidden" name="id" value="<?php echo $form_data[$column_id_map[$selectedTable]]; ?>">
    <?php endif; ?>

    <?php if ($selectedTable === 'book'): ?>
        Title: <input name="title" value="<?php echo $form_data['title'] ?? ''; ?>"><br>
        Category: <input name="category" value="<?php echo $form_data['category'] ?? ''; ?>"><br>
        Type: <input name="book_type" value="<?php echo $form_data['book_type'] ?? ''; ?>"><br>
        Price: <input name="original_price" value="<?php echo $form_data['original_price'] ?? ''; ?>"><br>
    <?php elseif ($selectedTable === 'author'): ?>
        First Name: <input name="first_name" value="<?php echo $form_data['first_name'] ?? ''; ?>"><br>
        Last Name: <input name="last_name" value="<?php echo $form_data['last_name'] ?? ''; ?>"><br>
        Country: <input name="country" value="<?php echo $form_data['country'] ?? ''; ?>"><br>
        Bio: <textarea name="bio"><?php echo $form_data['bio'] ?? ''; ?></textarea><br>
    <?php elseif ($selectedTable === 'borrower'): ?>
        First Name: <input name="first_name" value="<?php echo $form_data['first_name'] ?? ''; ?>"><br>
        Last Name: <input name="last_name" value="<?php echo $form_data['last_name'] ?? ''; ?>"><br>
        Type ID: <input name="type_id" value="<?php echo $form_data['type_id'] ?? ''; ?>"><br>
        Contact Info: <input name="contact_info" value="<?php echo $form_data['contact_info'] ?? ''; ?>"><br>
    <?php endif; ?>

    <button><?php echo $form_action === 'edit' ? 'Save' : 'Add'; ?></button>
    <a href="dashboard.php?table=<?php echo $selectedTable; ?>" style="margin-left:10px;">Cancel</a>
</form>
<?php endif; ?>



    <!-- Dynamic Table Display -->
    <?php
    if($selectedTable) {
        $sql = "SELECT * FROM $selectedTable";
        if($search) {
            $sql .= " WHERE ";
            $columns = [];
            $res = mysqli_query($conn,"SHOW COLUMNS FROM $selectedTable");
            while($col=mysqli_fetch_assoc($res)){
                $columns[] = $col['Field'] . " LIKE '%$search%'";
            }
            $sql .= implode(" OR ", $columns);
        }

        $result = mysqli_query($conn, $sql);
        if($result && mysqli_num_rows($result)>0){
            if($role=='admin') {
    echo '<a class="add-btn" href="dashboard.php?table='.$selectedTable.'&action=add">Add New '.ucfirst($selectedTable).'</a>';
}

            echo '<table><tr>';
            while($fieldinfo=mysqli_fetch_field($result)) {
                echo '<th>'.htmlspecialchars($fieldinfo->name).'</th>';
            }
            if($role=='admin') echo '<th>Actions</th>';
            echo '</tr>';

            while($row=mysqli_fetch_assoc($result)) {
                echo '<tr>';
                foreach($row as $val) echo '<td>'.htmlspecialchars($val).'</td>';
                if($role=='admin') {
                    echo '<td>
                        <a class="edit-btn" href="dashboard.php?table='.$selectedTable.'&action=edit&id='.$row[array_keys($row)[0]].'">Edit</a>

                        <a class="delete-btn" href="dashboard.php?table='.$selectedTable.'&action=delete&id='.$row[array_keys($row)[0]].'" onclick="return confirm(\'Are you sure?\')">Delete</a>

                    </td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo "<p>No records found in ".htmlspecialchars($selectedTable)."</p>";
        }
    }
    ?>

    <!-- Reports Section -->
    <div class="reports">
        <h3>Reports</h3>
        <?php foreach($reports as $rname=>$rpage): ?>
            <a href="<?php echo $rpage; ?>"><?php echo htmlspecialchars($rname); ?></a>
        <?php endforeach; ?>
    </div>

</div>

<a class="logout" href="logout.php">Logout</a>

</body>
</html>
