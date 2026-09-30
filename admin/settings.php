<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

$successMessage = "";
$errorMessage = "";


/*
|--------------------------------------------------------------------------
| Load Current Settings
|--------------------------------------------------------------------------
| The table only ever holds a single row. If it's somehow empty (table
| just created, row deleted, etc.) fall back to blank defaults so the
| form still renders instead of erroring out.
*/

$settingsId = null;

$defaults = [
    "restaurant_name" => "",
    "address" => "",
    "phone" => "",
    "phone_secondary" => "",
    "email" => "",
    "email_secondary" => "",
    "hours_weekday" => "",
    "hours_weekend" => "",
    "facebook_url" => "",
    "instagram_url" => "",
    "twitter_url" => ""
];

try {

    $stmt = $pdo->query("
        SELECT *
        FROM settings
        ORDER BY id ASC
        LIMIT 1
    ");

    $row = $stmt->fetch();

    if ($row) {
        $settingsId = (int) $row["id"];
        $settings = array_merge($defaults, $row);
    } else {
        $settings = $defaults;
    }

} catch (PDOException $e) {

    $settings = $defaults;
    $errorMessage = "Unable to load settings. Please make sure the settings table exists.";
}


/*
|--------------------------------------------------------------------------
| Success Message After Redirect
|--------------------------------------------------------------------------
*/

if (isset($_GET["success"]) && $_GET["success"] === "1") {
    $successMessage = "Settings updated successfully.";
}


/*
|--------------------------------------------------------------------------
| Handle Update
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    foreach ($defaults as $key => $value) {
        $settings[$key] = trim((string) ($_POST[$key] ?? ""));
    }


    // -------------------------------------------------
    // VALIDATION
    // -------------------------------------------------

    if ($settings["restaurant_name"] === "") {

        $errorMessage = "Restaurant name is required.";

    } elseif ($settings["address"] === "") {

        $errorMessage = "Address is required.";

    } elseif ($settings["phone"] === "") {

        $errorMessage = "A primary phone number is required.";

    } elseif ($settings["email"] === "") {

        $errorMessage = "A primary email address is required.";

    } elseif (!filter_var($settings["email"], FILTER_VALIDATE_EMAIL)) {

        $errorMessage = "Please enter a valid primary email address.";

    } elseif (
        $settings["email_secondary"] !== "" &&
        !filter_var($settings["email_secondary"], FILTER_VALIDATE_EMAIL)
    ) {

        $errorMessage = "Please enter a valid secondary email address.";

    } elseif ($settings["hours_weekday"] === "") {

        $errorMessage = "Weekday hours are required.";

    } else {

        try {

            if ($settingsId === null) {

                // -------------------------------------------------
                // NO ROW YET — INSERT ONE
                // -------------------------------------------------

                $stmt = $pdo->prepare("
                    INSERT INTO settings (
                        restaurant_name, address, phone, phone_secondary,
                        email, email_secondary, hours_weekday, hours_weekend,
                        facebook_url, instagram_url, twitter_url
                    ) VALUES (
                        :restaurant_name, :address, :phone, :phone_secondary,
                        :email, :email_secondary, :hours_weekday, :hours_weekend,
                        :facebook_url, :instagram_url, :twitter_url
                    )
                ");

            } else {

                // -------------------------------------------------
                // UPDATE THE EXISTING ROW
                // -------------------------------------------------

                $stmt = $pdo->prepare("
                    UPDATE settings
                    SET
                        restaurant_name = :restaurant_name,
                        address = :address,
                        phone = :phone,
                        phone_secondary = :phone_secondary,
                        email = :email,
                        email_secondary = :email_secondary,
                        hours_weekday = :hours_weekday,
                        hours_weekend = :hours_weekend,
                        facebook_url = :facebook_url,
                        instagram_url = :instagram_url,
                        twitter_url = :twitter_url
                    WHERE id = :id
                ");

                $stmt->bindValue(":id", $settingsId, PDO::PARAM_INT);
            }

            $stmt->bindValue(":restaurant_name", $settings["restaurant_name"]);
            $stmt->bindValue(":address", $settings["address"]);
            $stmt->bindValue(":phone", $settings["phone"]);
            $stmt->bindValue(":phone_secondary", $settings["phone_secondary"] !== "" ? $settings["phone_secondary"] : null);
            $stmt->bindValue(":email", $settings["email"]);
            $stmt->bindValue(":email_secondary", $settings["email_secondary"] !== "" ? $settings["email_secondary"] : null);
            $stmt->bindValue(":hours_weekday", $settings["hours_weekday"]);
            $stmt->bindValue(":hours_weekend", $settings["hours_weekend"] !== "" ? $settings["hours_weekend"] : null);
            $stmt->bindValue(":facebook_url", $settings["facebook_url"] !== "" ? $settings["facebook_url"] : null);
            $stmt->bindValue(":instagram_url", $settings["instagram_url"] !== "" ? $settings["instagram_url"] : null);
            $stmt->bindValue(":twitter_url", $settings["twitter_url"] !== "" ? $settings["twitter_url"] : null);

            $stmt->execute();


            // -------------------------------------------------
            // POST / REDIRECT / GET
            // -------------------------------------------------

            header("Location: settings.php?success=1");
            exit;

        } catch (PDOException $e) {

            $errorMessage = "Unable to save settings. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Settings | Bite &amp; Bliss</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link rel="stylesheet" href="css/admin.css">
    <script src="js/theme.js"></script>

</head>

<body>

<div class="admin-layout">

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main-content">

        <div class="container-fluid p-4">


            <!-- PAGE HEADING -->
            <div class="page-heading users-page-heading">

                <div>

                    <p class="gold-label">
                        BITE &amp; BLISS
                    </p>

                    <h1>
                        Settings
                    </h1>

                    <p>
                        Manage the contact info, hours, and social links shown across the site.
                    </p>

                </div>

            </div>


            <!-- SUCCESS MESSAGE -->
            <?php if ($successMessage !== ""): ?>

                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= htmlspecialchars($successMessage) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

            <?php endif; ?>


            <!-- ERROR MESSAGE -->
            <?php if ($errorMessage !== ""): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>

            <?php endif; ?>


            <!-- SETTINGS FORM -->
            <div class="dashboard-card user-form-card settings-form-card">

                <div class="card-header-custom">

                    <div>

                        <h5>
                            <i class="bi bi-gear-fill"></i>
                            Restaurant Settings
                        </h5>

                        <p>
                            These values feed the footer, home page, reservations page, and contact page.
                        </p>

                    </div>

                </div>

                <form method="POST">


                    <!-- =====================================================
                         RESTAURANT DETAILS
                    ====================================================== -->

                    <div class="row g-4">

                        <div class="col-md-4">

                            <label class="form-label">
                                Restaurant Name
                            </label>

                            <input
                                type="text"
                                name="restaurant_name"
                                class="form-control"
                                value="<?= htmlspecialchars($settings["restaurant_name"]) ?>"
                            >

                        </div>

                        <div class="col-md-8">

                            <label class="form-label">
                                Address
                            </label>

                            <input
                                type="text"
                                name="address"
                                class="form-control"
                                value="<?= htmlspecialchars($settings["address"]) ?>"
                            >

                        </div>

                    </div>


                    <!-- =====================================================
                         CONTACT INFORMATION
                    ====================================================== -->

                    <div class="user-password-section">

                        <div class="user-section-heading">

                            <h5>
                                Contact Information
                            </h5>

                            <p>
                                Shown in the footer, contact page, and reservations page.
                            </p>

                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Primary Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["phone"]) ?>"
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Secondary Phone
                                    <span class="optional-tag">(Optional)</span>
                                </label>

                                <input
                                    type="text"
                                    name="phone_secondary"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["phone_secondary"]) ?>"
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Primary Email
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["email"]) ?>"
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Secondary Email
                                    <span class="optional-tag">(Optional)</span>
                                </label>

                                <input
                                    type="email"
                                    name="email_secondary"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["email_secondary"]) ?>"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- =====================================================
                         BUSINESS HOURS
                    ====================================================== -->

                    <div class="user-password-section">

                        <div class="user-section-heading">

                            <h5>
                                Business Hours
                            </h5>

                            <p>
                                Free text, e.g. "Mon - Fri: 11:00 AM - 11:00 PM".
                            </p>

                        </div>

                        <div class="row g-4">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Weekday Hours
                                </label>

                                <input
                                    type="text"
                                    name="hours_weekday"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["hours_weekday"]) ?>"
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Weekend Hours
                                    <span class="optional-tag">(Optional)</span>
                                </label>

                                <input
                                    type="text"
                                    name="hours_weekend"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["hours_weekend"]) ?>"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- =====================================================
                         SOCIAL MEDIA LINKS
                    ====================================================== -->

                    <div class="user-password-section">

                        <div class="user-section-heading">

                            <h5>
                                Social Media Links
                            </h5>

                            <p>
                                Leave blank (or "#") for platforms you don't use yet.
                            </p>

                        </div>

                        <div class="row g-4">

                            <div class="col-md-4">

                                <label class="form-label">
                                    <i class="bi bi-facebook"></i>
                                    Facebook URL
                                </label>

                                <input
                                    type="text"
                                    name="facebook_url"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["facebook_url"]) ?>"
                                >

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    <i class="bi bi-instagram"></i>
                                    Instagram URL
                                </label>

                                <input
                                    type="text"
                                    name="instagram_url"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["instagram_url"]) ?>"
                                >

                            </div>

                            <div class="col-md-4">

                                <label class="form-label">
                                    <i class="bi bi-twitter-x"></i>
                                    Twitter / X URL
                                </label>

                                <input
                                    type="text"
                                    name="twitter_url"
                                    class="form-control"
                                    value="<?= htmlspecialchars($settings["twitter_url"]) ?>"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- BUTTONS -->
                    <div class="d-flex gap-2 mt-4 pt-3">

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save"></i>
                            Save Settings
                        </button>

                        <a href="dashboard.php" class="btn-cancel">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/admin.js"></script>

</body>
</html>