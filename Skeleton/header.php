<!-- <?php
// session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<style>
/* ---------- HEADER ---------- */
.header-bar {
    background: #fff;
    padding: 15px 25px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: "Poppins", sans-serif;
    box-shadow: 0px 2px 4px rgba(0,0,0,0.05);
    border-radius: 0 0 8px 8px;
}

.header-info {
    font-size: 16px;
    color: #2d3436;
}

.header-info strong {
    margin-right: 5px;
    color: #6c5ce7; /* مثل لون الأزرار */
}

.header-buttons a {
    text-decoration: none;
    margin-left: 10px;
    padding: 8px 16px;
    background: #6c5ce7;
    color: white;
    border-radius: 5px;
    font-weight: 500;
    transition: background 0.3s;
}

.header-buttons a:hover {
    background: #a29bfe;
}
</style>

<div class="header-bar">
    <div class="header-info">
        <strong><?php echo htmlspecialchars($username); ?></strong>
        (<?php echo htmlspecialchars($role); ?>)
    </div>
    <div class="header-buttons">
<a href="dashboard.php?table=<?php echo $_SESSION['last_table'] ?? ''; ?>">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div> -->


<?php
// تحقق إذا الجلسة بدأت مسبقًا
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// تحقق إذا المستخدم مسجل دخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<style>
/* ---------- HEADER ---------- */
.header-bar {
    background: #fff;
    padding: 15px 25px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-family: "Poppins", sans-serif;
    box-shadow: 0px 2px 4px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    z-index: 100;
}

.header-info strong {
    margin-right: 5px;
    color: #333;
}

.header-buttons a {
    text-decoration: none;
    margin-left: 10px;
    padding: 8px 16px;
    background: #6c5ce7;
    color: white;
    border-radius: 5px;
    font-weight: 500;
    transition: background 0.3s;
}

.header-buttons a:hover {
    background: #a29bfe;
}
</style>

<div class="header-bar">
    <div class="header-info">
        <strong><?= htmlspecialchars($username); ?></strong> (<?= htmlspecialchars($role); ?>)
    </div>
    <div class="header-buttons">
        <a href="dashboard.php?table=<?= $_SESSION['last_table'] ?? ''; ?>">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</div>
