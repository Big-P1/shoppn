<?php
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

require_admin();

$controller = new ProductController();
$categories = $controller->getAllCategories();
$brands = $controller->getAllBrands();
$products = $controller->getAllProducts();

$editProduct = null;

if (isset($_GET['edit_id'])) {
    $editId = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($editId !== false) {
        $editProduct = $controller->getProductById($editId);

        if (!$editProduct) {
            $_SESSION['error'] = 'Product not found.';
        }
    }
}

$pageTitle = 'Manage Products';
include __DIR__ . '/../layout/header.php';
?>

<div class="admin-page">
    <h2>Manage Products</h2>

    <?php include __DIR__ . '/../layout/flash.php'; ?>

    <div class="form-card wide" style="margin: 0 0 28px;">
        <h3 style="margin-top: 0;"><?php echo $editProduct ? 'Edit Product' : 'Add Product'; ?></h3>

        <form
            id="product-form"
            action="<?php echo BASE_URL; ?>/actions/<?php echo $editProduct ? 'update_product_action.php' : 'add_product_action.php'; ?>"
            method="POST"
            enctype="multipart/form-data"
            data-image-required="<?php echo $editProduct ? '0' : '1'; ?>"
        >
            <?php if ($editProduct) { ?>
                <input type="hidden" name="product_id" value="<?php echo (int) $editProduct['product_id']; ?>">
                <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editProduct['product_image'] ?? ''); ?>">
            <?php } ?>

            <div class="field">
                <label for="title">Product Title</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    maxlength="200"
                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['product_title']) : ''; ?>"
                    required
                >
                <span class="field-error" data-error-for="title"></span>
            </div>

            <div class="field">
                <label for="cat_id">Category</label>
                <select id="cat_id" name="cat_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat) { ?>
                        <option
                            value="<?php echo (int) $cat['cat_id']; ?>"
                            <?php echo ($editProduct && (int) $editProduct['product_cat'] === (int) $cat['cat_id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($cat['cat_name']); ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="field-error" data-error-for="cat_id"></span>
            </div>

            <div class="field">
                <label for="brand_id">Brand</label>
                <select id="brand_id" name="brand_id" required>
                    <option value="">Select brand</option>
                    <?php foreach ($brands as $brand) { ?>
                        <option
                            value="<?php echo (int) $brand['brand_id']; ?>"
                            <?php echo ($editProduct && (int) $editProduct['product_brand'] === (int) $brand['brand_id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($brand['brand_name']); ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="field-error" data-error-for="brand_id"></span>
            </div>

            <div class="field">
                <label for="price">Price (GH&#8373;)</label>
                <input
                    type="text"
                    inputmode="decimal"
                    id="price"
                    name="price"
                    value="<?php echo $editProduct ? htmlspecialchars(number_format((float) $editProduct['product_price'], 2, '.', '')) : ''; ?>"
                    required
                >
                <span class="field-error" data-error-for="price"></span>
            </div>

            <div class="field">
                <label for="desc">Description</label>
                <textarea id="desc" name="desc" maxlength="500"><?php echo $editProduct ? htmlspecialchars($editProduct['product_desc'] ?? '') : ''; ?></textarea>
                <span class="field-error" data-error-for="desc"></span>
            </div>

            <div class="field">
                <label for="keywords">Keywords</label>
                <input
                    type="text"
                    id="keywords"
                    name="keywords"
                    maxlength="100"
                    value="<?php echo $editProduct ? htmlspecialchars($editProduct['product_keywords'] ?? '') : ''; ?>"
                >
                <span class="field-hint">Comma separated, used by search.</span>
                <span class="field-error" data-error-for="keywords"></span>
            </div>

            <div class="field">
                <label for="product_image">Product Image<?php echo $editProduct ? ' (leave empty to keep current)' : ''; ?></label>

                <?php $currentImage = $editProduct['product_image'] ?? ''; ?>

                <img
                    id="image-preview"
                    class="current-image"
                    <?php echo $currentImage === '' ? 'hidden' : 'src="' . BASE_URL . '/images/products/' . rawurlencode($currentImage) . '"'; ?>
                    alt="Product image preview"
                >

                <input
                    type="file"
                    id="product_image"
                    name="product_image"
                    accept="image/jpeg,image/png,image/gif,image/webp"
                    <?php echo $editProduct ? '' : 'required'; ?>
                >
                <span class="field-hint">JPG, PNG, GIF or WEBP, up to 2MB.</span>
                <span class="field-error" data-error-for="product_image"></span>
            </div>

            <button type="submit" data-loading="Saving..."><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?></button>

            <?php if ($editProduct) { ?>
                <a href="<?php echo BASE_URL; ?>/views/admin/product.php">Cancel</a>
            <?php } ?>
        </form>
    </div>

    <h3>Existing Products</h3>

    <table class="data-table">
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Price</th>
            <th>Action</th>
        </tr>

        <?php foreach ($products as $row) { ?>
            <tr>
                <td>
                    <?php if (!empty($row['product_image'])) { ?>
                        <img class="thumb" src="<?php echo BASE_URL; ?>/images/products/<?php echo rawurlencode($row['product_image']); ?>" alt="">
                    <?php } ?>
                </td>
                <td><?php echo htmlspecialchars($row['product_title']); ?></td>
                <td><?php echo htmlspecialchars($row['cat_name']); ?></td>
                <td><?php echo htmlspecialchars($row['brand_name']); ?></td>
                <td>GH&#8373; <?php echo number_format((float) $row['product_price'], 2); ?></td>
                <td>
                    <a class="btn btn-outline" href="<?php echo BASE_URL; ?>/views/admin/product.php?edit_id=<?php echo (int) $row['product_id']; ?>">Edit</a>
                </td>
            </tr>
        <?php } ?>

        <?php if (empty($products)) { ?>
            <tr><td colspan="6" class="muted">No products yet.</td></tr>
        <?php } ?>
    </table>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
