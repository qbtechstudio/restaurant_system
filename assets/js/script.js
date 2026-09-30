// =====================================================
// BITE & BLISS - MAIN JAVASCRIPT
// =====================================================

(function () {
    "use strict";


    // =================================================
    // THEME TOGGLE
    // =================================================

    const themeToggle = document.getElementById("themeToggle");

    if (themeToggle) {

        const themeIcon = themeToggle.querySelector("i");

        const savedTheme =
            localStorage.getItem("biteBlissTheme");


        function applyTheme(theme) {

            const isLightMode = theme === "light";

            document.body.classList.toggle(
                "light-mode",
                isLightMode
            );


            if (themeIcon) {

                themeIcon.classList.toggle(
                    "bi-sun-fill",
                    !isLightMode
                );

                themeIcon.classList.toggle(
                    "bi-moon-fill",
                    isLightMode
                );

            }
        }


        applyTheme(
            savedTheme === "light"
                ? "light"
                : "dark"
        );


        themeToggle.addEventListener(
            "click",
            function () {

                const isLightMode =
                    document.body.classList.toggle(
                        "light-mode"
                    );

                const theme =
                    isLightMode
                        ? "light"
                        : "dark";


                if (themeIcon) {

                    themeIcon.classList.toggle(
                        "bi-sun-fill",
                        !isLightMode
                    );

                    themeIcon.classList.toggle(
                        "bi-moon-fill",
                        isLightMode
                    );

                }


                localStorage.setItem(
                    "biteBlissTheme",
                    theme
                );

            }
        );
    }


    // =================================================
    // MOUSE GLOW
    // =================================================

    const mouseGlow =
        document.querySelector(".mouse-glow");


    if (mouseGlow) {

        document.addEventListener(
            "mousemove",
            function (event) {

                mouseGlow.style.left =
                    `${event.clientX}px`;

                mouseGlow.style.top =
                    `${event.clientY}px`;

            }
        );

    }


    // =================================================
    // RESERVATION DATE
    // =================================================

    const dateInput =
        document.getElementById("date");


    if (dateInput) {

        const today = new Date();

        const year =
            today.getFullYear();

        const month =
            String(
                today.getMonth() + 1
            ).padStart(2, "0");

        const day =
            String(
                today.getDate()
            ).padStart(2, "0");


        dateInput.min =
            `${year}-${month}-${day}`;

    }


    // =================================================
    // RESERVATION FORM
    // =================================================

    const reservationForm =
        document.getElementById(
            "reservationForm"
        );


    const submitButton =
        document.getElementById(
            "submitBtn"
        );


    const formMessage =
        document.getElementById(
            "formMessage"
        );


    function showMessage(
        message,
        type
    ) {

        if (!formMessage) {
            return;
        }


        formMessage.textContent =
            message;


        formMessage.className =
            `form-message ${type}`;

    }


    if (reservationForm) {

        reservationForm.addEventListener(
            "submit",
            function (event) {

                const name =
                    document
                        .getElementById("name")
                        ?.value
                        .trim() || "";


                const email =
                    document
                        .getElementById("email")
                        ?.value
                        .trim() || "";


                const phone =
                    document
                        .getElementById("phone")
                        ?.value
                        .trim() || "";


                const guests =
                    document
                        .getElementById("guests")
                        ?.value || "";


                const date =
                    document
                        .getElementById("date")
                        ?.value || "";


                const time =
                    document
                        .getElementById("time")
                        ?.value || "";


                // -----------------------------------------
                // REQUIRED FIELDS
                // -----------------------------------------

                if (
                    !name ||
                    !email ||
                    !phone ||
                    !guests ||
                    !date ||
                    !time
                ) {

                    event.preventDefault();

                    return;
                }


                // -----------------------------------------
                // EMAIL VALIDATION
                // -----------------------------------------

                const emailPattern =
                    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


                if (
                    !emailPattern.test(email)
                ) {

                    event.preventDefault();

                    showMessage(
                        "Please enter a valid email address.",
                        "error"
                    );

                    return;
                }


                // -----------------------------------------
                // DATE VALIDATION
                // -----------------------------------------

                if (
                    dateInput?.min &&
                    date < dateInput.min
                ) {

                    event.preventDefault();

                    showMessage(
                        "Please select today or a future reservation date.",
                        "error"
                    );

                    return;
                }


                // -----------------------------------------
                // VALID FORM
                // -----------------------------------------
                // IMPORTANT:
                // DO NOT use event.preventDefault()
                // here. PHP must receive the POST request.

                if (submitButton) {

                    submitButton.disabled = true;

                    submitButton.innerHTML =
                        'Submitting <i class="bi bi-arrow-repeat"></i>';

                }

            }
        );

    }


    // =================================================
    // CONTACT FORM VALIDATION
    // =================================================

    const contactForm =
        document.getElementById(
            "contactForm"
        );


    if (contactForm) {

        contactForm.addEventListener(
            "submit",
            function (event) {

                if (
                    !contactForm.checkValidity()
                ) {

                    event.preventDefault();

                    event.stopPropagation();

                    contactForm.classList.add(
                        "was-validated"
                    );

                }

            }
        );

    }


    // =================================================
    // HIGHLIGHT TODAY'S OPENING HOURS
    // =================================================

    const hoursList =
        document.getElementById(
            "hoursList"
        );


    if (hoursList) {

        const today =
            new Date().getDay();


        hoursList
            .querySelectorAll(
                "li[data-day]"
            )
            .forEach(
                function (item) {

                    const days =
                        item
                            .getAttribute("data-day")
                            .split(",")
                            .map(
                                function (value) {
                                    return parseInt(value, 10);
                                }
                            );


                    if (days.includes(today)) {

                        item.classList.add(
                            "today"
                        );

                    }

                }
            );

    }


    // =================================================
    // MENU CATEGORY FILTER
    // =================================================

    const categoryButtons =
        document.querySelectorAll(
            ".menu-category-btn"
        );


    const menuSections =
        document.querySelectorAll(
            ".menu-category-section"
        );


    if (
        categoryButtons.length &&
        menuSections.length
    ) {

        categoryButtons.forEach(
            function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        const selectedCategory =
                            button.dataset.category;


                        // ---------------------------------
                        // ACTIVE BUTTON
                        // ---------------------------------

                        categoryButtons.forEach(
                            function (item) {

                                item.classList.toggle(
                                    "active",
                                    item === button
                                );

                            }
                        );


                        // ---------------------------------
                        // SHOW / HIDE SECTIONS
                        // ---------------------------------

                        menuSections.forEach(
                            function (section) {

                                const sectionCategory =
                                    section.dataset
                                        .menuCategory;


                                const showSection =
                                    selectedCategory === "all" ||
                                    selectedCategory ===
                                        sectionCategory;


                                section.style.display =
                                    showSection
                                        ? "block"
                                        : "none";

                            }
                        );

                    }
                );

            }
        );

    }


    // =================================================
    // BACK TO TOP
    // =================================================

    const backToTop =
        document.getElementById(
            "backToTop"
        );


    if (backToTop) {

        function updateBackToTop() {

            backToTop.classList.toggle(
                "show",
                window.scrollY > 300
            );

        }


        window.addEventListener(
            "scroll",
            updateBackToTop,
            {
                passive: true
            }
        );


        updateBackToTop();


        backToTop.addEventListener(
            "click",
            function () {

                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });

            }
        );

    }

})();