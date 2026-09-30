<?php

session_start();

require_once "config/database.php";

$cart = $_SESSION["cart"] ?? [];

$items = [];
$total = 0;

if (!empty($cart)) {

    $ids = array_keys($cart);

    $placeholders = implode(",", array_fill(0, count($ids), "?"));

    $stmt = $pdo->prepare("
        SELECT id, name, price, image
        FROM menu_items
        WHERE id IN ($placeholders)
    ");

    $stmt->execute($ids);

    $items = $stmt->fetchAll();

    foreach ($items as &$item) {

        $item["quantity"] = $cart[$item["id"]] ?? 0;

        $item["subtotal"] =
            $item["price"] * $item["quantity"];

        $total += $item["subtotal"];
    }

    unset($item);
}

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";

unset($_SESSION["success"], $_SESSION["error"]);

require_once "includes/header.php";
?>

<main class="container py-5 cart-page">

    <div class="text-center mb-5">
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>

        <div class="text-center py-5">

            <i class="bi bi-cart-x display-1"></i>

            <h3 class="mt-3">
                Your cart is empty
            </h3>

            <p>
                Add some delicious food from our menu.
            </p>

            <a href="menu.php" class="btn btn-primary">
                Browse Menu
            </a>

        </div>

    <?php else: ?>

        <form action="cart/update.php" method="POST">

            <div class="table-responsive cart-table-wrap">

                <table class="table align-middle cart-table">

                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th width="150">Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($items as $item): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($item["name"]) ?>
                                </strong>
                            </td>

                            <td>
                                Rs. <?= number_format($item["price"], 2) ?>
                            </td>

                            <td>

                                <input
                                    type="number"
                                    class="form-control"
                                    name="quantity[<?= $item["id"] ?>]"
                                    value="<?= $item["quantity"] ?>"
                                    min="1"
                                >

                            </td>

                            <td>
                                Rs.
                                <?= number_format($item["subtotal"], 2) ?>
                            </td>

                            <td>

                                <a
                                    href="cart/remove.php?id=<?= $item["id"] ?>"
                                    class="btn btn-sm btn-danger"
                                >
                                    <i class="bi bi-trash"></i>
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 cart-summary">

                <button
                    type="submit"
                    class="btn btn-secondary"
                >
                    Update Cart
                </button>

                <div class="text-end">

                    <h4>
                        Total:
                        Rs. <?= number_format($total, 2) ?>
                    </h4>

                    <a
                        href="checkout.php"
                        class="btn btn-primary mt-2"
                    >
                        Proceed to Checkout
                    </a>

                </div>

            </div>

        </form>

    <?php endif; ?>

</main>

<?php require_once "includes/footer.php"; ?>
