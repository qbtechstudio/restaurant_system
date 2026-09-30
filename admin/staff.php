<?php
require_once "admin_auth_check.php";
require_once "../config/database.php";

$successMessage = "";
$errorMessage = "";

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $successMessage = "Staff member added successfully.";
    } elseif ($_GET['success'] === 'updated') {
        $successMessage = "Staff member updated successfully.";
    } elseif ($_GET['success'] === 'deleted') {
        $successMessage = "Staff member deleted successfully.";
    }
}

try {
    $stmt = $pdo->query("
        SELECT id, name, position, phone, email, address, status, created_at
        FROM staff
        ORDER BY id DESC
    ");
    $staffMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $staffMembers = [];
    $errorMessage = "Unable to load staff records. Please make sure the staff table exists.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management | Bite & Bliss</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/staff.css">
</head>

<body>

    <?php require_once "includes/sidebar.php"; ?>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">
        <div class="container-fluid p-4 staff-page-container">

            <div class="page-heading staff-page-heading">
                <div>
                    <span class="gold-label">ADMINISTRATION</span>
                    <h1>Staff Management</h1>
                    <p>Manage restaurant staff members and their information.</p>
                </div>

                <a href="staff_add.php" class="btn gold-btn staff-add-btn">
                    <i class="bi bi-person-plus-fill"></i>
                    Add Staff
                </a>
            </div>

            <?php if ($successMessage): ?>
                <div class="alert staff-alert staff-alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span><?= htmlspecialchars($successMessage) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="alert staff-alert staff-alert-error alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= htmlspecialchars($errorMessage) ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="dashboard-card staff-card">

                <div class="card-header-custom staff-card-header">
                    <div>
                        <h3>
                            <i class="bi bi-person-badge-fill"></i>
                            All Staff
                        </h3>
                        <p>Restaurant staff directory</p>
                    </div>

                    <div class="message-count staff-count">
                        <?= count($staffMembers) ?>
                        <?= count($staffMembers) === 1 ? 'Member' : 'Members' ?>
                    </div>
                </div>

                <?php if (!empty($staffMembers)): ?>

                    <div class="table-responsive">
                        <table class="table restaurant-table staff-table align-middle">
                            <thead>
                                <tr>
                                    <th>STAFF MEMBER</th>
                                    <th>POSITION</th>
                                    <th>CONTACT</th>
                                    <th>EMAIL</th>
                                    <th>STATUS</th>
                                    <th class="text-end">ACTIONS</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($staffMembers as $staff): ?>

                                    <?php
                                    $name = htmlspecialchars($staff['name']);
                                    $position = htmlspecialchars($staff['position']);
                                    $phone = htmlspecialchars($staff['phone']);
                                    $email = htmlspecialchars($staff['email'] ?? '');
                                    $address = htmlspecialchars($staff['address'] ?? '');
                                    $status = $staff['status'];

                                    $initial = strtoupper(substr(trim($staff['name']), 0, 1));

                                    $statusClass = 'status-active';
                                    $statusIcon = 'bi-check-circle-fill';

                                    if ($status === 'On Leave') {
                                        $statusClass = 'status-leave';
                                        $statusIcon = 'bi-clock-fill';
                                    } elseif ($status === 'Inactive') {
                                        $statusClass = 'status-inactive';
                                        $statusIcon = 'bi-x-circle-fill';
                                    }
                                    ?>

                                    <tr>
                                        <td>
                                            <div class="staff-member">
                                                <div class="staff-avatar">
                                                    <?= htmlspecialchars($initial) ?>
                                                </div>

                                                <div class="staff-member-info">
                                                    <strong><?= $name ?></strong>

                                                    <?php if ($address): ?>
                                                        <small>
                                                            <i class="bi bi-geo-alt"></i>
                                                            <?= $address ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="staff-position">
                                                <i class="bi bi-briefcase-fill"></i>
                                                <?= $position ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a href="tel:<?= $phone ?>" class="staff-contact">
                                                <i class="bi bi-telephone-fill"></i>
                                                <?= $phone ?>
                                            </a>
                                        </td>

                                        <td>
                                            <?php if ($email): ?>
                                                <a href="mailto:<?= $email ?>" class="staff-email">
                                                    <i class="bi bi-envelope-fill"></i>
                                                    <?= $email ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="staff-no-data">—</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <span class="staff-status <?= $statusClass ?>">
                                                <i class="bi <?= $statusIcon ?>"></i>
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="staff-actions">
                                                <a href="staff_edit.php?id=<?= (int)$staff['id'] ?>"
                                                    class="staff-action-btn staff-edit-btn"
                                                    title="Edit Staff">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <a href="staff_delete.php?id=<?= (int)$staff['id'] ?>"
                                                    class="staff-action-btn staff-delete-btn"
                                                    title="Delete Staff"
                                                    onclick="return confirm('Are you sure you want to delete this staff member?');">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php else: ?>

                    <div class="empty-staff">
                        <div class="empty-staff-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <h3>No Staff Members Yet</h3>
                        <p>Start by adding your first restaurant staff member.</p>

                        <a href="staff_add.php" class="btn gold-btn">
                            <i class="bi bi-person-plus-fill"></i>
                            Add First Staff Member
                        </a>
                    </div>

                <?php endif; ?>

            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
    <script src="js/theme.js"></script>

</body>

</html>