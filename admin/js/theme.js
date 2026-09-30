// ADMIN - DARK / LIGHT MODE

(function () {

    var STORAGE_KEY = "bb-admin-theme";
    var root = document.documentElement;

    function getSavedTheme() {
        try {
            return localStorage.getItem(STORAGE_KEY) || "dark";
        } catch (e) {
            return "dark";
        }
    }

    function applyTheme(theme) {

        root.setAttribute("data-theme", theme);

        var button = document.getElementById("themeToggle");

        if (button) {
            button.querySelector("i").className =
                theme === "dark" ? "bi bi-sun" : "bi bi-moon-stars";

            button.querySelector("span").textContent =
                theme === "dark" ? "Light Mode" : "Dark Mode";
        }

        // lets other scripts (like the charts in admin.js) react
        document.dispatchEvent(new CustomEvent("themechange"));
    }

    // 1) runs while <head> is loading, so the page never flashes the wrong theme
    root.setAttribute("data-theme", getSavedTheme());

    // 2) once the page exists, set the button label and wire the click
    document.addEventListener("DOMContentLoaded", function () {

        applyTheme(getSavedTheme());
        var button = document.getElementById("themeToggle");

        if (!button) return;

        button.addEventListener("click", function () {

            var next = root.getAttribute("data-theme") === "dark" ? "light" : "dark";

            try {
                localStorage.setItem(STORAGE_KEY, next);
            } catch (e) {}

            applyTheme(next);
        });
    });

})();