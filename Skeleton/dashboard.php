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

// Handle table selection
$selectedTable = $_GET['table'] ?? '';
$search = $_GET['search'] ?? '';
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
                echo '<a class="add-btn" href="add_'.$selectedTable.'.php">Add New '.ucfirst($selectedTable).'</a>';
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
                        <a class="edit-btn" href="edit_'.$selectedTable.'.php?id='.$row[array_keys($row)[0]].'">Edit</a>
                        <a class="delete-btn" href="delete_'.$selectedTable.'.php?id='.$row[array_keys($row)[0]].'" onclick="return confirm(\'Are you sure?\')">Delete</a>
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
