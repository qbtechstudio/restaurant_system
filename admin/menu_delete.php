<?php
require_once "admin_auth_check.php";
require_once "../config/database.php";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($id) {
    $stmt = $pdo->prepare("SELECT image FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if ($item) {
        $del = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
        $del->execute([$id]);
        if (!empty($item["image"]) && strpos($item["image"], "uploads/menu/") === 0) @unlink(__DIR__ . "/" . $item["image"]);
        $_SESSION["success"] = "Menu item deleted successfully.";
    } else $_SESSION["error"] = "Menu item not found.";
}
header("Location: menu.php");
exit;
