<?php
require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to unsave this story");
} else {
    $user_id = $_SESSION['user_id'];
}

$query = "DELETE FROM story_saves WHERE story_id=? AND user_id=?";
$stmt  = $mysqli->prepare($query);
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$story_id = $_POST['id'];
$stmt->bind_param('ss', $story_id, $user_id);
$stmt->execute();
$stmt->close();
header("Location: home.php");
?>
