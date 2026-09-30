<?php

session_start();

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$item_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if ($item_id) {
    unset($_SESSION["cart"][$item_id]);
}

header("Location: ../cart.php");
exit;