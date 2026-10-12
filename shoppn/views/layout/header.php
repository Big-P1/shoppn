<?php
require_once __DIR__ . '/../../core/core.php';

$pageTitle = isset($pageTitle) ? $pageTitle . ' | Shoppn' : 'Shoppn';
$searchValue = isset($_GET['user_query']) ? trim($_GET['user_query']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a class="logo" href="<?php echo BASE_URL; ?>/index.php">Shoppn</a>

        <form class="search-form" action="<?php echo BASE_URL; ?>/views/search_results.php" method="GET">
            <input
                type="search"
                name="user_query"
                placeholder="Search products"
                value="<?php echo htmlspecialchars($searchValue); ?>"
                aria-label="Search products"
            >
            <button type="submit">Search</button>
        </form>

        <nav class="main-nav">
            <a href="<?php echo BASE_URL; ?>/index.php">Home</a>
            <a href="<?php echo BASE_URL; ?>/views/all_products.php">All Products</a>

            <?php if (is_admin()) { ?>
                <a href="<?php echo BASE_URL; ?>/views/admin/brand.php">Brand</a>
                <a href="<?php echo BASE_URL; ?>/views/admin/category.php">Category</a>
                <a href="<?php echo BASE_URL; ?>/views/admin/product.php">Product</a>
            <?php } ?>

            <?php if (!is_logged_in()) { ?>
                <a href="<?php echo BASE_URL; ?>/views/register.php">Register</a>
                <a href="<?php echo BASE_URL; ?>/views/login.php">Login</a>
            <?php } else { ?>
                <span class="welcome">Welcome, <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'Customer'); ?></span>
                <a href="<?php echo BASE_URL; ?>/views/account/my_account.php">My Account</a>
                <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
            <?php } ?>
        </nav>
    </div>
</header>

<div class="wrap">
