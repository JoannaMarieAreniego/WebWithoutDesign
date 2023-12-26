<?php
session_start();
require("0conn.php");

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (!isset($_SESSION["is_admin"]) || $_SESSION["is_admin"] !== true) {
    echo "You must login first";
    header("Refresh: 2; url=3login.php");
    session_destroy();
    exit();
}

if (isset($_GET["recipe_id"])) {
    $recipe_id = $_GET["recipe_id"];
    
    $stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    $instructions = getInstructions($pdo, $recipe_id);
    $ingredients = getIngredients($pdo, $recipe_id);
    $images = getImages($pdo, $recipe_id);
    
} else {
    echo "Recipe not found.";
    exit();
}

function getImages($pdo, $meal_id) {
    $stmt = $pdo->prepare("SELECT * FROM meal_images WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getInstructions($pdo, $meal_id) {
    $stmt = $pdo->prepare("SELECT * FROM instructions WHERE meal_id = ? ORDER BY step_number");
    $stmt->execute([$meal_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getIngredients($pdo, $meal_id) {
    $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE meal_id = ?");
    $stmt->execute([$meal_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-color: #ededed;
            filter: blur(15px);
            z-index: -1;
        }

        .logo-container {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
             justify-content: center; 
             align-items: center;
            background-color: #18392B; /* Green background */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 60px;
            padding: 20px;
            width: auto;
            margin-right: 10px;
        }

        .logo h1 {
            font-family: cursive;
            font-size: 24px;
            margin: 0;
            color: #fff; /* White text */
        }


        h3 {
            /* margin-top: 70px; */
            color: #fff; /* White text */
            font-weight: bold;
            padding: 20px;
        }
        .container {
            max-width: 700px; 
            margin: 70px auto;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #4caf50;
            color: #FFF;
            font-weight: bold;
            text-align: center;
        }
        .card-body {
            padding: 20px;
        }
        .card img {
         display: block;
         margin: 0 auto;
         width: 600px;
        max-width: 100%;
        height: 400px;
         margin-bottom: 20px;
        border-radius: 6px;
}
        .card-text {
            margin-bottom: 10px;
            
        }

        .card-title {
            color: #4caf50;
        }

        ul, ol {
            margin-bottom: 15px;
        }

        a {
            color: #4caf50;;
            text-decoration: none;
        }

        .btn-secondary {
            background-color: #4caf50;
            color: #fff;
        }

        .dashboard{
           margin-left: 1100px;
        align-items: center;
        justify-items: center;
        }

    </style>
</head>

<body>

<div class="logo-container">
        <div class="logo">
            <img src="logo.png" alt="Tastebud Logo">
            <h1>Tastebud</h1>
        </div>
        <div class="dashboard">
        <a href="8category_page.php?category_id=<?php echo $recipe['category_id']; ?>"
                class="btn btn-secondary">Back to Categories</a>
</div>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><?php echo $recipe['meal_name']; ?></h2>
            </div>
            <div class="card-body">
                <p class="card-text">Category: <?php echo $recipe['category_id']; ?></p>
                <p class="card-text">Video Link: <a href="<?php echo $recipe['video_link']; ?>" target="_blank"><?php echo $recipe['video_link']; ?></a></p>

                <h3 class="card-title">Images</h3>
                    <?php foreach ($images as $image) { ?>
                        <img src="<?php echo $image['image_link']; ?>" alt="Recipe Image">
                <?php } ?>

                <h3 class="card-title">Instructions</h3>
                <ol class="card-text">
                    <?php foreach ($instructions as $instruction) { ?>
                        <li><?php echo $instruction['step_description']; ?></li>
                    <?php } ?>
                </ol>

                <h3 class="card-title">Ingredients</h3>
                <ul class="card-text">
                    <?php foreach ($ingredients as $ingredient) { ?>
                        <li><?php echo $ingredient['ingredient_name']; ?></li>
                    <?php } ?>
                </ul>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
