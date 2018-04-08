<?php
require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

$error_code = 0;
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to save a story");
} else {
    $user_id = $_SESSION['user_id'];
}

if (empty($_POST['id'])) {
    die("Story ID cannot be empty to save a story");
} else {
    $story_id = $_POST['id'];
}

// check that the story was not already saved by the user. if it was, exit
$safe_user_id  = $mysqli->real_escape_string($user_id);
$safe_story_id = $mysqli->real_escape_string($story_id);

$query = "SELECT * FROM story_saves
INNER JOIN users on story_saves.user_id=users.id
WHERE users.id='" . $safe_user_id . "' AND story_saves.story_id='" . $safe_story_id . "'";
$res   = $mysqli->query($query);

if ($res->num_rows > 0) {
    // story already saved
    $error_code = 1;
    header("Location: home.php?error_code=$error_code");
    exit;
}

$insert_query = "INSERT INTO story_saves (user_id, story_id) VALUES (?, ?)";
$stmt         = $mysqli->prepare($insert_query);
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('ss', $user_id, $story_id);
$stmt->execute();
$stmt->close();
header("Location: home.php");
?>
