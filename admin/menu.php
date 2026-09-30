<?php
require_once "admin_auth_check.php";
require_once "../config/database.php";

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";
unset($_SESSION["success"], $_SESSION["error"]);

$stmt = $pdo->query("SELECT * FROM menu_items ORDER BY is_featured DESC, id DESC");
$items = $stmt->fetchAll();

$categoryNames = [
    "starters" => "Starters",
    "main" => "Main Course",
    "desserts" => "Desserts",
    "drinks" => "Drinks"
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/menu-admin.css">
    <script src="js/theme.js"></script>
</head>

<body>
    <div class="admin-layout">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <?php require_once "includes/sidebar.php"; ?>
        <main class="main-content">
            <div class="container-fluid p-4">
                <div class="page-heading users-page-heading">
                    <div>
                        <p class="gold-label">BITE &amp; BLISS</p>
                        <h1>Menu Management</h1>
                        <p>Add, edit, remove and publish menu items for the customer menu page.</p>
                    </div>
                    <a href="menu_add.php" class="gold-btn"><i class="bi bi-plus-lg"></i> Add Menu Item</a>
                </div>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <div class="dashboard-card contact-messages-card">
                    <div class="card-header-custom">
                        <div>
                            <h5>All Menu Items</h5>
                            <p>Only active items are shown on the public menu page.</p>
                        </div><span class="message-count"><?= count($items) ?> <?= count($items) === 1 ? 'Item' : 'Items' ?></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table restaurant-table menu-admin-table">
                            <thead>
                                <tr>
                                    <th>IMAGE</th>
                                    <th>NAME</th>
                                    <th>CATEGORY</th>
                                    <th>PRICE</th>
                                    <th>STATUS</th>
                                    <th>FEATURED</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$items): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">No menu items yet. Click <strong>Add Menu Item</strong> to create your first item.</td>
                                    </tr>
                                    <?php else: foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="menu-admin-thumb"><?php if (!empty($item['image'])): ?><img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"><?php else: ?><i class="bi bi-image"></i><?php endif; ?></div>
                                            </td>
                                            <td><strong><?= htmlspecialchars($item['name']) ?></strong><small><?= htmlspecialchars(mb_strimwidth($item['description'] ?? '', 0, 55, '...')) ?></small></td>
                                            <td><?= htmlspecialchars($categoryNames[$item['category']] ?? ucfirst($item['category'])) ?></td>
                                            <td>Rs. <?= number_format((float)$item['price'], 2) ?></td>
                                            <td><span class="menu-status <?= $item['status'] ? 'active' : 'inactive' ?>"><?= $item['status'] ? 'Active' : 'Inactive' ?></span></td>
                                            <td><?= $item['is_featured'] ? '<span class="menu-featured"><i class="bi bi-star-fill"></i> Yes</span>' : 'No' ?></td>
                                            <td>
                                                <div class="menu-actions"><a href="menu_edit.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil"></i></a><a href="menu_delete.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this menu item?');"><i class="bi bi-trash"></i></a></div>
                                            </td>
                                        </tr>
                                <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="js/admin.js"></script>
</body>

</html>