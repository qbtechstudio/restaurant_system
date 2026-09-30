<?php
require_once "admin_auth_check.php";
require_once "../config/database.php";

$categories = ["starters" => "Starters", "main" => "Main Course", "desserts" => "Desserts", "drinks" => "Drinks"];
$error = "";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: menu.php");
    exit;
}
$find = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
$find->execute([$id]);
$item = $find->fetch();
if (!$item) {
    $_SESSION["error"] = "Menu item not found.";
    header("Location: menu.php");
    exit;
}
$name = $item["name"];
$category = $item["category"];
$description = $item["description"] ?? "";
$price = $item["price"];
$status = (int)$item["status"];
$is_featured = (int)$item["is_featured"];
$oldImage = $item["image"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $category = $_POST["category"] ?? "";
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $status = isset($_POST["status"]) ? 1 : 0;
    $is_featured = isset($_POST["is_featured"]) ? 1 : 0;

    if ($name === "") $error = "Please enter the menu item name.";
    elseif (!array_key_exists($category, $categories)) $error = "Please select a valid category.";
    elseif ($price === "" || !is_numeric($price) || (float)$price < 0) $error = "Please enter a valid price.";

    $imagePath = $oldImage;
    if ($error === "" && isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) $error = "Image upload failed.";
        elseif ($_FILES["image"]["size"] > 3 * 1024 * 1024) $error = "Image must be 3MB or smaller.";
        else {
            $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "webp" => "image/webp"];
            $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $mime = mime_content_type($_FILES["image"]["tmp_name"]);
            if (!isset($allowed[$ext]) || $allowed[$ext] !== $mime) $error = "Only JPG, PNG or WEBP images are allowed.";
            else {
                $uploadDir = __DIR__ . "/uploads/menu/";
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $filename = bin2hex(random_bytes(12)) . "." . $ext;
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $uploadDir . $filename)) $error = "Could not save the image.";
                else $imagePath = "uploads/menu/" . $filename;
            }
        }
    }

    if ($error === "") {
        if ($is_featured) $pdo->exec("UPDATE menu_items SET is_featured = 0 WHERE id <> " . (int)$id);
        $stmt = $pdo->prepare("UPDATE menu_items SET name=:name, category=:category, description=:description, price=:price, image=:image, status=:status, is_featured=:featured WHERE id=:id");
        $stmt->execute([":name" => $name, ":category" => $category, ":description" => $description ?: null, ":price" => (float)$price, ":image" => $imagePath, ":status" => $status, ":featured" => $is_featured, ":id" => $id]);
        if ($imagePath !== $oldImage && $oldImage && strpos($oldImage, "uploads/menu/") === 0) {
            @unlink(__DIR__ . "/" . $oldImage);
        }
        $_SESSION["success"] = "Menu item updated successfully.";
        header("Location: menu.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/menu-admin.css">
    <script src="js/theme.js"></script>
</head>

<body>
    <div class="admin-layout">
        <div class="sidebar-overlay" id="sidebarOverlay"></div><?php require_once "includes/sidebar.php"; ?><main class="main-content">
            <div class="container-fluid p-4">
                <div class="page-heading users-page-heading">
                    <div>
                        <p class="gold-label">BITE &amp; BLISS</p>
                        <h1>Edit Menu Item</h1>
                        <p>Update this menu item and its public visibility.</p>
                    </div><a href="menu.php" class="gold-btn"><i class="bi bi-arrow-left"></i> Back to Menu</a>
                </div><?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?><div class="dashboard-card user-form-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>Menu Information</h5>
                            <p>Enter the dish details below.</p>
                        </div>
                    </div>
                    <form method="POST" enctype="multipart/form-data"><input type="hidden" name="id" value="<?= (int)$id ?>">
                        <div class="row g-4">
                            <div class="col-md-6"><label class="form-label">Dish Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" maxlength="150" required></div>
                            <div class="col-md-3"><label class="form-label">Category</label><select name="category" class="form-select" required><?php foreach ($categories as $key => $label): ?><option value="<?= $key ?>" <?= $category === $key ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-3"><label class="form-label">Price (Rs.)</label><input type="number" name="price" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars($price) ?>" required></div>
                            <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4" maxlength="1000" placeholder="Describe the dish..."><?= htmlspecialchars($description) ?></textarea></div>
                            <div class="col-md-7"><label class="form-label">Dish Image</label><?php if ($oldImage): ?><div class="current-menu-image"><img src="<?= htmlspecialchars($oldImage) ?>" alt="Current image"></div><?php endif; ?><input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text">JPG, PNG or WEBP. Maximum 3MB.</div>
                            </div>
                            <div class="col-md-5">
                                <div class="menu-checks"><label><input type="checkbox" name="status" <?= $status ? 'checked' : '' ?>> <span>Publish / Active</span></label><label><input type="checkbox" name="is_featured" <?= $is_featured ? 'checked' : '' ?>> <span>Make Chef's Special</span></label></div>
                            </div>
                        </div>
                        <div class="form-actions"><button type="submit" class="btn-submit"><i class="bi bi-check2"></i> Save Changes</button><a href="menu.php" class="btn-cancel">Cancel</a></div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/admin.js"></script>
</body>

</html>