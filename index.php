
<?php

/*
|--------------------------------------------------------------------------
| HOME PAGE
|--------------------------------------------------------------------------
*/

$pageTitle = "Home | Bite & Bliss";

include 'includes/header.php';


/*
|--------------------------------------------------------------------------
| MENU DATA
|--------------------------------------------------------------------------
|
| header.php already loads database.php and creates $pdo.
|
*/

$featuredItems = [];
$categoryCounts = [];

$totalMenuItems = 0;


/*
|--------------------------------------------------------------------------
| GET FEATURED / POPULAR MENU ITEMS
|--------------------------------------------------------------------------
|
| First get items marked as featured.
| If fewer than 3 featured items exist, fill the remaining
| positions with the newest active menu items.
|
*/

try {

    $stmt = $pdo->query("
        SELECT
            id,
            name,
            category,
            description,
            price,
            image,
            status,
            is_featured
        FROM menu_items
        WHERE status = 1
        AND is_featured = 1
        ORDER BY RAND()
        LIMIT 3");

    $featuredItems = $stmt->fetchAll();


    /*
    | If there are no featured items,
    | get the latest active dishes.
    */

    if (count($featuredItems) < 3) {

        $existingIds = [];

        foreach ($featuredItems as $item) {
            $existingIds[] = (int)$item['id'];
        }


        if (!empty($existingIds)) {

            $placeholders = implode(
                ',',
                array_fill(
                    0,
                    count($existingIds),
                    '?'
                )
            );

            $sql = "
                SELECT
                    id,
                    name,
                    category,
                    description,
                    price,
                    image,
                    status,
                    is_featured
                FROM menu_items
                WHERE status = 1
                AND id NOT IN ($placeholders)
                ORDER BY RAND()
                LIMIT " . (3 - count($featuredItems));

            $stmt = $pdo->prepare($sql);

            $stmt->execute($existingIds);

        } else {

            $stmt = $pdo->query("
                SELECT
                    id,
                    name,
                    category,
                    description,
                    price,
                    image,
                    status,
                    is_featured
                FROM menu_items
                WHERE status = 1
                ORDER BY RAND()
                LIMIT 3
            ");
        }


        $additionalItems = $stmt->fetchAll();

        $featuredItems = array_merge(
            $featuredItems,
            $additionalItems
        );
    }


} catch (PDOException $e) {

    $featuredItems = [];
}


/*
|--------------------------------------------------------------------------
| TOTAL ACTIVE MENU ITEMS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT COUNT(*)
        FROM menu_items
        WHERE status = 1
    ");

    $totalMenuItems = (int)$stmt->fetchColumn();

} catch (PDOException $e) {

    $totalMenuItems = 0;
}


/*
|--------------------------------------------------------------------------
| CATEGORY COUNTS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->query("
        SELECT
            category,
            COUNT(*) AS total
        FROM menu_items
        WHERE status = 1
        GROUP BY category
    ");

    foreach ($stmt->fetchAll() as $row) {

        $categoryCounts[
            strtolower($row['category'])
        ] = (int)$row['total'];
    }

} catch (PDOException $e) {

    $categoryCounts = [];
}


/*
|--------------------------------------------------------------------------
| CATEGORY HELPER
|--------------------------------------------------------------------------
*/

function homeCategoryCount($category, $categoryCounts)
{
    return $categoryCounts[
        strtolower($category)
    ] ?? 0;
}


/*
|--------------------------------------------------------------------------
| CATEGORY LABEL
|--------------------------------------------------------------------------
*/

function homeCategoryLabel($category)
{
    $labels = [

        'starters' => 'Starters',

        'main' => 'Main Course',

        'desserts' => 'Desserts',

        'drinks' => 'Drinks'

    ];

    return $labels[
        strtolower($category)
    ] ?? ucfirst($category);
}


/*
|--------------------------------------------------------------------------
| CATEGORY ICON
|--------------------------------------------------------------------------
*/

function homeCategoryIcon($category)
{
    $icons = [

        'starters' => 'bi-fire',

        'main' => 'bi-egg-fried',

        'desserts' => 'bi-cake2',

        'drinks' => 'bi-cup-straw'

    ];

    return $icons[
        strtolower($category)
    ] ?? 'bi-egg-fried';
}


/*
|--------------------------------------------------------------------------
| CATEGORY DESCRIPTION
|--------------------------------------------------------------------------
*/

function homeCategoryDescription($category)
{
    $descriptions = [

        'starters' =>
            'Delicious starters prepared to begin your meal perfectly.',

        'main' =>
            'Rich and satisfying main dishes prepared with fresh ingredients.',

        'desserts' =>
            'Sweet and delicious treats to complete your meal.',

        'drinks' =>
            'Refreshing drinks to perfectly complement your meal.'

    ];

    return $descriptions[
        strtolower($category)
    ] ?? 'Delicious dishes prepared with care and quality ingredients.';
}


/*
|--------------------------------------------------------------------------
| DISH IMAGE
|--------------------------------------------------------------------------
*/

function homeDishImage($image)
{
    if (!empty($image)) {

        return 'admin/' . ltrim(
            $image,
            '/'
        );
    }

    return 'assets/images/hero-food.jpg';
}


/*
|--------------------------------------------------------------------------
| DISH BADGE
|--------------------------------------------------------------------------
*/

function homeDishBadge($item)
{
    if ((int)$item['is_featured'] === 1) {
        return 'Featured';
    }

    return 'Popular';
}


/*
|--------------------------------------------------------------------------
| CUSTOMER REVIEWS
|--------------------------------------------------------------------------
|
| Reviews are loaded dynamically from contact_messages.
| Only messages whose subject is "Feedback" are shown.
|
*/

$reviews = [];

try {

    $reviewStmt = $pdo->query("
        SELECT name, message, created_at
        FROM contact_messages
        WHERE subject = 'Feedback'
        ORDER BY created_at DESC
        LIMIT 3
    ");

    $reviews = $reviewStmt->fetchAll();

} catch (PDOException $e) {

    $reviews = [];
}

?>

<main>


    <!-- =====================================================
         HERO SECTION
    ====================================================== -->

    <section class="hero-section">

        <div class="container">

            <div class="row align-items-center min-vh-100">


                <!-- HERO CONTENT -->

                <div class="col-lg-6">

                    <span class="hero-subtitle">

                        WELCOME TO
                        <?= htmlspecialchars(
                            $settings['restaurant_name']
                            ?? 'BITE & BLISS',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>


                    <h1>

                        Taste the
                        <span>Happiness</span>
                        in Every Bite

                    </h1>


                    <p>

                        Experience delicious Pakistani and continental cuisine
                        prepared with fresh ingredients, authentic flavors,
                        and a passion for great food.

                    </p>


                    <div class="hero-buttons">

                        <a
                            href="menu.php"
                            class="btn btn-primary-custom">

                            Explore Menu

                            <i class="bi bi-arrow-right"></i>

                        </a>


                        <a
                            href="reservations.php"
                            class="btn btn-outline-custom">

                            Reserve a Table

                        </a>

                    </div>


                    <div class="hero-info">


                        <!-- HOURS -->

                        <div>

                            <i
                                class="bi bi-clock"
                                aria-hidden="true">
                            </i>

                            <span>

                                Open Daily<br>

                                <strong>

                                    <?= htmlspecialchars(
                                        $settings['hours_weekday']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </strong>

                            </span>

                        </div>


                        <!-- PHONE -->

                        <div>

                            <i
                                class="bi bi-telephone"
                                aria-hidden="true">
                            </i>

                            <span>

                                Call Us<br>

                                <strong>

                                    <?= htmlspecialchars(
                                        $settings['phone']
                                        ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </strong>

                            </span>

                        </div>


                    </div>

                </div>


                <!-- HERO IMAGE -->

                <div class="col-lg-6">

                    <div class="hero-image-wrapper">

                        <div class="hero-image-circle">

                            <img
                                src="assets/images/hero-food.jpg"
                                alt="Delicious food at Bite & Bliss"
                                class="hero-image">

                        </div>


                        <div class="hero-badge">

                            <strong>
                                <?= $totalMenuItems ?>+
                            </strong>

                            <span>
                                Delicious Dishes
                            </span>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </section>



    <!-- =====================================================
         ABOUT SECTION
    ====================================================== -->

    <section class="about-section py-5">

        <div class="container py-5">

            <div class="row align-items-center g-5">


                <!-- IMAGE -->

                <div class="col-lg-6">

                    <div class="about-image-wrapper">

                        <div class="about-image-box">

                           <img src="./assets/images/about-1.jpg" alt="">

                        </div>


                        <div class="about-experience">

                            <strong>
                                10+
                            </strong>

                            <span>
                                Years of Experience
                            </span>

                        </div>

                    </div>

                </div>


                <!-- CONTENT -->

                <div class="col-lg-6">

                    <span class="section-subtitle">

                        ABOUT
                        <?= htmlspecialchars(
                            $settings['restaurant_name']
                            ?? 'BITE & BLISS',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </span>


                    <h2 class="section-title">

                        Where Great Food Meets
                        <span>Great Moments</span>

                    </h2>


                    <p class="section-text">

                        At Bite & Bliss, we believe that food is more than
                        just a meal. It is an experience that brings people
                        together.

                    </p>


                    <p class="section-text">

                        Our kitchen combines traditional flavors with modern
                        culinary ideas to create dishes that everyone can enjoy.

                    </p>


                    <div class="about-features">


                        <div class="about-feature">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Fresh & Quality Ingredients
                            </span>

                        </div>


                        <div class="about-feature">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Experienced Chefs
                            </span>

                        </div>


                        <div class="about-feature">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Fast & Friendly Service
                            </span>

                        </div>


                        <div class="about-feature">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Comfortable Dining Experience
                            </span>

                        </div>


                    </div>


                    <a
                        href="about.php"
                        class="btn btn-primary-custom mt-3">

                        Discover More

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>


            </div>

        </div>

    </section>



    <!-- =====================================================
         FOOD CATEGORIES
    ====================================================== -->

    <section class="categories-section py-5">

        <div class="container py-5">


            <div class="section-heading text-center mb-5">

                <span class="section-subtitle">
                    EXPLORE OUR MENU
                </span>


                <h2 class="section-title">

                    Choose Your
                    <span>Favorite</span>

                </h2>


                <p class="section-text mx-auto">

                    Discover delicious dishes prepared with fresh ingredients
                    and authentic flavors.

                </p>

            </div>


            <div class="row g-4">


                <?php

                $categories = [

                    'starters',
                    'main',
                    'desserts',
                    'drinks'

                ];

                ?>


                <?php foreach ($categories as $category): ?>


                    <div class="col-lg-3 col-md-6">


                        <div class="category-card">


                            <div class="category-icon">

                                <i
                                    class="bi <?= homeCategoryIcon($category) ?>"
                                    aria-hidden="true">
                                </i>

                            </div>


                            <h4>

                                <?= htmlspecialchars(
                                    homeCategoryLabel($category),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </h4>


                            <p>

                                <?= htmlspecialchars(
                                    homeCategoryDescription($category),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </p>

                            <a href="menu.php">

                                Explore

                                <i
                                    class="bi bi-arrow-right"
                                    aria-hidden="true">
                                </i>

                            </a>


                        </div>

                    </div>


                <?php endforeach; ?>


            </div>

        </div>

    </section>



    <!-- =====================================================
         POPULAR / FEATURED DISHES
    ====================================================== -->

    <section class="popular-section py-5">

        <div class="container py-5">


            <div class="section-heading text-center mb-5">

                <span class="section-subtitle">

                    CUSTOMER FAVORITES

                </span>


                <h2 class="section-title">

                    Our
                    <span>Popular Dishes</span>

                </h2>


                <p class="section-text mx-auto">

                    Enjoy some of our most loved dishes, carefully prepared
                    to make every meal special.

                </p>

            </div>


            <div class="row g-4">


                <?php if (!empty($featuredItems)): ?>


                    <?php foreach (
                        array_slice(
                            $featuredItems,
                            0,
                            6
                        ) as $item
                    ): ?>


                        <div class="col-lg-4 col-md-6">


                            <div class="dish-card">


                                <!-- IMAGE -->

                                <div class="dish-image">


                                    <img
                                        src="<?= htmlspecialchars(
                                            homeDishImage(
                                                $item['image']
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $item['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        class="w-100 h-100"
                                        style="object-fit:cover;"
                                        loading="lazy">


                                    <span class="dish-badge">

                                        <?= htmlspecialchars(
                                            homeDishBadge($item),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>


                                </div>


                                <!-- CONTENT -->

                                <div class="dish-content">


                                    <div class="dish-rating">

                                        <i
                                            class="bi bi-star-fill"
                                            aria-hidden="true">
                                        </i>

                                        <span>
                                            Featured Dish
                                        </span>

                                    </div>


                                    <h4>

                                        <?= htmlspecialchars(
                                            $item['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </h4>


                                    <p>

                                        <?= htmlspecialchars(
                                            $item['description']
                                                ?: 'Deliciously prepared with fresh ingredients and authentic flavors.',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </p>


                                    <div class="dish-bottom">


                                        <strong>

                                            Rs.
                                            <?= number_format(
                                                (float)$item['price'],
                                                2
                                            ) ?>

                                        </strong>


                                        <form
                                            action="cart/add.php"
                                            method="POST"
                                            class="d-inline">


                                            <input
                                                type="hidden"
                                                name="menu_item_id"
                                                value="<?= (int)$item['id'] ?>">


                                            <input
                                                type="hidden"
                                                name="quantity"
                                                value="1">


                                            <button
                                                type="submit"
                                                class="dish-btn"
                                                aria-label="Add <?= htmlspecialchars(
                                                    $item['name'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?> to cart">

                                                <i
                                                    class="bi bi-plus-lg"
                                                    aria-hidden="true">
                                                </i>

                                            </button>


                                        </form>


                                    </div>


                                </div>


                            </div>

                        </div>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="col-12">

                        <div class="text-center py-5">

                            <i
                                class="bi bi-egg-fried"
                                style="font-size:50px;">
                            </i>


                            <h4 class="mt-3">

                                Menu Coming Soon

                            </h4>


                            <p>

                                Our delicious dishes will appear here
                                once they are added to the menu.

                            </p>


                        </div>

                    </div>


                <?php endif; ?>


            </div>


            <div class="text-center mt-5">


                <a
                    href="menu.php"
                    class="btn btn-outline-custom">

                    View Full Menu

                    <i class="bi bi-arrow-right"></i>

                </a>


            </div>


        </div>

    </section>



    <!-- =====================================================
         SPECIAL OFFERS
    ====================================================== -->

    <section class="offers-section py-5">

        <div class="container py-5">

            <div class="row align-items-center g-4">


                <div class="col-lg-7">


                    <span class="section-subtitle">

                        LIMITED TIME OFFER

                    </span>


                    <h2 class="section-title">

                        Delicious Food,
                        <span>Special Prices</span>

                    </h2>


                    <p class="section-text">

                        Enjoy your favorite meals at special prices.
                        Treat yourself and your loved ones to delicious
                        food without breaking the bank.

                    </p>


                    <div class="offer-details">


                        <div class="offer-item">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Freshly prepared meals
                            </span>

                        </div>


                        <div class="offer-item">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Available for dine-in & takeaway
                            </span>

                        </div>


                        <div class="offer-item">

                            <i
                                class="bi bi-check-circle-fill"
                                aria-hidden="true">
                            </i>

                            <span>
                                Special offers on selected dishes
                            </span>

                        </div>


                    </div>


                    <a
                        href="menu.php"
                        class="btn btn-primary-custom mt-4">

                        Order Now

                        <i class="bi bi-arrow-right"></i>

                    </a>


                </div>


                <div class="col-lg-5">


                    <div class="offer-card">


                        <span class="offer-label">

                            TODAY'S SPECIAL

                        </span>


                        <h3>
                            20% OFF
                        </h3>


                        <p>
                            On selected dishes
                        </p>


                        <div class="offer-code">

                            Use Code:

                            <strong>
                                BITE20
                            </strong>

                        </div>


                        <small>

                            *Terms & conditions apply

                        </small>


                    </div>


                </div>


            </div>

        </div>

    </section>



    <!-- =====================================================
         WHY CHOOSE US
    ====================================================== -->

    <section class="why-us-section py-5">

        <div class="container py-5">


            <div class="section-heading text-center mb-5">


                <span class="section-subtitle">

                    WHY BITE & BLISS

                </span>


                <h2 class="section-title">

                    Why Customers
                    <span>Choose Us</span>

                </h2>


                <p class="section-text mx-auto">

                    We focus on quality food, great service and a dining
                    experience that keeps our customers coming back.

                </p>


            </div>


            <div class="row g-4">


                <div class="col-lg-3 col-md-6">

                    <div class="why-card">

                        <div class="why-icon">

                            <i class="bi bi-award"></i>

                        </div>

                        <h4>
                            Quality Food
                        </h4>

                        <p>
                            Carefully prepared dishes made with
                            fresh and quality ingredients.
                        </p>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="why-card">

                        <div class="why-icon">

                            <i class="bi bi-clock-history"></i>

                        </div>

                        <h4>
                            Fast Service
                        </h4>

                        <p>
                            Quick and efficient service without
                            compromising food quality.
                        </p>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="why-card">

                        <div class="why-icon">

                            <i class="bi bi-people"></i>

                        </div>

                        <h4>
                            Friendly Staff
                        </h4>

                        <p>
                            Our team is always ready to make
                            your dining experience special.
                        </p>

                    </div>

                </div>


                <div class="col-lg-3 col-md-6">

                    <div class="why-card">

                        <div class="why-icon">

                            <i class="bi bi-heart"></i>

                        </div>

                        <h4>
                            Made With Love
                        </h4>

                        <p>
                            Every dish is prepared with care,
                            passion and attention to detail.
                        </p>

                    </div>

                </div>


            </div>

        </div>

    </section>



    <!-- =====================================================
         CUSTOMER REVIEWS
    ====================================================== -->

    <section class="reviews-section py-5">

        <div class="container py-5">

            <div class="section-heading text-center mb-5">

                <span class="section-subtitle">
                    CUSTOMER REVIEWS
                </span>

                <h2 class="section-title">
                    What Our Customers
                    <span>Say</span>
                </h2>

                <p class="section-text mx-auto">
                    Our customers make every meal special. Here's what
                    they have to say about their Bite & Bliss experience.
                </p>

            </div>

            <div class="row g-4">

                <?php if (!empty($reviews)): ?>

                    <?php foreach ($reviews as $review): ?>

                        <?php
                        $reviewName = trim((string)$review['name']);
                        $reviewMessage = trim((string)$review['message']);
                        $reviewInitial = strtoupper(substr($reviewName !== '' ? $reviewName : 'C', 0, 1));
                        ?>

                        <div class="col-lg-4 col-md-6">
                            <div class="review-card">
                                <div class="review-stars" aria-label="Customer feedback">
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                </div>

                                <p class="review-text">
                                    &quot;<?= htmlspecialchars($reviewMessage, ENT_QUOTES, 'UTF-8') ?>&quot;
                                </p>

                                <div class="review-user">
                                    <div class="review-avatar">
                                        <?= htmlspecialchars($reviewInitial, ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                    <div>
                                        <h5><?= htmlspecialchars($reviewName, ENT_QUOTES, 'UTF-8') ?></h5>
                                        <span>Customer</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-12">
                        <div class="review-card text-center">
                            <div class="review-stars justify-content-center">
                                <i class="bi bi-star" aria-hidden="true"></i>
                                <i class="bi bi-star" aria-hidden="true"></i>
                                <i class="bi bi-star" aria-hidden="true"></i>
                                <i class="bi bi-star" aria-hidden="true"></i>
                                <i class="bi bi-star" aria-hidden="true"></i>
                            </div>
                            <p class="review-text mb-0">
                                No customer reviews have been added yet.
                            </p>
                        </div>
                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>



    <!-- =====================================================
         RESERVATION CTA
    ====================================================== -->

    <section class="reservation-section py-5">

        <div class="container py-5">


            <div class="reservation-box">


                <div class="row align-items-center g-4">


                    <div class="col-lg-8">


                        <span class="section-subtitle">

                            RESERVE YOUR TABLE

                        </span>


                        <h2>

                            Make Your Next Meal
                            <span>Extra Special</span>

                        </h2>


                        <p>

                            Planning a family dinner, date night or gathering?
                            Reserve your table with us and enjoy a memorable
                            dining experience.

                        </p>


                    </div>


                    <div class="col-lg-4 text-lg-end">


                        <a
                            href="reservations.php"
                            class="btn btn-primary-custom">

                            Reserve a Table

                            <i class="bi bi-calendar-check"></i>

                        </a>


                    </div>


                </div>


            </div>

        </div>

    </section>


</main>


<?php include 'includes/footer.php'; ?>

