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

// Tables to show
$tables = ['book', 'author', 'publisher', 'borrower', 'loan', 'sale'];

// MODE: control screen content
$mode = $_GET['mode'] ?? '';
$r = $_GET['r'] ?? '';   // report type
$selectedTable = $_GET['table'] ?? '';
if ($selectedTable) {
    $_SESSION['last_table'] = $selectedTable;
}


$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="dashboard-container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>Dashboard</h2>

            <h3>Tables</h3>
            <ul>
                <?php foreach ($tables as $table): ?>
                    <li>
                        <a href="dashboard.php?table=<?php echo $table; ?>">
                            <?php echo ucfirst($table); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h3>Reports</h3>
            <ul>
                <li><a href="dashboard.php?mode=reports">Reports Dashboard</a></li>
            </ul>

            <h3>About Us</h3>
            <ul>
                <li><a href="dashboard.php?mode=about">About Our System</a></li>
            </ul>

            <a class="logout-btn" href="logout.php">Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="main-content">

            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

            <!-- HANDLE ABOUT US -->
            <?php if ($mode == 'about'): ?>
                <div class="about-box">
                    <h2>About Our Library System</h2>
                    <p>
                        This Library Management Dashboard allows admins to manage books, authors,
                        borrowers, loans, and sales within a simple and user-friendly interface.
                    </p>
                    <p>
                        The system is designed for small libraries that need effective tools
                        for tracking inventory, transactions, and generating reports.
                    </p>
                    <p>
                        The dashboard demonstrates PHP, MySQL, and modern UI/UX design principles.
                    </p>
                </div>
            <?php endif; ?>


            <!-- HANDLE REPORTS -->
            <?php if ($mode == 'reports'): ?>
                <div class="reports-box">
                    <h2>Reports Dashboard</h2>
                    <div class="report-links">
                        <a href="dashboard.php?mode=reports&r=total_value">Total Value</a>
                        <a href="dashboard.php?mode=reports&r=available_books">Available Books</a>
                        <a href="dashboard.php?mode=reports&r=borrower_books">Borrower Loan/Sale</a>
                        <a href="dashboard.php?mode=reports&r=books_country">Books by Country</a>
                        <a href="dashboard.php?mode=reports&r=never_borrowed">Never Borrowed/Bought</a>
                        <a href="dashboard.php?mode=reports&r=multiple_authors">Multiple Authors</a>
                        <a href="dashboard.php?mode=reports&r=sold_books">Sold Books</a>
                        <a href="dashboard.php?mode=reports&r=current_loans">Current Loans</a>
                        <a href="dashboard.php?mode=reports&r=borrower_history">Borrower History</a>
                        <a href="dashboard.php?mode=reports&r=borrowed_range">Borrowed by Date</a>
                    </div>


                    <div class="report-output">
                        <?php

                        function fetchAll($conn, $sql)
                        {
                            $result = mysqli_query($conn, $sql);
                            if (!$result)
                                return [];
                            $rows = [];
                            while ($row = mysqli_fetch_assoc($result))
                                $rows[] = $row;
                            return $rows;
                        }

                        $r = $_GET['r'] ?? '';

                        if ($r == 'total_value') {
                            $rows = fetchAll($conn, "SELECT SUM(original_price) AS total FROM book");
                            echo "<h3>Total Book Value: {$rows[0]['total']} USD</h3>";
                        } elseif ($r == 'available_books') {
                            $rows = fetchAll($conn, "SELECT * FROM book WHERE available = 1");
                            echo "<table><tr><th>Title</th><th>Category</th></tr>";
                            foreach ($rows as $b) {
                                echo "<tr><td>{$b['title']}</td><td>{$b['category']}</td></tr>";
                            }
                            echo "</table>";
                        } elseif ($r == 'borrower_books') {
                            echo "<h3>Select a borrower from the Borrower table to view their loans or purchases.</h3>";
                        } elseif ($r == 'books_country') {
                            if (!isset($_GET['country'])) {
                                echo '<form method="get">
                                   <input type="hidden" name="mode" value="reports">
                                  <input type="hidden" name="r" value="books_country">
                                  <input name="country" placeholder="Country" required>
                                   <button type="submit">Filter</button>
                                  </form>';
                            } else {
                                $country = mysqli_real_escape_string($conn, $_GET['country']);
                                $rows = fetchAll($conn, "SELECT * FROM book WHERE country='$country'");
                                echo "<table><tr><th>Title</th><th>Country</th></tr>";
                                foreach ($rows as $b)
                                    echo "<tr><td>{$b['title']}</td><td>{$b['country']}</td></tr>";
                                echo "</table>";
                            }
                        } elseif ($r == 'never_borrowed') {
                            $rows = fetchAll($conn, "
                            SELECT br.*
                            FROM borrower br
                            LEFT JOIN loan l ON br.borrower_id = l.borrower_id
                            LEFT JOIN sale s ON br.borrower_id = s.borrower_id
                            WHERE l.loan_id IS NULL AND s.sale_id IS NULL
                              ");

                            echo "<table><tr>";
                            foreach ($rows[0] as $col => $v)
                                echo "<th>$col</th>";
                            echo "</tr>";

                            foreach ($rows as $b) {
                                echo "<tr>";
                                foreach ($b as $v)
                                    echo "<td>$v</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        }
                        ?>

                    </div>

                </div>
            <?php endif; ?>


            <!-- SHOW TABLE VIEWER IF NOT IN MODE -->
            <?php if (!$mode): ?>
                <div class="table-view-section">
                    <form method="get">
                        <input type="hidden" name="table" value="<?php echo htmlspecialchars($selectedTable); ?>">

                        <input type="text" name="search" placeholder="Search..."
                            value="<?php echo htmlspecialchars($search); ?>">

                        <button type="submit">Search</button>
                    </form>

                </div>
            <?php endif; ?>


            <!-- SHOW TABLE RESULTS -->
            <?php
            if ($selectedTable && !$mode) {

                // base select
                $sql = "SELECT * FROM $selectedTable";

                // search
                if ($search) {
                    $sql .= " WHERE ";
                    $cols = [];

                    $colResult = mysqli_query($conn, "SHOW COLUMNS FROM `$selectedTable`");
                    while ($col = mysqli_fetch_assoc($colResult)) {
                        $field = $col['Field'];
                        $type = $col['Type'];

                        // Numeric columns → CAST
                        if (preg_match('/int|decimal|float|double/', $type)) {
                            $cols[] = "CAST(`$field` AS CHAR) LIKE '%$search%'";
                        }
                        // Text columns
                        else {
                            $cols[] = "`$field` LIKE '%$search%'";
                        }
                    }

                    // Join with OR
                    $sql .= implode(" OR ", $cols);
                }


                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {

                    if ($role == 'admin') {
                        echo '<a class="add-btn" href="add_' . $selectedTable . '.php">Add New ' . ucfirst($selectedTable) . '</a>';
                    }

                    echo '<table><tr>';
                    while ($field = mysqli_fetch_field($result)) {
                        echo '<th>' . htmlspecialchars($field->name) . '</th>';
                    }
                    if ($role == 'admin')
                        echo '<th>Actions</th>';
                    echo '</tr>';

                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        foreach ($row as $value) {
                            echo '<td>' . htmlspecialchars($value) . '</td>';
                        }

                        if ($role == 'admin') {
                            $pk = array_keys($row)[0];
                            echo '<td>
                                <a href="edit_' . $selectedTable . '.php?id=' . $row[$pk] . '">Edit</a> |
                                <a href="delete_' . $selectedTable . '.php?id=' . $row[$pk] . '" onclick="return confirm(\'Delete?\')">Delete</a>
                              </td>';
                        }

                        echo '</tr>';
                    }

                    echo '</table>';

                } else {
                    echo "<p>No records found for <strong>$selectedTable</strong></p>";
                }
            }
            ?>

        </div> <!-- main-content -->

    </div> <!-- container -->

</body>

</html>