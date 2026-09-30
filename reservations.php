<?php

$pageTitle = "Reservations | Bite & Bliss";

require_once __DIR__ . "/config/database.php";

$successMessage = "";
$errorMessage = "";

$old = [
    "full_name"        => "",
    "email"            => "",
    "phone"            => "",
    "reservation_date" => "",
    "reservation_time" => "",
    "guests"           => "",
    "special_request"  => ""
];


/* =====================================================
   SUCCESS MESSAGE AFTER REDIRECT
   ===================================================== */

if (
    isset($_GET["success"]) &&
    $_GET["success"] === "1"
) {
    $successMessage =
        "Your reservation has been submitted successfully.";
}


/* =====================================================
   RESERVATION FORM SUBMISSION
   ===================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    foreach ($old as $key => $value) {

        $old[$key] = trim(
            (string) ($_POST[$key] ?? "")
        );
    }


    /* =================================================
       REQUIRED FIELDS
       ================================================= */

    if (
        $old["full_name"] === "" ||
        $old["email"] === "" ||
        $old["phone"] === "" ||
        $old["reservation_date"] === "" ||
        $old["reservation_time"] === "" ||
        $old["guests"] === ""
    ) {

        $errorMessage =
            "Please fill in all required fields.";
    }


    /* =================================================
       EMAIL VALIDATION
       ================================================= */ elseif (
        !filter_var(
            $old["email"],
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $errorMessage =
            "Please enter a valid email address.";
    }


    /* =================================================
       DATE VALIDATION
       ================================================= */ elseif (
        $old["reservation_date"] < date("Y-m-d")
    ) {

        $errorMessage =
            "Please select today or a future reservation date.";
    }


    /* =================================================
       GUEST VALIDATION
       ================================================= */ elseif (
        (int) $old["guests"] <= 0
    ) {

        $errorMessage =
            "Please select the number of guests.";
    }


    /* =================================================
       SAVE RESERVATION
       ================================================= */ else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO reservations
                (
                    full_name,
                    email,
                    phone,
                    reservation_date,
                    reservation_time,
                    guests,
                    special_request
                )
                VALUES
                (
                    :full_name,
                    :email,
                    :phone,
                    :reservation_date,
                    :reservation_time,
                    :guests,
                    :special_request
                )
            ");

            $stmt->execute([
                ":full_name" =>
                $old["full_name"],

                ":email" =>
                $old["email"],

                ":phone" =>
                $old["phone"],

                ":reservation_date" =>
                $old["reservation_date"],

                ":reservation_time" =>
                $old["reservation_time"],

                ":guests" =>
                (int) $old["guests"],

                ":special_request" =>
                $old["special_request"]
            ]);


            /* =========================================
               POST / REDIRECT / GET
               ========================================= */

            header(
                "Location: reservations.php?success=1"
            );

            exit;
        } catch (PDOException $e) {

            // Do not expose database details.

            $errorMessage =
                "Unable to submit your reservation right now. Please try again.";
        }
    }
}


/* =====================================================
   SHARED HEADER
   ===================================================== */

include "includes/header.php";

?>

<!-- =====================================================
     RESERVATION VALIDATION STYLING
     ===================================================== -->

<style>
    /* -----------------------------------------------------
   INPUT / SELECT BASE
   ----------------------------------------------------- */

    .reservation-section .form-group input,
    .reservation-section .form-group select,
    .reservation-section .form-group textarea {
        transition:
            border-color 0.25s ease,
            box-shadow 0.25s ease;
    }


    /* -----------------------------------------------------
   INVALID = RED
   Matches partner's styling
   ----------------------------------------------------- */

    .reservation-section .form-group input:not([type="date"]):invalid:not(:placeholder-shown) {

        border-color: #dc3545 !important;
    }


    .reservation-section .form-group select:user-invalid {

        border-color: #dc3545 !important;
    }


    .reservation-section .form-group input[type="date"]:user-invalid {

        border-color: #dc3545 !important;
    }


    /* -----------------------------------------------------
   VALID = GREEN
   Matches partner's styling
   ----------------------------------------------------- */

    .reservation-section .form-group input:not([type="date"]):valid:not(:placeholder-shown) {

        border-color: #198754 !important;
    }


    .reservation-section .form-group select:user-valid {

        border-color: #198754 !important;
    }


    .reservation-section .form-group input[type="date"]:user-valid {

        border-color: #198754 !important;
    }


    /* -----------------------------------------------------
   INPUT BOX INVALID
   ----------------------------------------------------- */

    .reservation-section .input-box:has(input:not([type="date"]):invalid:not(:placeholder-shown)) {

        border-color: #dc3545 !important;

        box-shadow:
            0 0 0 2px rgba(220, 53, 69, 0.08);
    }


    .reservation-section .input-box:has(input[type="date"]:user-invalid) {

        border-color: #dc3545 !important;

        box-shadow:
            0 0 0 2px rgba(220, 53, 69, 0.08);
    }


    .reservation-section .input-box:has(select:user-invalid) {

        border-color: #dc3545 !important;

        box-shadow:
            0 0 0 2px rgba(220, 53, 69, 0.08);
    }


    /* -----------------------------------------------------
   INPUT BOX VALID
   ----------------------------------------------------- */

    .reservation-section .input-box:has(input:not([type="date"]):valid:not(:placeholder-shown)) {

        border-color: #198754 !important;
    }


    .reservation-section .input-box:has(input[type="date"]:user-valid) {

        border-color: #198754 !important;
    }


    .reservation-section .input-box:has(select:user-valid) {

        border-color: #198754 !important;
    }


    /* -----------------------------------------------------
   KEEP VALIDATION COLORS WHILE FOCUSED
   (otherwise the gold focus ring would hide red/green)
   ----------------------------------------------------- */

    .reservation-section .input-box:focus-within:has(input:not([type="date"]):invalid:not(:placeholder-shown)) {

        border-color: #dc3545 !important;
    }

    .reservation-section .input-box:focus-within:has(input:not([type="date"]):valid:not(:placeholder-shown)) {

        border-color: #198754 !important;
    }

    .reservation-section .input-box:focus-within:has(input[type="date"]:user-invalid) {

        border-color: #dc3545 !important;
    }

    .reservation-section .input-box:focus-within:has(input[type="date"]:user-valid) {

        border-color: #198754 !important;
    }

    .reservation-section .input-box:focus-within:has(select:user-invalid) {

        border-color: #dc3545 !important;
    }

    .reservation-section .input-box:focus-within:has(select:user-valid) {

        border-color: #198754 !important;
    }


    /* -----------------------------------------------------
   FOCUS
   ----------------------------------------------------- */

    .reservation-section .form-group input:focus,
    .reservation-section .form-group select:focus,
    .reservation-section .form-group textarea:focus {
        outline: none;
    }


    /* -----------------------------------------------------
   SUCCESS / ERROR MESSAGE
   ----------------------------------------------------- */

    .reservation-alert {

        display: flex !important;

        align-items: flex-start;

        gap: 10px;

        margin-bottom: 24px;
    }


    /* -----------------------------------------------------
   PER-FIELD ERROR MESSAGES
   ----------------------------------------------------- */

    .reservation-section .form-group .invalid-feedback {

        display: none;

        color: #dc3545;

        font-size: 12px;

        margin-top: 6px;
    }


    .reservation-section .form-group:has(input:not([type="date"]):invalid:not(:placeholder-shown)) .invalid-feedback,
    .reservation-section .form-group:has(input[type="date"]:user-invalid) .invalid-feedback,
    .reservation-section .form-group:has(select:user-invalid) .invalid-feedback {

        display: block;
    }


    /* -----------------------------------------------------
   DATE PICKER ICON / POPUP — DARK MODE
   Tells the browser to draw its native calendar icon and
   popup in a light color so it's visible on a dark field.
   Site defaults to dark mode; .light-mode flips it back.
   ----------------------------------------------------- */

    .reservation-section .form-group input[type="date"] {

        color-scheme: dark;
    }

    body.light-mode .reservation-section .form-group input[type="date"] {

        color-scheme: light;
    }


    /* -----------------------------------------------------
   KEEP DARK BACKGROUND ON FOCUS
   Bootstrap's .form-control:focus forces a white
   background — this overrides it for reservation fields.
   ----------------------------------------------------- */

    .reservation-section .input-box input:focus,
    .reservation-section .input-box select:focus,
    .reservation-section .input-box textarea:focus {

        background: transparent !important;
        color: var(--text) !important;
    }
</style>


<main>

    <!-- =====================================================
         HERO
         ===================================================== -->

    <section class="hero">

        <div class="container">

            <p class="section-label">
                BITE &amp; BLISS
            </p>

            <h1>
                Reserve Your
                <span>Table</span>
            </h1>

            <p>
                Your perfect dining experience starts here.
                Reserve your table and enjoy delicious food
                with your loved ones.
            </p>

            <div class="breadcrumb">

                <a href="index.php">
                    Home
                </a>

                <i
                    class="bi bi-chevron-right"
                    aria-hidden="true">
                </i>

                <span>
                    Reservations
                </span>

            </div>

        </div>

    </section>


    <!-- =====================================================
         RESERVATION CONTENT
         ===================================================== -->

    <section class="reservation-section">

        <div class="container">

            <div class="reservation-grid">


                <!-- =================================================
                     LEFT INFORMATION
                     ================================================= -->

                <div class="left-content">

                    <p class="section-label">
                        YOUR TABLE AWAITS
                    </p>

                    <h2>

                        A Perfect Table
                        <br>

                        <span>
                            For Every Moment
                        </span>

                    </h2>

                    <p class="description">

                        Whether it's a romantic dinner, a family
                        gathering, or a celebration with friends,
                        Bite &amp; Bliss is ready to make your experience
                        unforgettable.

                    </p>


                    <!-- OPENING HOURS -->

                    <div class="info-item">

                        <div class="info-icon">

                            <i
                                class="bi bi-clock"
                                aria-hidden="true">
                            </i>

                        </div>

                        <div>

                            <h4>
                                Opening Hours
                            </h4>

                            <p>
                                Daily
                            </p>

                            <strong>
                                <?= htmlspecialchars($settings["hours_weekday"]) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- PHONE -->

                    <div class="info-item">

                        <div class="info-icon">

                            <i
                                class="bi bi-telephone"
                                aria-hidden="true">
                            </i>

                        </div>

                        <div>

                            <h4>
                                Call Us
                            </h4>

                            <p>
                                For reservations &amp; inquiries
                            </p>

                            <strong>
                                <?= htmlspecialchars($settings["phone"]) ?>
                            </strong>

                        </div>

                    </div>


                    <!-- LOCATION -->

                    <div class="info-item">

                        <div class="info-icon">

                            <i
                                class="bi bi-geo-alt"
                                aria-hidden="true">
                            </i>

                        </div>

                        <div>

                            <h4>
                                Visit Us
                            </h4>

                            <p>
                                <?= htmlspecialchars($settings["address"]) ?>
                            </p>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     RESERVATION FORM
                     ================================================= -->

                <div class="form-card">


                    <!-- FORM HEADING -->

                    <div class="form-heading">

                        <p class="section-label">
                            BOOK A TABLE
                        </p>

                        <h3>
                            Make a Reservation
                        </h3>

                        <p>
                            Fill in your details below to request
                            a table at Bite &amp; Bliss.
                        </p>

                    </div>


                    <!-- =================================================
                         SUCCESS MESSAGE
                         ================================================= -->

                    <?php if ($successMessage !== ""): ?>

                        <div
                            class="form-message success reservation-alert"
                            role="status"
                            aria-live="polite">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>

                                <?= htmlspecialchars(
                                    $successMessage,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </span>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         ERROR MESSAGE
                         ================================================= -->

                    <?php if ($errorMessage !== ""): ?>

                        <div
                            class="form-message error reservation-alert"
                            role="alert"
                            aria-live="assertive">

                            <i
                                class="bi bi-exclamation-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>

                                <?= htmlspecialchars(
                                    $errorMessage,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </span>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         FORM
                         ================================================= -->

                    <form
                        id="reservationForm"
                        action="reservations.php"
                        method="POST"
                        novalidate>


                        <div class="form-grid">


                            <!-- =========================================
                                 FULL NAME
                                 ========================================== -->

                            <div class="form-group">

                                <label for="name">
                                    Full Name
                                </label>

                                <div class="input-box">

                                    <i
                                        class="bi bi-person"
                                        aria-hidden="true">
                                    </i>

                                    <input
                                        type="text"
                                        id="name"
                                        name="full_name"
                                        placeholder="Full Name"
                                        autocomplete="name"
                                        value="<?= htmlspecialchars(
                                                    $old["full_name"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        required>

                                </div>

                                <div class="invalid-feedback">Please enter your full name.</div>

                            </div>


                            <!-- =========================================
                                 EMAIL
                                 ========================================== -->

                            <div class="form-group">

                                <label for="email">
                                    Email Address
                                </label>

                                <div class="input-box">

                                    <i
                                        class="bi bi-envelope"
                                        aria-hidden="true">
                                    </i>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="Email Address"
                                        autocomplete="email"
                                        value="<?= htmlspecialchars(
                                                    $old["email"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        required>

                                </div>

                                <div class="invalid-feedback">Please enter a valid email address.</div>

                            </div>


                            <!-- =========================================
                                 PHONE
                                 ========================================== -->

                            <div class="form-group">

                                <label for="phone">
                                    Phone Number
                                </label>

                                <div class="input-box">

                                    <i
                                        class="bi bi-telephone"
                                        aria-hidden="true">
                                    </i>

                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        placeholder="Phone Number"
                                        autocomplete="tel"
                                        inputmode="tel"
                                        value="<?= htmlspecialchars(
                                                    $old["phone"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        required>

                                </div>

                                <div class="invalid-feedback">Please enter your phone number.</div>

                            </div>


                            <!-- =========================================
                                 GUESTS
                                 ========================================== -->

                            <div class="form-group">

                                <label for="guests">
                                    Number of Guests
                                </label>

                                <div class="input-box">

                                    <i
                                        class="bi bi-people"
                                        aria-hidden="true">
                                    </i>

                                    <select
                                        id="guests"
                                        name="guests"
                                        required>

                                        <option
                                            value=""
                                            disabled
                                            <?= $old["guests"] === ""
                                                ? "selected"
                                                : "" ?>>

                                            Select guests

                                        </option>


                                        <option
                                            value="1"
                                            <?= $old["guests"] === "1"
                                                ? "selected"
                                                : "" ?>>

                                            1 Person

                                        </option>


                                        <option
                                            value="2"
                                            <?= $old["guests"] === "2"
                                                ? "selected"
                                                : "" ?>>

                                            2 People

                                        </option>


                                        <option
                                            value="3"
                                            <?= $old["guests"] === "3"
                                                ? "selected"
                                                : "" ?>>

                                            3 People

                                        </option>


                                        <option
                                            value="4"
                                            <?= $old["guests"] === "4"
                                                ? "selected"
                                                : "" ?>>

                                            4 People

                                        </option>


                                        <option
                                            value="5"
                                            <?= $old["guests"] === "5"
                                                ? "selected"
                                                : "" ?>>

                                            5 People

                                        </option>


                                        <option
                                            value="6"
                                            <?= $old["guests"] === "6"
                                                ? "selected"
                                                : "" ?>>

                                            6 People

                                        </option>


                                        <option
                                            value="7"
                                            <?= $old["guests"] === "7"
                                                ? "selected"
                                                : "" ?>>

                                            7 People

                                        </option>


                                        <option
                                            value="8"
                                            <?= $old["guests"] === "8"
                                                ? "selected"
                                                : "" ?>>

                                            8 People

                                        </option>


                                        <option
                                            value="9"
                                            <?= $old["guests"] === "9"
                                                ? "selected"
                                                : "" ?>>

                                            9+ People

                                        </option>

                                    </select>

                                </div>

                                <div class="invalid-feedback">Please select the number of guests.</div>

                            </div>


                            <!-- =========================================
                                 DATE
                                 ========================================== -->

                            <div class="form-group">

                                <label for="date">
                                    Reservation Date
                                </label>

                                <div class="input-box">

                                    <i
                                        class="bi bi-calendar3"
                                        aria-hidden="true">
                                    </i>

                                    <input
                                        type="date"
                                        id="date"
                                        name="reservation_date"
                                        value="<?= htmlspecialchars(
                                                    $old["reservation_date"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        required>

                                </div>

                                <div class="invalid-feedback">Please select today or a future date.</div>

                            </div>


                            <!-- =========================================
                                 TIME
                                 ========================================== -->

                            <div class="form-group">

                                <label for="time">
                                    Preferred Time
                                </label>

                                <div class="input-box">

                                    <i
                                        class="bi bi-clock"
                                        aria-hidden="true">
                                    </i>

                                    <select
                                        id="time"
                                        name="reservation_time"
                                        required>

                                        <option
                                            value=""
                                            disabled
                                            <?= $old["reservation_time"] === ""
                                                ? "selected"
                                                : "" ?>>

                                            Select time

                                        </option>


                                        <option
                                            value="11:00:00"
                                            <?= $old["reservation_time"] === "11:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            11:00 AM

                                        </option>


                                        <option
                                            value="12:00:00"
                                            <?= $old["reservation_time"] === "12:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            12:00 PM

                                        </option>


                                        <option
                                            value="13:00:00"
                                            <?= $old["reservation_time"] === "13:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            01:00 PM

                                        </option>


                                        <option
                                            value="14:00:00"
                                            <?= $old["reservation_time"] === "14:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            02:00 PM

                                        </option>


                                        <option
                                            value="15:00:00"
                                            <?= $old["reservation_time"] === "15:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            03:00 PM

                                        </option>


                                        <option
                                            value="17:00:00"
                                            <?= $old["reservation_time"] === "17:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            05:00 PM

                                        </option>


                                        <option
                                            value="18:00:00"
                                            <?= $old["reservation_time"] === "18:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            06:00 PM

                                        </option>


                                        <option
                                            value="19:00:00"
                                            <?= $old["reservation_time"] === "19:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            07:00 PM

                                        </option>


                                        <option
                                            value="20:00:00"
                                            <?= $old["reservation_time"] === "20:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            08:00 PM

                                        </option>


                                        <option
                                            value="21:00:00"
                                            <?= $old["reservation_time"] === "21:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            09:00 PM

                                        </option>


                                        <option
                                            value="22:00:00"
                                            <?= $old["reservation_time"] === "22:00:00"
                                                ? "selected"
                                                : "" ?>>

                                            10:00 PM

                                        </option>

                                    </select>

                                </div>

                                <div class="invalid-feedback">Please select a preferred time.</div>

                            </div>


                            <!-- =========================================
                                 SPECIAL REQUEST
                                 ========================================== -->

                            <div class="form-group full">

                                <label for="message">

                                    Special Requests

                                    <span>
                                        (Optional)
                                    </span>

                                </label>

                                <div class="input-box textarea-box">

                                    <i
                                        class="bi bi-chat-left-text"
                                        aria-hidden="true">
                                    </i>

                                    <textarea
                                        id="message"
                                        name="special_request"
                                        class="form-control"
                                        rows="4"
                                        placeholder="Any special requests or occasions?"><?= htmlspecialchars(
                                                                                                $old["special_request"],
                                                                                                ENT_QUOTES,
                                                                                                "UTF-8"
                                                                                            ) ?></textarea>

                                </div>

                            </div>


                            <!-- =========================================
                                 SUBMIT
                                 ========================================== -->

                            <div class="form-group full">

                                <button
                                    type="submit"
                                    class="submit-btn"
                                    id="submitBtn">

                                    Reserve My Table

                                    <i
                                        class="bi bi-arrow-right"
                                        aria-hidden="true">
                                    </i>

                                </button>


                                <p class="form-note">

                                    <i
                                        class="bi bi-shield-check"
                                        aria-hidden="true">
                                    </i>

                                    Your information is safe with us.

                                </p>


                                <div
                                    id="formMessage"
                                    class="form-message"
                                    role="status"
                                    aria-live="polite">
                                </div>

                            </div>


                        </div>

                    </form>


                </div>

            </div>

        </div>

    </section>

</main>


<?php include "includes/footer.php"; ?>