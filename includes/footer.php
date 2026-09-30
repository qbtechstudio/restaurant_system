<!-- =========================
         BACK TO TOP
    ========================== -->

    <button
        id="backToTop"
        class="back-to-top"
        type="button"
        aria-label="Back to top">

        <i
            class="bi bi-arrow-up"
            aria-hidden="true">
        </i>

    </button>


    <!-- =========================
         FOOTER
    ========================== -->

    <footer class="site-footer">

        <div class="container">

            <div class="row g-4">

                <!-- About -->
                <div class="col-lg-4 col-md-6">

                    <a
                        href="<?= $sitePath ?? '' ?>index.php"
                        class="footer-logo">

                        Bite <span>&</span> Bliss

                    </a>

                    <p class="footer-text">

                        Bringing people together through delicious food,
                        warm hospitality, and memorable dining experiences.

                    </p>

                </div>


                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">

                    <h5>
                        Quick Links
                    </h5>

                    <ul class="footer-links">

                        <li>
                            <a href="<?= $sitePath ?? '' ?>index.php">
                                Home
                            </a>
                        </li>

                        <li>
                            <a href="<?= $sitePath ?? '' ?>menu.php">
                                Menu
                            </a>
                        </li>

                        <li>
                            <a href="<?= $sitePath ?? '' ?>about.php">
                                About
                            </a>
                        </li>

                        <li>
                            <a href="<?= $sitePath ?? '' ?>contact.php">
                                Contact
                            </a>
                        </li>

                    </ul>

                </div>


                <!-- Explore -->
                <div class="col-lg-3 col-md-6">

                    <h5>
                        Explore
                    </h5>

                    <ul class="footer-links">

                        <li>
                            <a href="<?= $sitePath ?? '' ?>reservations.php">
                                Reservations
                            </a>
                        </li>

                        <li>
                            <a href="<?= $sitePath ?? '' ?>menu.php">
                                Our Menu
                            </a>
                        </li>

                        <li>
                            <a href="<?= $sitePath ?? '' ?>about.php">
                                Our Story
                            </a>
                        </li>

                    </ul>

                </div>


                <!-- Contact -->
                <div class="col-lg-3 col-md-6">

                    <h5>
                        Contact
                    </h5>

                    <ul class="footer-contact">

                        <li>

                            <i
                                class="bi bi-geo-alt"
                                aria-hidden="true">
                            </i>

                            <span>
                                <?= htmlspecialchars($settings["address"]) ?>
                            </span>

                        </li>

                        <li>

                            <i
                                class="bi bi-telephone"
                                aria-hidden="true">
                            </i>

                            <span>
                                <?= htmlspecialchars($settings["phone"]) ?>
                            </span>

                        </li>

                        <li>

                            <i
                                class="bi bi-envelope"
                                aria-hidden="true">
                            </i>

                            <span>
                                <?= htmlspecialchars($settings["email"]) ?>
                            </span>

                        </li>

                    </ul>

                </div>

            </div>


            <!-- Divider -->
            <hr>


            <!-- Footer Bottom -->
            <div class="footer-bottom">

                <p>
                    &copy; <?= date('Y') ?> Bite &amp; Bliss.
                    All Rights Reserved.
                </p>

                <div class="footer-social">

                    <a target="_blank"
                        href="<?= htmlspecialchars($settings["facebook_url"] ?: '#') ?>"
                        aria-label="Facebook">

                        <i
                            class="bi bi-facebook"
                            aria-hidden="true">
                        </i>

                    </a>

                    <a target="_blank"
                        href="<?= htmlspecialchars($settings["instagram_url"] ?: '#') ?>"
                        aria-label="Instagram">

                        <i
                            class="bi bi-instagram"
                            aria-hidden="true">
                        </i>

                    </a>

                    <a target="_blank"
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

    </footer>


    <!-- =========================
         BOOTSTRAP JAVASCRIPT
    ========================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    <!-- =========================
         MAIN JAVASCRIPT
    ========================== -->

    <script src="<?= $sitePath ?? '' ?>assets/js/script.js"></script>

</body>
</html>