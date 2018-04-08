<?php
require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to delete this story");
} else {
    $user_id  = $_SESSION['user_id'];
}

// delete all comments associated with this story
$query                = "DELETE FROM comments WHERE story_id=?";
$delete_comments_stmt = $mysqli->prepare($query);
if (!$delete_comments_stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$story_id = $_POST['id'];

$delete_comments_stmt->bind_param('s', $story_id);
$delete_comments_stmt->execute();
$delete_comments_stmt->close();

// delete all saves
$del_saves_query                = "DELETE FROM story_saves WHERE story_id=?";
$delete_saves_stmt = $mysqli->prepare($del_saves_query);
if (!$delete_saves_stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$delete_saves_stmt->bind_param('s', $story_id);
$delete_saves_stmt->execute();
$delete_saves_stmt->close();

// delete the story
$delete_story_query = "DELETE FROM stories WHERE user_id=? AND id=?";
$delete_story_stmt  = $mysqli->prepare($delete_story_query);
if (!$delete_story_stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$delete_story_stmt->bind_param('ss', $user_id, $story_id);
$delete_story_stmt->execute();
$delete_story_stmt->close();
header("Location: home.php");
?>
