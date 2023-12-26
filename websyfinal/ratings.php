<?php
session_start();
require("0conn.php");

// Check if meal_id is set in the URL
if (isset($_GET['meal_id'])) {
    $meal_id = $_GET['meal_id'];

    // Check if the user is logged in
    if (!isset($_SESSION['username'])) {
        header("Location: 9customer.php");
        exit();
    }

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Fetch all ratings
        $fetchAllRatingsStmt = $pdo->prepare("SELECT * FROM ratings WHERE meal_id = ?");
        $fetchAllRatingsStmt->execute([$meal_id]);
        $allRatings = $fetchAllRatingsStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // Check if the submitted form is for adding a new rating
        if (isset($_POST['rating_value'])) {
            $rating_value = filter_input(INPUT_POST, 'rating_value', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 5)));
            $rating_comment = $_POST['rating_comment']; // Get the rating comment

            if ($rating_value !== false) {
                // Check if the user has already rated this meal
                $existingRatingStmt = $pdo->prepare("SELECT * FROM ratings WHERE meal_id = ? AND username = ?");
                $existingRatingStmt->execute([$meal_id, $_SESSION['username']]);
                $existingRating = $existingRatingStmt->fetch(PDO::FETCH_ASSOC);

                // Insert or update the rating
                if (!$existingRating) {
                    $insertRatingStmt = $pdo->prepare("INSERT INTO ratings (meal_id, username, rating_value, rating_comment, date_rated) VALUES (?, ?, ?, ?, NOW())");
                    $insertRatingStmt->execute([$meal_id, $_SESSION['username'], $rating_value, $rating_comment]);
                }
            }
        }
        $fetchRatingsStmt = $pdo->prepare("SELECT * FROM ratings WHERE meal_id = ?");
        $fetchRatingsStmt->execute([$meal_id]);
        $ratings = $fetchRatingsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    header("Location: 12user_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
    <form method="post" action="">
        <h2>Meal Ratings</h2>
        <?php if (count($allRatings) > 0): ?>
            <ul>
                <?php foreach ($allRatings as $rating): ?>
                    <li>
                        <strong>Username:</strong> <?php echo $rating['username']; ?><br>
                        <strong>Rating:</strong> <?php echo $rating['rating_value']; ?><br>
                        <strong>Comment:</strong> <?php echo $rating['rating_comment']; ?><br>
                        <strong>Date Rated:</strong> <?php echo $rating['date_rated']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No ratings available for this meal.</p>
        <?php endif; ?>

        <label for="rating_value">Rate this Meal:</label>
        <select name="rating_value" required>
            <option value="1">1 - Very Bad</option>
            <option value="2">2 - Bad</option>
            <option value="3">3 - Average</option>
            <option value="4">4 - Good</option>
            <option value="5">5 - Excellent</option>
        </select>
        <br><br>
        
        <!-- Add a textarea for the rating comment -->
        <label for="rating_comment">Add a Comment:</label>
        <textarea name="rating_comment" rows="4" cols="50" required></textarea>
        <br><br>
        
        <button type="submit" name="submit">Submit Rating</button>
    </form>
</body>
</html>