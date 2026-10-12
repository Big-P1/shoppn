<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();
$brands = $controller->getAllBrands();

$editBrand = null;

if (isset($_GET['edit_id'])) {
    $editId = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($editId !== false) {
        $editBrand = $controller->getBrandById($editId);

        if (!$editBrand) {
            $_SESSION['error'] = 'Brand not found.';
        }
    }
}

$pageTitle = 'Manage Brands';
include __DIR__ . '/../layout/header.php';
?>

<div class="admin-page">
    <h2>Manage Brands</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <h3><?php echo $editBrand ? 'Edit Brand' : 'Add Brand'; ?></h3>

    <form
        class="inline-form"
        action="<?php echo BASE_URL; ?>/actions/<?php echo $editBrand ? 'update_brand_action.php' : 'add_brand_action.php'; ?>"
        method="POST"
        data-name-field="brand_name"
        data-name-label="Brand name"
    >
        <?php if ($editBrand) { ?>
            <input type="hidden" name="brand_id" value="<?php echo (int) $editBrand['brand_id']; ?>">
        <?php } ?>

        <div class="field">
            <label for="brand_name">Brand Name</label>
            <input
                type="text"
                id="brand_name"
                name="brand_name"
                maxlength="100"
                value="<?php echo $editBrand ? htmlspecialchars($editBrand['brand_name']) : ''; ?>"
                required
            >
            <span class="field-error" data-error-for="brand_name"></span>
        </div>

        <div class="actions">
            <button type="submit" data-loading="Saving..."><?php echo $editBrand ? 'Update Brand' : 'Add Brand'; ?></button>

            <?php if ($editBrand) { ?>
                <a href="<?php echo BASE_URL; ?>/views/admin/brand.php">Cancel</a>
            <?php } ?>
        </div>
    </form>

    <h3>Existing Brands</h3>

    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Brand Name</th>
            <th>Action</th>
        </tr>

        <?php foreach ($brands as $row) { ?>
            <tr>
                <td><?php echo (int) $row['brand_id']; ?></td>
                <td><?php echo htmlspecialchars($row['brand_name']); ?></td>
                <td>
                    <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/admin/brand.php?edit_id=<?php echo (int) $row['brand_id']; ?>">Edit</a>
                </td>
            </tr>
        <?php } ?>

        <?php if (empty($brands)) { ?>
            <tr><td colspan="3" class="muted">No brands yet.</td></tr>
        <?php } ?>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
