<?php
require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to delete this comment");
} else {
  $user_id    = $_SESSION['user_id'];
}

$query = "DELETE FROM comments WHERE id=? AND user_id=?";
$stmt  = $mysqli->prepare($query);

if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$comment_id = $_POST['id'];
$story_id   = $_POST['story_id'];
$stmt->bind_param('ss', $comment_id, $user_id);
$stmt->execute();
$stmt->close();
header("Location: view_story.php?id=$story_id");
?>
