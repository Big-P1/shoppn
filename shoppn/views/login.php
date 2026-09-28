<?php
include 'layout/header.php';
?>

<h2>Customer Login</h2>

<?php
if (isset($_GET['error'])) {
    echo "<p>" . $_GET['error'] . "</p>";
}
?>

<form action="../actions/login_action.php" method="POST">
    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

<?php
include 'layout/footer.php';
?>