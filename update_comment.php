<?php
require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

$story_id = $_POST['story_id'];
$error_code = 0;
if (!isset($_SESSION['user_id'])) {
    $error_code = 1;
    header("Location: edit_comment.php?id=$comment_id&error_code=$error_code");
    exit;
} else {
    $user_id = $_SESSION['user_id'];
}

if (empty($_POST['body'])) {
    $error_code = 2;
    header("Location: edit_comment.php?id=$comment_id&error_code=$error_code");
    exit;
} else {
    $body = trim($_POST['body']);
}

if (empty($story_id)) {
    $error_code = 3;
    header("Location: edit_comment.php?id=$comment_id&error_code=$error_code");
    exit;
}

if (empty($_POST['id'])) {
    $error_code = 4;
    header("Location: edit_comment.php?id=$comment_id&error_code=$error_code");
    exit;
} else {
    $comment_id = $_POST['id'];
}
$query = "UPDATE comments SET body=? WHERE id=? AND user_id=?";
$stmt  = $mysqli->prepare($query);
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('sss', $body, $comment_id, $user_id);
$stmt->execute();
$stmt->close();
header("Location: view_story.php?id=$story_id");
?>
