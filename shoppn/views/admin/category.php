<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();
$categories = $controller->getAllCategories();

$editCategory = null;

if (isset($_GET['edit_id'])) {
    $editId = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($editId !== false) {
        $editCategory = $controller->getCategoryById($editId);

        if (!$editCategory) {
            $_SESSION['error'] = 'Category not found.';
        }
    }
}

$pageTitle = 'Manage Categories';
include __DIR__ . '/../layout/header.php';
?>

<div class="admin-page">
    <h2>Manage Categories</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <h3><?php echo $editCategory ? 'Edit Category' : 'Add Category'; ?></h3>

    <form
        class="inline-form"
        action="<?php echo BASE_URL; ?>/actions/<?php echo $editCategory ? 'update_category_action.php' : 'add_category_action.php'; ?>"
        method="POST"
        data-name-field="cat_name"
        data-name-label="Category name"
    >
        <?php if ($editCategory) { ?>
            <input type="hidden" name="cat_id" value="<?php echo (int) $editCategory['cat_id']; ?>">
        <?php } ?>

        <div class="field">
            <label for="cat_name">Category Name</label>
            <input
                type="text"
                id="cat_name"
                name="cat_name"
                maxlength="100"
                value="<?php echo $editCategory ? htmlspecialchars($editCategory['cat_name']) : ''; ?>"
                required
            >
            <span class="field-error" data-error-for="cat_name"></span>
        </div>

        <div class="actions">
            <button type="submit" data-loading="Saving..."><?php echo $editCategory ? 'Update Category' : 'Add Category'; ?></button>

            <?php if ($editCategory) { ?>
                <a href="<?php echo BASE_URL; ?>/views/admin/category.php">Cancel</a>
            <?php } ?>
        </div>
    </form>

    <h3>Existing Categories</h3>

    <table class="data-table">
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>

        <?php foreach ($categories as $row) { ?>
            <tr>
                <td><?php echo (int) $row['cat_id']; ?></td>
                <td><?php echo htmlspecialchars($row['cat_name']); ?></td>
                <td>
                    <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/admin/category.php?edit_id=<?php echo (int) $row['cat_id']; ?>">Edit</a>
                </td>
            </tr>
        <?php } ?>

        <?php if (empty($categories)) { ?>
            <tr><td colspan="3" class="muted">No categories yet.</td></tr>
        <?php } ?>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
