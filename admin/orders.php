<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Messages
|--------------------------------------------------------------------------
*/

$success = $_SESSION["success"] ?? "";
$error   = $_SESSION["error"] ?? "";

unset($_SESSION["success"], $_SESSION["error"]);

/*
|--------------------------------------------------------------------------
| Search & Filter
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$status = trim($_GET["status"] ?? "");

/*
|--------------------------------------------------------------------------
| Build Query
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id,
        full_name,
        email,
        phone,
        order_type,
        table_number,
        delivery_address,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE 1=1
";

$params = [];

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

if ($search !== "") {

    $sql .= "
        AND (
            full_name LIKE :search
            OR email LIKE :search
            OR phone LIKE :search
            OR CAST(id AS CHAR) LIKE :search
        )
    ";

    $params[":search"] = "%" . $search . "%";
}

/*
|--------------------------------------------------------------------------
| Status Filter
|--------------------------------------------------------------------------
*/

if ($status !== "") {

    $sql .= "
        AND status = :status
    ";

    $params[":status"] = $status;
}

$sql .= "
    ORDER BY id DESC
";

/*
|--------------------------------------------------------------------------
| Get Orders
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$orders = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Count Orders
|--------------------------------------------------------------------------
*/

$totalOrders = (int) $pdo
    ->query("SELECT COUNT(*) FROM orders")
    ->fetchColumn();

$pendingOrders = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM orders
        WHERE status = 'Pending'
    ")
    ->fetchColumn();

$completedOrders = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM orders
        WHERE status = 'Completed'
    ")
    ->fetchColumn();

$totalRevenue = (float) $pdo
    ->query("
        SELECT COALESCE(SUM(total_amount), 0)
        FROM orders
        WHERE status != 'Cancelled'
    ")
    ->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Order Management</title>

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

    <style>

        .order-stat-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid rgba(0,0,0,.08);
            border-radius: 16px;
            padding: 22px;
            height: 100%;
        }

        .order-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            background: rgba(198, 161, 91, .15);
            color: #c6a15b;
        }

        .order-stat-card h3 {
            margin: 12px 0 3px;
            font-size: 28px;
        }

        .order-stat-card p {
            margin: 0;
            opacity: .7;
        }

        .order-filter {
            background: var(--card-bg, #fff);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(0,0,0,.08);
            margin-bottom: 20px;
        }

        .order-table td {
            vertical-align: middle;
        }

        .order-customer strong {
            display: block;
        }

        .order-customer small {
            opacity: .65;
        }

        .order-status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255,193,7,.15);
            color: #b58100;
        }

        .status-confirmed {
            background: rgba(13,110,253,.12);
            color: #0d6efd;
        }

        .status-preparing {
            background: rgba(111,66,193,.12);
            color: #6f42c1;
        }

        .status-ready {
            background: rgba(13,202,240,.15);
            color: #087990;
        }

        .status-completed {
            background: rgba(25,135,84,.13);
            color: #198754;
        }

        .status-cancelled {
            background: rgba(220,53,69,.13);
            color: #dc3545;
        }

        .order-type {
            text-transform: capitalize;
        }

        .order-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 7px;
        }

        .order-actions form {
            margin: 0;
        }

        .order-action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 7px;
            background: transparent;
            font-size: 14px;
            transition: all .2s ease;
        }

        .order-view-btn {
            color: #6cb7ff;
            background: rgba(13, 110, 253, .10);
            border-color: rgba(13, 110, 253, .22);
        }

        .order-view-btn:hover {
            color: #fff !important;
            background: #0d6efd;
            border-color: #0d6efd;
            transform: translateY(-1px);
        }

        .order-edit-btn {
            color: var(--gold);
            background: rgba(212, 175, 55, .10);
            border-color: rgba(212, 175, 55, .22);
        }

        .order-edit-btn:hover {
            color: #fff !important;
            background: var(--gold);
            border-color: var(--gold);
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(212, 175, 55, .28);
        }

        .order-edit-btn:focus-visible {
            color: #17130a;
            outline: 3px solid rgba(212, 175, 55, .35);
            outline-offset: 2px;
        }

        .order-delete-btn {
            color: #ff6b78;
            background: rgba(220, 53, 69, .08);
            border-color: rgba(220, 53, 69, .18);
        }

        .order-delete-btn:hover {
            color: #fff;
            background: #dc3545;
            border-color: #dc3545;
            transform: translateY(-1px);
        }

    </style>

</head>

<body class="admin-orders-page">

<div class="admin-layout">

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main-content">

        <div class="container-fluid p-4">

            <!-- ================================================= -->
            <!-- PAGE HEADING -->
            <!-- ================================================= -->

            <div class="page-heading users-page-heading">

                <div>

                    <p class="gold-label">
                        BITE &amp; BLISS
                    </p>

                    <h1>
                        Order Management
                    </h1>

                    <p>
                        View, manage and update customer orders.
                    </p>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- MESSAGES -->
            <!-- ================================================= -->

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


            <!-- ================================================= -->
            <!-- STATISTICS -->
            <!-- ================================================= -->

            <div class="row g-4 mb-4">

                <div class="col-md-6 col-xl-3">

                    <div class="order-stat-card">

                        <div class="order-stat-icon">
                            <i class="bi bi-bag-check"></i>
                        </div>

                        <h3>
                            <?= $totalOrders ?>
                        </h3>

                        <p>
                            Total Orders
                        </p>

                    </div>

                </div>


                <div class="col-md-6 col-xl-3">

                    <div class="order-stat-card">

                        <div class="order-stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>

                        <h3>
                            <?= $pendingOrders ?>
                        </h3>

                        <p>
                            Pending Orders
                        </p>

                    </div>

                </div>


                <div class="col-md-6 col-xl-3">

                    <div class="order-stat-card">

                        <div class="order-stat-icon">
                            <i class="bi bi-check2-circle"></i>
                        </div>

                        <h3>
                            <?= $completedOrders ?>
                        </h3>

                        <p>
                            Completed Orders
                        </p>

                    </div>

                </div>


                <div class="col-md-6 col-xl-3">

                    <div class="order-stat-card">

                        <div class="order-stat-icon">
                            <i class="bi bi-currency-rupee"></i>
                        </div>

                        <h3>
                            Rs. <?= number_format($totalRevenue, 0) ?>
                        </h3>

                        <p>
                            Total Revenue
                        </p>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- SEARCH / FILTER -->
            <!-- ================================================= -->

            <div class="order-filter">

                <form
                    method="GET"
                    class="row g-3 align-items-end"
                >

                    <div class="col-md-6">

                        <label class="form-label">
                            Search Orders
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by order ID, name, email or phone..."
                            value="<?= htmlspecialchars($search) ?>"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Order Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="Pending"
                                <?= $status === "Pending" ? "selected" : "" ?>
                            >
                                Pending
                            </option>

                            <option
                                value="Confirmed"
                                <?= $status === "Confirmed" ? "selected" : "" ?>
                            >
                                Confirmed
                            </option>

                            <option
                                value="Preparing"
                                <?= $status === "Preparing" ? "selected" : "" ?>
                            >
                                Preparing
                            </option>

                            <option
                                value="Ready"
                                <?= $status === "Ready" ? "selected" : "" ?>
                            >
                                Ready
                            </option>

                            <option
                                value="Completed"
                                <?= $status === "Completed" ? "selected" : "" ?>
                            >
                                Completed
                            </option>

                            <option
                                value="Cancelled"
                                <?= $status === "Cancelled" ? "selected" : "" ?>
                            >
                                Cancelled
                            </option>

                        </select>

                    </div>


                    <div class="col-md-2">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="gold-btn w-100"
                            >
                                <i class="bi bi-search"></i>
                                Search
                            </button>

                            <a
                                href="orders.php"
                                class="btn btn-outline-secondary"
                                title="Reset"
                            >
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>

                        </div>

                    </div>

                </form>

            </div>


            <!-- ================================================= -->
            <!-- ORDERS TABLE -->
            <!-- ================================================= -->

            <div class="dashboard-card contact-messages-card">

                <div class="card-header-custom">

                    <div>

                        <h5>
                            All Orders
                        </h5>

                        <p>
                            Manage customer orders and their status.
                        </p>

                    </div>

                    <span class="message-count">

                        <?= count($orders) ?>

                        <?= count($orders) === 1
                            ? "Order"
                            : "Orders"
                        ?>

                    </span>

                </div>


                <div class="table-responsive">

                    <table class="table restaurant-table order-table">

                        <thead>

                            <tr>

                                <th>
                                    ORDER
                                </th>

                                <th>
                                    CUSTOMER
                                </th>

                                <th>
                                    TYPE
                                </th>

                                <th>
                                    TOTAL
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    DATE
                                </th>

                                <th>
                                    ACTIONS
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        <?php if (!$orders): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center py-5"
                                >

                                    <i
                                        class="bi bi-bag-x"
                                        style="font-size:40px;"
                                    ></i>

                                    <div class="mt-2">
                                        No orders found.
                                    </div>

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($orders as $order): ?>

                                <?php

                                $statusClass =
                                    strtolower(
                                        str_replace(
                                            " ",
                                            "-",
                                            $order["status"]
                                        )
                                    );

                                ?>

                                <tr>

                                    <!-- ORDER -->

                                    <td>

                                        <strong>
                                            #<?= (int)$order["id"] ?>
                                        </strong>

                                    </td>


                                    <!-- CUSTOMER -->

                                    <td>

                                        <div class="order-customer">

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $order["full_name"]
                                                ) ?>
                                            </strong>

                                            <small>
                                                <?= htmlspecialchars(
                                                    $order["email"]
                                                ) ?>
                                            </small>

                                        </div>

                                    </td>


                                    <!-- TYPE -->

                                    <td>

                                        <span class="order-type">

                                            <?= htmlspecialchars(
                                                $order["order_type"]
                                            ) ?>

                                        </span>

                                    </td>


                                    <!-- TOTAL -->

                                    <td>

                                        <strong>
                                            Rs.
                                            <?= number_format(
                                                (float)$order["total_amount"],
                                                2
                                            ) ?>
                                        </strong>

                                    </td>


                                    <!-- STATUS -->

                                    <td>

                                        <span
                                            class="order-status status-<?= htmlspecialchars($statusClass) ?>"
                                        >
                                            <?= htmlspecialchars(
                                                $order["status"]
                                            ) ?>
                                        </span>

                                    </td>


                                    <!-- DATE -->

                                    <td>

                                        <?= htmlspecialchars(
                                            date(
                                                "d M Y",
                                                strtotime(
                                                    $order["created_at"]
                                                )
                                            )
                                        ) ?>

                                        <br>

                                        <small>

                                            <?= htmlspecialchars(
                                                date(
                                                    "h:i A",
                                                    strtotime(
                                                        $order["created_at"]
                                                    )
                                                )
                                            ) ?>

                                        </small>

                                    </td>


                                    <!-- ACTIONS -->

                                    <td>

                                        <div class="order-actions">

                                            <a
                                                href="order_view.php?id=<?= (int)$order["id"] ?>"
                                                class="order-action-btn order-view-btn"
                                                title="View Order"
                                            >
                                                <i class="bi bi-eye-fill"></i>
                                            </a>


                                            <a
                                                href="order_edit.php?id=<?= (int)$order["id"] ?>"
                                                class="order-action-btn order-edit-btn"
                                                title="Edit Order"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </a>


                                            <form
                                                action="order_delete.php"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this order?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int)$order["id"] ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="order-action-btn order-delete-btn"
                                                    title="Delete Order"
                                                >
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

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
