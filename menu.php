<?php

require_once "config/database.php";

$pageTitle = "Menu | Bite & Bliss";

include "includes/header.php";

/*
|--------------------------------------------------------------------------
| Get Available Menu Items
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT *
    FROM menu_items
    WHERE status = 1
    ORDER BY
        is_featured DESC,
        FIELD(category, 'starters', 'main', 'desserts', 'drinks'),
        id DESC
");

$menuItems = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| Featured Item
|--------------------------------------------------------------------------
*/

$featured = null;

foreach ($menuItems as $key => $item) {

    if ((int)$item["is_featured"] === 1) {

        $featured = $item;

        unset($menuItems[$key]);

        break;
    }
}

/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

$categories = [

    "starters" => [
        "label" => "Starters",
        "description" =>
            "Start your experience with something delicious."
    ],

    "main" => [
        "label" => "Main Course",
        "description" =>
            "Our most popular dishes prepared fresh for you."
    ],

    "desserts" => [
        "label" => "Desserts",
        "description" =>
            "Finish your meal with something sweet."
    ],

    "drinks" => [
        "label" => "Drinks",
        "description" =>
            "Refreshing drinks to complete your dining experience."
    ]

];

?>

<main class="menu-page">

<!-- ========================================================= -->
<!-- MENU HERO -->
<!-- ========================================================= -->

<section class="menu-hero">

    <div class="menu-hero-content">

        <span class="menu-subtitle">
            Our Menu
        </span>

        <h1>
            Taste Something Special
        </h1>

        <p>
            Explore our carefully selected menu featuring
            delicious dishes prepared with quality ingredients
            and served with care.
        </p>

    </div>

</section>


<!-- ========================================================= -->
<!-- CATEGORY BUTTONS -->
<!-- ========================================================= -->

<section class="menu-categories">

    <div class="menu-category-buttons">

        <button
            type="button"
            class="menu-category-btn active"
            data-category="all"
        >
            All
        </button>

        <?php foreach ($categories as $key => $cat): ?>

            <button
                type="button"
                class="menu-category-btn"
                data-category="<?= htmlspecialchars($key) ?>"
            >
                <?= htmlspecialchars($cat["label"]) ?>
            </button>

        <?php endforeach; ?>

    </div>

</section>


<!-- ========================================================= -->
<!-- MENU SECTION -->
<!-- ========================================================= -->

<section class="menu-section">

    <div class="menu-container">


        <!-- ================================================= -->
        <!-- FEATURED ITEM -->
        <!-- ================================================= -->

        <?php if ($featured): ?>

            <div
                class="featured-menu"
                data-menu-category="<?= htmlspecialchars(
                    $featured["category"]
                ) ?>"
            >

                <div class="featured-menu-inner">


                    <!-- IMAGE -->

                    <div class="featured-menu-image">

                        <img
                            src="<?= htmlspecialchars(
                                $featured["image"]
                                    ? "admin/" . $featured["image"]
                                    : "assets/images/hero-food.jpg"
                            ) ?>"
                            alt="<?= htmlspecialchars(
                                $featured["name"]
                            ) ?>"
                        >

                    </div>


                    <!-- CONTENT -->

                    <div class="featured-menu-content">

                        <span class="featured-label">
                            Chef's Special
                        </span>

                        <h2>
                            <?= htmlspecialchars(
                                $featured["name"]
                            ) ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars(
                                $featured["description"] ?? ""
                            ) ?>
                        </p>

                        <span class="featured-price">
                            Rs.
                            <?= number_format(
                                (float)$featured["price"],
                                2
                            ) ?>
                        </span>


                        <!-- FEATURED ACTIONS -->

                        <div
                            class="menu-card-footer mt-3"
                        >

                            <!-- ADD TO CART -->

                            <form
                                action="cart/add.php"
                                method="POST"
                                class="d-inline"
                            >

                                <input
                                    type="hidden"
                                    name="menu_item_id"
                                    value="<?= (int)$featured["id"] ?>"
                                >

                                <input
                                    type="hidden"
                                    name="quantity"
                                    value="1"
                                >

                                <button
                                    type="submit"
                                    class="menu-book-btn"
                                >
                                    <i
                                        class="bi bi-cart-plus"
                                        aria-hidden="true"
                                    ></i>

                                    Add to Cart
                                </button>

                            </form>


                            <!-- RESERVATION -->

                            <a
                                href="reservations.php"
                                class="menu-book-btn"
                            >
                                Reserve a Table

                                <i
                                    class="bi bi-arrow-right"
                                    aria-hidden="true"
                                ></i>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php endif; ?>


        <!-- ================================================= -->
        <!-- NORMAL MENU CATEGORIES -->
        <!-- ================================================= -->

        <?php foreach ($categories as $key => $cat): ?>

            <?php

            $items = array_filter(
                $menuItems,
                fn($item) =>
                    $item["category"] === $key
            );

            if (!$items) {
                continue;
            }

            ?>

            <div
                class="menu-category-section"
                data-menu-category="<?= htmlspecialchars($key) ?>"
            >


                <!-- CATEGORY TITLE -->

                <div class="menu-section-title">

                    <h2>
                        <?= htmlspecialchars(
                            $cat["label"]
                        ) ?>
                    </h2>

                    <p>
                        <?= htmlspecialchars(
                            $cat["description"]
                        ) ?>
                    </p>

                </div>


                <!-- MENU GRID -->

                <div class="menu-grid">

                    <?php foreach ($items as $item): ?>

                        <article class="menu-card">


                            <!-- IMAGE -->

                            <div class="menu-card-image">

                                <img
                                    src="<?= htmlspecialchars(
                                        $item["image"]
                                            ? "admin/" . $item["image"]
                                            : "assets/images/hero-food.jpg"
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $item["name"]
                                    ) ?>"
                                    loading="lazy"
                                >

                            </div>


                            <!-- CONTENT -->

                            <div class="menu-card-content">


                                <!-- NAME + PRICE -->

                                <div class="menu-card-header">

                                    <h3>
                                        <?= htmlspecialchars(
                                            $item["name"]
                                        ) ?>
                                    </h3>

                                    <span class="menu-price">

                                        Rs.
                                        <?= number_format(
                                            (float)$item["price"],
                                            2
                                        ) ?>

                                    </span>

                                </div>


                                <!-- DESCRIPTION -->

                                <p>

                                    <?= htmlspecialchars(
                                        $item["description"] ?? ""
                                    ) ?>

                                </p>


                                <!-- ACTIONS -->

                                <div class="menu-card-footer">


                                    <!-- ADD TO CART -->

                                    <form
                                        action="cart/add.php"
                                        method="POST"
                                        class="menu-cart-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="menu_item_id"
                                            value="<?= (int)$item["id"] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="quantity"
                                            value="1"
                                        >

                                        <button
                                            type="submit"
                                            class="menu-book-btn"
                                        >

                                            <i
                                                class="bi bi-cart-plus"
                                                aria-hidden="true"
                                            ></i>

                                            Add to Cart

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php endforeach; ?>


        <!-- ================================================= -->
        <!-- EMPTY STATE -->
        <!-- ================================================= -->

        <?php if (!$menuItems && !$featured): ?>

            <div class="menu-empty-state">

                <i class="bi bi-journal-text"></i>

                <h2>
                    Our menu is being prepared
                </h2>

                <p>
                    Please check back soon for our latest dishes.
                </p>

            </div>

        <?php endif; ?>


    </div>

</section>


<!-- ========================================================= -->
<!-- CTA -->
<!-- ========================================================= -->

<section class="menu-cta">

    <div class="menu-cta-content">

        <span class="menu-cta-subtitle">
            Your Table Awaits
        </span>

        <h2>
            Ready to Dine With Us?
        </h2>

        <p>
            Reserve your table and enjoy a memorable dining
            experience at Bite &amp; Bliss.
        </p>

        <a
            href="reservations.php"
            class="menu-cta-btn"
        >

            Reserve a Table

            <i
                class="bi bi-arrow-right"
                aria-hidden="true"
            ></i>

        </a>

    </div>

</section>


</main>

<?php include "includes/footer.php"; ?>