<?php
require_once __DIR__ . '/../../core/core.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shoppn</title>
</head>
<body>

<header>
    <h2>Shoppn</h2>

    <nav>
        <a href="../index.php">Home</a>

        <?php if (!is_logged_in()) { ?>
            <a href="../register.php">Register</a>
            <a href="../login.php">Login</a>
        <?php } else { ?>
            <span>Welcome, <?php echo $_SESSION['customer_name']; ?></span>

            <?php if (is_admin()) { ?>
                <a href="../admin/index.php">Admin</a>
            <?php } ?>

            <a href="../actions/logout_action.php">Logout</a>
        <?php } ?>
    </nav>
</header>