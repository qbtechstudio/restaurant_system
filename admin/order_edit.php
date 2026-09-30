<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {

    $_SESSION["error"] = "Invalid order.";
    header("Location: orders.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Order
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $id
]);

$order = $stmt->fetch();

if (!$order) {

    $_SESSION["error"] = "Order not found.";
    header("Location: orders.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| POST UPDATE
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name =
        trim($_POST["full_name"] ?? "");

    $email =
        trim($_POST["email"] ?? "");

    $phone =
        trim($_POST["phone"] ?? "");

    $order_type =
        trim($_POST["order_type"] ?? "");

    $table_number =
        trim($_POST["table_number"] ?? "");

    $delivery_address =
        trim($_POST["delivery_address"] ?? "");

    $special_request =
        trim($_POST["special_request"] ?? "");

    $status =
        trim($_POST["status"] ?? "");

    $quantities =
        $_POST["quantity"] ?? [];


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if (
        $full_name === "" ||
        $email === "" ||
        $phone === ""
    ) {

        $_SESSION["error"] =
            "Name, email and phone are required.";

        header(
            "Location: order_edit.php?id=" . $id
        );

        exit;
    }


    $allowedTypes = [
        "Dine-in",
        "Takeaway",
        "Delivery"
    ];

    $allowedStatuses = [
        "Pending",
        "Confirmed",
        "Preparing",
        "Ready",
        "Completed",
        "Cancelled"
    ];


    if (!in_array($order_type, $allowedTypes, true)) {

        $_SESSION["error"] =
            "Invalid order type.";

        header(
            "Location: order_edit.php?id=" . $id
        );

        exit;
    }


    if (!in_array($status, $allowedStatuses, true)) {

        $_SESSION["error"] =
            "Invalid order status.";

        header(
            "Location: order_edit.php?id=" . $id
        );

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Start Transaction
    |--------------------------------------------------------------------------
    */

    try {

        $pdo->beginTransaction();


        /*
        |--------------------------------------------------------------------------
        | Update Order
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            UPDATE orders
            SET
                full_name = :full_name,
                email = :email,
                phone = :phone,
                order_type = :order_type,
                table_number = :table_number,
                delivery_address = :delivery_address,
                special_request = :special_request,
                status = :status
            WHERE id = :id
        ");

        $stmt->execute([

            ":full_name" =>
                $full_name,

            ":email" =>
                $email,

            ":phone" =>
                $phone,

            ":order_type" =>
                $order_type,

            ":table_number" =>
                $order_type === "Dine-in"
                    ? $table_number
                    : null,

            ":delivery_address" =>
                $order_type === "Delivery"
                    ? $delivery_address
                    : null,

            ":special_request" =>
                $special_request !== ""
                    ? $special_request
                    : null,

            ":status" =>
                $status,

            ":id" =>
                $id
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Quantities
        |--------------------------------------------------------------------------
        */

        foreach ($quantities as $itemId => $quantity) {

            $itemId =
                (int)$itemId;

            $quantity =
                (int)$quantity;


            if ($itemId <= 0) {
                continue;
            }


            /*
            | If quantity is zero, remove item
            */

            if ($quantity <= 0) {

                $delete = $pdo->prepare("
                    DELETE FROM order_items
                    WHERE id = :id
                    AND order_id = :order_id
                ");

                $delete->execute([
                    ":id" => $itemId,
                    ":order_id" => $id
                ]);

                continue;
            }


            /*
            | Get original price
            */

            $priceStmt = $pdo->prepare("
                SELECT item_price
                FROM order_items
                WHERE id = :id
                AND order_id = :order_id
                LIMIT 1
            ");

            $priceStmt->execute([
                ":id" => $itemId,
                ":order_id" => $id
            ]);

            $item =
                $priceStmt->fetch();


            if (!$item) {
                continue;
            }


            $price =
                (float)$item["item_price"];

            $subtotal =
                $price * $quantity;


            /*
            | Update item
            */

            $updateItem = $pdo->prepare("
                UPDATE order_items
                SET
                    quantity = :quantity,
                    subtotal = :subtotal
                WHERE id = :id
                AND order_id = :order_id
            ");

            $updateItem->execute([

                ":quantity" =>
                    $quantity,

                ":subtotal" =>
                    $subtotal,

                ":id" =>
                    $itemId,

                ":order_id" =>
                    $id
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Recalculate Order Total
        |--------------------------------------------------------------------------
        */

        $totalStmt = $pdo->prepare("
            SELECT COALESCE(SUM(subtotal), 0)
            FROM order_items
            WHERE order_id = :order_id
        ");

        $totalStmt->execute([
            ":order_id" => $id
        ]);

        $newTotal =
            (float)$totalStmt->fetchColumn();


        /*
        |--------------------------------------------------------------------------
        | Update Total
        |--------------------------------------------------------------------------
        */

        $totalUpdate = $pdo->prepare("
            UPDATE orders
            SET total_amount = :total
            WHERE id = :id
        ");

        $totalUpdate->execute([

            ":total" =>
                $newTotal,

            ":id" =>
                $id
        ]);


        $pdo->commit();


        $_SESSION["success"] =
            "Order updated successfully.";

        header(
            "Location: order_view.php?id=" . $id
        );

        exit;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        $_SESSION["error"] =
            "Unable to update the order.";

        header(
            "Location: order_edit.php?id=" . $id
        );

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Get Order Items
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = :order_id
    ORDER BY id ASC
");

$stmt->execute([
    ":order_id" => $id
]);

$items = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Edit Order #<?= (int)$order["id"] ?>
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="css/admin.css"
    >

    <script src="js/theme.js"></script>

</head>

<body class="admin-order-edit-page">

<div class="admin-layout">

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main-content">

        <div class="container-fluid p-4">

            <div class="page-heading">

                <div>

                    <p class="gold-label">
                        BITE &amp; BLISS
                    </p>

                    <h1>
                        Edit Order #<?= (int)$order["id"] ?>
                    </h1>

                    <p>
                        Update customer information,
                        order status and quantities.
                    </p>

                </div>

                <a
                    href="order_view.php?id=<?= (int)$order["id"] ?>"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>

            </div>


            <form method="POST">


                <!-- CUSTOMER -->

                <div class="dashboard-card p-4 mb-4 admin-order-form-card">

                    <h5>
                        Customer &amp; Order Information
                    </h5>

                    <hr>

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $order["full_name"]
                                ) ?>"
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $order["email"]
                                ) ?>"
                            >

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $order["phone"]
                                ) ?>"
                            >

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">
                                Order Type
                            </label>

                            <select
                                name="order_type"
                                class="form-select"
                                id="orderType"
                            >

                                <option
                                    value="Dine-in"
                                    <?= $order["order_type"] === "Dine-in"
                                        ? "selected"
                                        : "" ?>
                                >
                                    Dine-in
                                </option>

                                <option
                                    value="Takeaway"
                                    <?= $order["order_type"] === "Takeaway"
                                        ? "selected"
                                        : "" ?>
                                >
                                    Takeaway
                                </option>

                                <option
                                    value="Delivery"
                                    <?= $order["order_type"] === "Delivery"
                                        ? "selected"
                                        : "" ?>
                                >
                                    Delivery
                                </option>

                            </select>

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-select"
                            >

                                <?php

                                $statuses = [
                                    "Pending",
                                    "Confirmed",
                                    "Preparing",
                                    "Ready",
                                    "Completed",
                                    "Cancelled"
                                ];

                                ?>

                                <?php foreach ($statuses as $orderStatus): ?>

                                    <option
                                        value="<?= $orderStatus ?>"
                                        <?= $order["status"] === $orderStatus
                                            ? "selected"
                                            : "" ?>
                                    >
                                        <?= $orderStatus ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <div
                            class="col-md-6"
                            id="tableField"
                        >

                            <label class="form-label">
                                Table Number
                            </label>

                            <input
                                type="text"
                                name="table_number"
                                class="form-control"
                                value="<?= htmlspecialchars(
                                    $order["table_number"] ?? ""
                                ) ?>"
                            >

                        </div>


                        <div
                            class="col-12"
                            id="addressField"
                        >

                            <label class="form-label">
                                Delivery Address
                            </label>

                            <textarea
                                name="delivery_address"
                                class="form-control"
                                rows="3"
                            ><?= htmlspecialchars(
                                $order["delivery_address"] ?? ""
                            ) ?></textarea>

                        </div>


                        <div class="col-12">

                            <label class="form-label">
                                Special Request
                            </label>

                            <textarea
                                name="special_request"
                                class="form-control"
                                rows="3"
                            ><?= htmlspecialchars(
                                $order["special_request"] ?? ""
                            ) ?></textarea>

                        </div>

                    </div>

                </div>


                <!-- ITEMS -->

                <div class="dashboard-card admin-order-items-card">

                    <div class="p-4">

                        <h5>
                            Order Items
                        </h5>

                        <p class="mb-0">
                            Set quantity to 0 to remove an item.
                        </p>

                    </div>


                    <div class="table-responsive">

                        <table class="table admin-order-items-table">

                            <thead>

                                <tr>

                                    <th>
                                        ITEM
                                    </th>

                                    <th>
                                        PRICE
                                    </th>

                                    <th width="150">
                                        QUANTITY
                                    </th>

                                    <th>
                                        SUBTOTAL
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php foreach ($items as $item): ?>

                                <tr>

                                    <td>

                                        <?= htmlspecialchars(
                                            $item["item_name"]
                                        ) ?>

                                    </td>

                                    <td>

                                        Rs.
                                        <?= number_format(
                                            (float)$item["item_price"],
                                            2
                                        ) ?>

                                    </td>

                                    <td>

                                        <input
                                            type="number"
                                            name="quantity[<?= (int)$item["id"] ?>]"
                                            value="<?= (int)$item["quantity"] ?>"
                                            min="0"
                                            class="form-control"
                                        >

                                    </td>

                                    <td>

                                        Rs.
                                        <?= number_format(
                                            (float)$item["subtotal"],
                                            2
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                            <?php if (!$items): ?>

                                <tr>

                                    <td
                                        colspan="4"
                                        class="text-center py-4"
                                    >
                                        No items found.
                                    </td>

                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>


                    <div class="p-4">

                        <button
                            type="submit"
                            class="gold-btn"
                        >
                            <i class="bi bi-check2"></i>
                            Save Order Changes
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </main>

</div>


<script>

const orderType =
    document.getElementById("orderType");

const tableField =
    document.getElementById("tableField");

const addressField =
    document.getElementById("addressField");


function updateOrderFields() {

    if (orderType.value === "Dine-in") {

        tableField.style.display = "block";
        addressField.style.display = "none";

    } else if (orderType.value === "Delivery") {

        tableField.style.display = "none";
        addressField.style.display = "block";

    } else {

        tableField.style.display = "none";
        addressField.style.display = "none";

    }
}


orderType.addEventListener(
    "change",
    updateOrderFields
);

updateOrderFields();

</script>

<script src="js/admin.js"></script>

</body>
</html>
