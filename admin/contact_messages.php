<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";


// FETCH CONTACT MESSAGES

$stmt = $pdo->query(
    "SELECT
        id,
        name,
        email,
        phone,
        subject,
        message,
        status,
        created_at
    FROM contact_messages
    ORDER BY created_at"
);

$contactMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);


// FLASH MESSAGES

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";

unset($_SESSION["success"], $_SESSION["error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | Bite & Bliss</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/theme.js"></script>
</head>

<body>

    <div class="admin-layout">

        <!-- SIDEBAR OVERLAY -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- SIDEBAR -->
        <?php require_once "includes/sidebar.php"; ?>


        <!-- MAIN CONTENT -->

        <main class="main-content">

            <div class="container-fluid p-4">

                <!-- PAGE HEADING -->
                <div class="page-heading">
                    <div>
                        <p class="gold-label">BITE & BLISS</p>
                        <h1>Contact Messages</h1>
                        <p>View and manage messages received from customers.</p>
                    </div>
                </div>

                <!-- SUCCESS MESSAGE -->

                <?php if ($success !== ""): ?>

                    <div class="alert alert-success">
                        <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>

                <!-- ERROR MESSAGE -->

                <?php if ($error !== ""): ?>

                    <div class="alert alert-danger">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                <?php endif; ?>


                <!-- CONTACT MESSAGES CARD -->

                <div class="dashboard-card contact-messages-card">

                    <!-- CARD HEADER -->
                    <div class="card-header-custom">

                        <div>
                            <h5>All Contact Messages</h5>
                            <p>Messages submitted through the Contact Us form.</p>
                        </div>

                        <span class="message-count">
                            <?= count($contactMessages) ?>
                            <?= count($contactMessages) === 1 ? "Message" : "Messages" ?>
                        </span>

                    </div>


                    <!-- TABLE -->

                    <div class="table-responsive">

                        <table class="table restaurant-table contact-messages-table">

                            <thead>
                                <tr>

                                    <th>ID</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>PHONE</th>
                                    <th>SUBJECT</th>
                                    <th>MESSAGE</th>
                                    <th>STATUS</th>
                                    <th>DATE</th>
                                    <th>ACTION</th>

                                </tr>
                            </thead>

                            <tbody>

                                <?php if (empty($contactMessages)): ?>

                                    <tr>
                                        <td colspan="9" class="text-center">

                                            <div class="empty-messages">
                                                <i class="bi bi-envelope-open"></i>
                                                <h6>No Contact Messages</h6>
                                                <p>There are no messages to display.</p>
                                            </div>

                                        </td>
                                    </tr>


                                <?php else: ?>

                                    <?php foreach ($contactMessages as $contact): ?>

                                        <tr>

                                            <!-- ID -->
                                            <td>
                                                <strong>#<?= (int) $contact["id"] ?></strong>
                                            </td>

                                            <!-- NAME -->
                                            <td>
                                                <div class="message-user">

                                                    <span class="message-avatar">
                                                        <?= htmlspecialchars(
                                                            strtoupper(substr($contact["name"], 0, 1)),
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>
                                                    </span>

                                                    <span><?= htmlspecialchars($contact["name"], ENT_QUOTES, 'UTF-8') ?></span>

                                                </div>
                                            </td>

                                            <!-- EMAIL -->
                                            <td>
                                                <a href="mailto:<?= htmlspecialchars($contact["email"], ENT_QUOTES, 'UTF-8') ?>" class="contact-link">
                                                    <?= htmlspecialchars(
                                                        $contact["email"],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?></a>
                                            </td>

                                            <!-- PHONE -->
                                            <td>
                                                <?php if (!empty($contact["phone"])): ?>

                                                    <a href="tel:<?= htmlspecialchars(
                                                                        $contact["phone"],
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>"
                                                        class="contact-link">

                                                        <?= htmlspecialchars($contact["phone"], ENT_QUOTES, 'UTF-8') ?>
                                                    </a>

                                                <?php else: ?>

                                                    <span class="empty-value">—</span>

                                                <?php endif; ?>

                                            </td>

                                            <!-- SUBJECT -->
                                            <td>
                                                <span class="subject-text">
                                                    <?= htmlspecialchars($contact["subject"], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </td>

                                            <!-- MESSAGE -->
                                            <td>
                                                <div class="message-content">
                                                    <?= nl2br(
                                                        htmlspecialchars($contact["message"], ENT_QUOTES, 'UTF-8')
                                                    ) ?>
                                                </div>
                                            </td>

                                            <!-- STATUS -->
                                            <td>

                                                <?php

                                                $status = $contact["status"];

                                                $statusClass = match ($status) {
                                                    "New" => "message-status-new",
                                                    "Read" => "message-status-read",
                                                    "Replied" => "message-status-replied",
                                                    default => "message-status-default"
                                                };

                                                ?>

                                                <span class="message-status <?= $statusClass ?>">
                                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                                </span>

                                            </td>

                                            <!-- DATE -->
                                            <td>
                                                <?= date("d M Y, h:i A", strtotime($contact["created_at"])) ?>
                                            </td>

                                            <!-- DELETE -->
                                            <td>
                                                <form action="contact_message_delete.php" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this message?');">

                                                    <input type="hidden" name="id" value="<?= (int) $contact["id"] ?>">

                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete message">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </form>
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

</body>

</html>