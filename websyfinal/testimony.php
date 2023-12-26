<?php
session_start();
require("0conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['username'])) {
        header("Location: 3login.php");
        exit();
    }

    $username = $_SESSION['username'];
    $testimonial_text = $_POST['testimonial_text'];
    $testimonial_text = implode(' ', array_slice(str_word_count($testimonial_text, 2), 0, 100));

    try {
        $stmt = $conn->prepare("INSERT INTO testimonies (username, testimonial_text, date_posted) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $testimonial_text);
        $stmt->execute();
        $stmt->close();
        header("Location: testimony.php");
        exit();
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write Testimonies</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>

<div class="container">
    <h2>Write Testimonies</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="testimonial_text">Your Testimony (up to 100 words):</label>
        <textarea name="testimonial_text" rows="4" cols="50" required></textarea>
        <br>
        <input type="submit" value="Submit Testimony">
    </form>
</div>

</body>
</html>
