<?php

$pageTitle = "Contact Us | Bite & Bliss";

require_once __DIR__ . "/config/database.php";


// =====================================================
// FORM MESSAGES
// =====================================================

$successMessage = "";
$errorMessage = "";


// =====================================================
// OLD FORM VALUES
// =====================================================

$old = [
    "name" => "",
    "email" => "",
    "phone" => "",
    "subject" => "",
    "message" => ""
];


// =====================================================
// SUCCESS MESSAGE AFTER REDIRECT
// =====================================================

if (
    isset($_GET["sent"]) &&
    $_GET["sent"] === "1"
) {
    $successMessage =
        "Thanks! Your message has been sent successfully.";
}


// =====================================================
// CONTACT FORM SUBMISSION
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // -------------------------------------------------
    // GET FORM VALUES
    // -------------------------------------------------

    foreach ($old as $key => $value) {

        $old[$key] = trim(
            (string) ($_POST[$key] ?? "")
        );
    }


    // -------------------------------------------------
    // REQUIRED FIELDS
    // -------------------------------------------------

    if (
        $old["name"] === "" ||
        $old["email"] === "" ||
        $old["subject"] === "" ||
        $old["message"] === ""
    ) {

        $errorMessage =
            "Please fill in all required fields.";
    }


    // -------------------------------------------------
    // EMAIL VALIDATION
    // -------------------------------------------------

    elseif (
        !filter_var(
            $old["email"],
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $errorMessage =
            "Please enter a valid email address.";
    }


    // -------------------------------------------------
    // SAVE MESSAGE
    // -------------------------------------------------

    else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO contact_messages
                (
                    name,
                    email,
                    phone,
                    subject,
                    message
                )
                VALUES
                (
                    :name,
                    :email,
                    :phone,
                    :subject,
                    :message
                )
            ");

            $stmt->execute([

                ":name" =>
                $old["name"],

                ":email" =>
                $old["email"],

                ":phone" =>
                $old["phone"],

                ":subject" =>
                $old["subject"],

                ":message" =>
                $old["message"]

            ]);


            // -------------------------------------------------
            // POST / REDIRECT / GET
            // -------------------------------------------------

            header(
                "Location: contact.php?sent=1"
            );

            exit;
        } catch (PDOException $e) {

            // Do not expose database details to visitors.

            $errorMessage =
                "Unable to send your message right now. Please try again.";
        }
    }
}


// =====================================================
// SHARED HEADER
// =====================================================

include 'includes/header.php';

?>


<main>


    <!-- =====================================================
         CONTACT HERO
    ====================================================== -->

    <section class="contact-hero">

        <div class="container">

            <div class="contact-hero-inner">


                <span class="hero-subtitle contact-hero-subtitle">
                    GET IN TOUCH
                </span>


                <h1>
                    We'd Love to
                    <span>Hear From You</span>
                </h1>


                <p>

                    Questions about a reservation, feedback on your last visit,
                    or planning something special? Reach out and our team will
                    get back to you shortly.

                </p>


                <div class="contact-hero-strip">


                    <!-- PHONE -->

                    <div>

                        <i
                            class="bi bi-telephone"
                            aria-hidden="true">
                        </i>

                        <span>

                            Call Us<br>

                            <strong>
                                <?= htmlspecialchars($settings["phone"]) ?>
                            </strong>

                        </span>

                    </div>


                    <!-- EMAIL -->

                    <div>

                        <i
                            class="bi bi-envelope"
                            aria-hidden="true">
                        </i>

                        <span>

                            Email Us<br>

                            <strong>
                                <?= htmlspecialchars($settings["email"]) ?>
                            </strong>

                        </span>

                    </div>


                    <!-- HOURS -->

                    <div>

                        <i
                            class="bi bi-clock"
                            aria-hidden="true">
                        </i>

                        <span>

                            Hours<br>

                            <strong>
                                <?= htmlspecialchars($settings["hours_weekday"]) ?>
                            </strong>

                        </span>

                    </div>


                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         CONTACT INFORMATION
    ====================================================== -->

    <section class="info-section py-5">

        <div class="container py-4">

            <div class="row g-4">


                <!-- ADDRESS -->

                <div class="col-lg-3 col-md-6">

                    <div class="info-card">

                        <i
                            class="bi bi-geo-alt"
                            aria-hidden="true">
                        </i>

                        <h5>
                            Visit Us
                        </h5>

                        <p>
                            <?= htmlspecialchars($settings["address"]) ?>
                        </p>

                    </div>

                </div>


                <!-- PHONE -->

                <div class="col-lg-3 col-md-6">

                    <div class="info-card">

                        <i
                            class="bi bi-telephone"
                            aria-hidden="true">
                        </i>

                        <h5>
                            Call Us
                        </h5>

                        <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $settings["phone"])) ?>">
                            <?= htmlspecialchars($settings["phone"]) ?>
                        </a>

                        <?php if (!empty($settings["phone_secondary"])): ?>

                            <br>

                            <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', $settings["phone_secondary"])) ?>">
                                <?= htmlspecialchars($settings["phone_secondary"]) ?>
                            </a>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- EMAIL -->

                <div class="col-lg-3 col-md-6">

                    <div class="info-card">

                        <i
                            class="bi bi-envelope"
                            aria-hidden="true">
                        </i>

                        <h5>
                            Email Us
                        </h5>

                        <a href="mailto:<?= htmlspecialchars($settings["email"]) ?>">
                            <?= htmlspecialchars($settings["email"]) ?>
                        </a>

                        <?php if (!empty($settings["email_secondary"])): ?>

                            <br>

                            <a href="mailto:<?= htmlspecialchars($settings["email_secondary"]) ?>">
                                <?= htmlspecialchars($settings["email_secondary"]) ?>
                            </a>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- HOURS -->

                <div class="col-lg-3 col-md-6">

                    <div class="info-card">

                        <i
                            class="bi bi-clock"
                            aria-hidden="true">
                        </i>

                        <h5>
                            Working Hours
                        </h5>

                        <p>

                            <?= htmlspecialchars($settings["hours_weekday"]) ?>

                            <?php if (!empty($settings["hours_weekend"])): ?>
                                <br>
                                <?= htmlspecialchars($settings["hours_weekend"]) ?>
                            <?php endif; ?>

                        </p>

                    </div>

                </div>


            </div>

        </div>

    </section>


    <!-- =====================================================
         CONTACT FORM + SIDE PANEL
    ====================================================== -->

    <section class="contact-main-section py-5">

        <div class="container py-4">

            <div class="row g-5">


                <!-- =================================================
                     CONTACT FORM
                ================================================== -->

                <div class="col-lg-7">


                    <span class="section-subtitle">
                        SEND A MESSAGE
                    </span>


                    <h2 class="section-title">

                        Let's Start a
                        <span>Conversation</span>

                    </h2>


                    <div class="contact-form-card mt-4">


                        <!-- SUCCESS MESSAGE -->

                        <?php if ($successMessage !== ""): ?>

                            <div
                                class="alert alert-success custom-alert"
                                role="alert">

                                <i
                                    class="bi bi-check-circle-fill me-2"
                                    aria-hidden="true">
                                </i>

                                <?= htmlspecialchars(
                                    $successMessage,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </div>

                        <?php endif; ?>


                        <!-- ERROR MESSAGE -->

                        <?php if ($errorMessage !== ""): ?>

                            <div
                                class="alert alert-danger custom-alert"
                                role="alert">

                                <i
                                    class="bi bi-exclamation-triangle-fill me-2"
                                    aria-hidden="true">
                                </i>

                                <?= htmlspecialchars(
                                    $errorMessage,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </div>

                        <?php endif; ?>


                        <!-- FORM -->

                        <form
                            id="contactForm"
                            method="POST"
                            action="contact.php"
                            novalidate>


                            <div class="row g-3">


                                <!-- NAME -->

                                <div class="col-md-6">

                                    <label
                                        class="form-label"
                                        for="cf-name">

                                        Full Name

                                    </label>


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="cf-name"
                                        name="name"
                                        placeholder="Your name"
                                        autocomplete="name"
                                        value="<?= htmlspecialchars(
                                                    $old["name"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        required>


                                    <div class="invalid-feedback">

                                        Please enter your name.

                                    </div>

                                </div>


                                <!-- EMAIL -->

                                <div class="col-md-6">

                                    <label
                                        class="form-label"
                                        for="cf-email">

                                        Email Address

                                    </label>


                                    <input
                                        type="email"
                                        class="form-control"
                                        id="cf-email"
                                        name="email"
                                        placeholder="you@example.com"
                                        autocomplete="email"
                                        value="<?= htmlspecialchars(
                                                    $old["email"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        required>


                                    <div class="invalid-feedback">

                                        Please enter a valid email.

                                    </div>

                                </div>


                                <!-- PHONE -->

                                <div class="col-md-6">

                                    <label
                                        class="form-label"
                                        for="cf-phone">

                                        Phone Number
                                        <span>
                                            (Optional)
                                        </span>

                                    </label>


                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="cf-phone"
                                        name="phone"
                                        placeholder="+92 300 0000000"
                                        autocomplete="tel"
                                        inputmode="tel"
                                        value="<?= htmlspecialchars(
                                                    $old["phone"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>">

                                </div>


                                <!-- SUBJECT -->

                                <div class="col-md-6">

                                    <label
                                        class="form-label"
                                        for="cf-subject">

                                        Subject

                                    </label>


                                    <select
                                        class="form-select"
                                        id="cf-subject"
                                        name="subject"
                                        required>


                                        <option
                                            value=""
                                            disabled
                                            <?= $old["subject"] === ""
                                                ? "selected"
                                                : "" ?>>

                                            Choose a topic

                                        </option>


                                        <option
                                            value="General Inquiry"
                                            <?= $old["subject"] === "General Inquiry"
                                                ? "selected"
                                                : "" ?>>

                                            General Inquiry

                                        </option>


                                        <option
                                            value="Reservation"
                                            <?= $old["subject"] === "Reservation"
                                                ? "selected"
                                                : "" ?>>

                                            Reservation

                                        </option>


                                        <option
                                            value="Feedback"
                                            <?= $old["subject"] === "Feedback"
                                                ? "selected"
                                                : "" ?>>

                                            Feedback

                                        </option>


                                        <option
                                            value="Catering & Events"
                                            <?= $old["subject"] === "Catering & Events"
                                                ? "selected"
                                                : "" ?>>

                                            Catering &amp; Events

                                        </option>


                                        <option
                                            value="Careers"
                                            <?= $old["subject"] === "Careers"
                                                ? "selected"
                                                : "" ?>>

                                            Careers

                                        </option>


                                    </select>


                                    <div class="invalid-feedback">

                                        Please choose a subject.

                                    </div>

                                </div>


                                <!-- MESSAGE -->

                                <div class="col-12">

                                    <label
                                        class="form-label"
                                        for="cf-message">

                                        Message

                                    </label>


                                    <textarea
                                        class="form-control"
                                        id="cf-message"
                                        name="message"
                                        rows="5"
                                        placeholder="Tell us how we can help..."
                                        required><?= htmlspecialchars(
                                                        $old["message"],
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?></textarea>


                                    <div class="invalid-feedback">

                                        Please write a short message.

                                    </div>

                                </div>


                                <!-- SUBMIT -->

                                <div class="col-12">

                                    <button
                                        type="submit"
                                        class="btn-submit">

                                        <span>
                                            Send Message
                                        </span>

                                        <i
                                            class="bi bi-arrow-right"
                                            aria-hidden="true">
                                        </i>

                                    </button>

                                </div>


                            </div>


                        </form>


                    </div>

                </div>


                <!-- =================================================
                     SIDE PANEL
                ================================================== -->

                <div class="col-lg-5">


                    <span class="section-subtitle">
                        DETAILS
                    </span>


                    <h2 class="section-title">

                        Opening Hours &amp;
                        <span>Socials</span>

                    </h2>


                    <!-- OPENING HOURS -->

                    <div class="side-panel-card mt-4">

                        <h5 class="side-panel-title">

                            <i
                                class="bi bi-clock side-panel-icon"
                                aria-hidden="true">
                            </i>

                            Opening Hours

                        </h5>


                        <ul
                            class="hours-list"
                            id="hoursList">


                            <li data-day="1,2,3,4,5">

                                <span>
                                    Weekdays
                                </span>

                                <strong>
                                    <?= htmlspecialchars($settings["hours_weekday"]) ?>
                                </strong>

                            </li>


                            <?php if (!empty($settings["hours_weekend"])): ?>

                                <li data-day="0,6">

                                    <span>
                                        Weekend
                                    </span>

                                    <strong>
                                        <?= htmlspecialchars($settings["hours_weekend"]) ?>
                                    </strong>

                                </li>

                            <?php endif; ?>


                        </ul>

                    </div>


                    <!-- SOCIAL MEDIA -->

                    <div class="side-panel-card">

                        <h5 class="side-panel-title">

                            <i
                                class="bi bi-share side-panel-icon"
                                aria-hidden="true">
                            </i>

                            Follow Us

                        </h5>


                        <div class="social-row">


                            <a
                                href="<?= htmlspecialchars($settings["facebook_url"] ?: '#') ?>"
                                aria-label="Facebook">

                                <i
                                    class="bi bi-facebook"
                                    aria-hidden="true">
                                </i>

                            </a>


                            <a
                                href="<?= htmlspecialchars($settings["instagram_url"] ?: '#') ?>"
                                aria-label="Instagram">

                                <i
                                    class="bi bi-instagram"
                                    aria-hidden="true">
                                </i>

                            </a>


                            <a
                                href="#"
                                aria-label="TikTok">

                                <i
                                    class="bi bi-tiktok"
                                    aria-hidden="true">
                                </i>

                            </a>


                            <a
                                href="#"
                                aria-label="WhatsApp">

                                <i
                                    class="bi bi-whatsapp"
                                    aria-hidden="true">
                                </i>

                            </a>


                            <a
                                href="<?= htmlspecialchars($settings["twitter_url"] ?: '#') ?>"
                                aria-label="Twitter">

                                <i
                                    class="bi bi-twitter-x"
                                    aria-hidden="true">
                                </i>

                            </a>


                        </div>

                    </div>


                </div>


            </div>

        </div>

    </section>


    <!-- =====================================================
         LOCATION / MAP
    ====================================================== -->

    <section class="map-section py-5">

        <div class="container py-4">


            <div class="text-center mb-5">

                <span class="section-subtitle">
                    FIND US
                </span>


                <h2 class="section-title">

                    Our
                    <span>Location</span>

                </h2>


                <p class="section-text mx-auto map-description">

                    Drop by for dine-in, or use the map below to get
                    directions straight to our door.

                </p>

            </div>


            <div class="map-wrapper">


                <iframe
                    src="https://www.google.com/maps?q=<?= urlencode($settings["address"]) ?>&output=embed"
                    title="Bite & Bliss location map"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>


                <div class="map-pin-card">


                    <i
                        class="bi bi-geo-alt-fill"
                        aria-hidden="true">
                    </i>


                    <div>

                        <strong>
                            Bite &amp; Bliss
                        </strong>

                        <span>
                            <?= htmlspecialchars($settings["address"]) ?>
                        </span>

                    </div>


                </div>


            </div>

        </div>

    </section>


</main>


<?php include 'includes/footer.php'; ?>