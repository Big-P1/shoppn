<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarController = new ProductController();
$sidebarCategories = $sidebarController->getAllCategories();
$sidebarBrands = $sidebarController->getAllBrands();
?>
<aside class="sidebar">
    <h3>Categories</h3>
    <ul>
        <?php foreach ($sidebarCategories as $cat) { ?>
            <li>
                <a href="<?php echo BASE_URL; ?>/index.php?cat=<?php echo (int) $cat['cat_id']; ?>">
                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                </a>
            </li>
        <?php } ?>
        <?php if (empty($sidebarCategories)) { ?>
            <li class="muted">No categories yet</li>
        <?php } ?>
    </ul>

    <h3>Brands</h3>
    <ul>
        <?php foreach ($sidebarBrands as $brand) { ?>
            <li>
                <a href="<?php echo BASE_URL; ?>/index.php?brand=<?php echo (int) $brand['brand_id']; ?>">
                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                </a>
            </li>
        <?php } ?>
        <?php if (empty($sidebarBrands)) { ?>
            <li class="muted">No brands yet</li>
        <?php } ?>
    </ul>
</aside>
