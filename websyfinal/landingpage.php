<?php
require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$fetchTopMealsStmt = $pdo->prepare("SELECT * FROM meals ORDER BY views DESC LIMIT 3");
$fetchTopMealsStmt->execute();
$topMeals = $fetchTopMealsStmt->fetchAll(PDO::FETCH_ASSOC);

$fetchRecentTestimoniesStmt = $pdo->prepare("SELECT * FROM testimonies ORDER BY date_posted DESC LIMIT 5");
$fetchRecentTestimoniesStmt->execute();
$recentTestimonies = $fetchRecentTestimoniesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website Name</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        header {
            background-color: #222;
            color: #fff;
            text-align: center;
            padding: 1em 0;
        }

        section {
            padding: 2em;
        }

        .meal-container {
            display: flex;
            justify-content: space-around;
            margin-top: 2em;
        }

        .meal-card {
            border: 1px solid #ddd;
            padding: 1em;
            width: 30%;
            text-align: center;
            background-color: #fff;
            border-radius: 5px;
            margin-bottom: 1em; /* Add margin-bottom for spacing between cards */
        }

        .meal-card img {
            max-width: 100%;
            border-radius: 5px;
        }

        .meal-description {
            margin-top: 1em;
            color: black;
        }

        .views{
            color: gray;
            font-size: 15px;
        }

        .testimonial-container {
            margin-top: 2em;
        }

        .testimonial-card {
            border: 1px solid #ddd;
            padding: 1em;
            margin-bottom: 1em;
            background-color: #fff;
            border-radius: 5px;
        }

        .get-started-button {
            display: inline-block;
            padding: 1em 2em;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1em;
        }
    </style>
</head>
<body>
    <header>
        <h1>Your Website Name</h1>
    </header>

    <section>
        <p>Welcome to Your Website! Discover delicious meals and read what our customers are saying.</p>

        <div class="meal-container">
            <?php foreach ($topMeals as $meal): ?>
                <div class="meal-card">
                    <h2><?php echo $meal['meal_name']; ?></h2>
                    <p class = "views">Views: <?php echo $meal['views']; ?></p>
                    <div class="meal-description">
                        <?php echo substr($meal['description'], 0, 100); ?>
                    </div>
                    <br>

                    <?php
                    $fetchImagesStmt = $pdo->prepare("SELECT * FROM meal_images WHERE meal_id = ? LIMIT 1");
                    $fetchImagesStmt->execute([$meal['meal_id']]);
                    $image = $fetchImagesStmt->fetch(PDO::FETCH_ASSOC);
                    ?>

                    <?php if ($image): ?>
                        <img src="<?php echo $image['image_link']; ?>" alt="Meal Image">
                    <?php endif; ?>
                    <br><br>

                    <a href="1registration.php"><button type="submit">View More</button></a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="testimonial-container">
            <h2>Recent Testimonies</h2>
            <?php foreach ($recentTestimonies as $testimonial): ?>
                <div class="testimonial-card">
                    <p><?php echo $testimonial['testimonial_text']; ?></p>
                    <p><strong>Posted by:</strong> <?php echo $testimonial['username']; ?></p>
                    <p><strong>Date Posted:</strong> <?php echo $testimonial['date_posted']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="register.php" class="get-started-button">Get Started</a>
    </section>
</body>
</html>
