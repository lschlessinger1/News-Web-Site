<?php

require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

$story_id = $_POST['story_id'];

$error_code = 0;
if (!isset($_SESSION['user_id'])) {
    $error_code = 1;
    header("Location: view_story.php?id=$story_id&error_code=$error_code");
    exit;
} else {
    $user_id  = $_SESSION['user_id'];
}

if (empty($_POST['body'])) {
    $error_code = 2;
    header("Location: view_story.php?id=$story_id&error_code=$error_code");
    exit;
} else {
    $body     = trim($_POST['body']);
}

if (empty($story_id)) {
    $error_code = 3;
    header("Location: view_story.php?id=$story_id&error_code=$error_code");
    exit;
}

$query = "INSERT INTO comments (body, user_id, story_id) VALUES (?, ?, ?)";
$stmt  = $mysqli->prepare($query);
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$stmt->bind_param('sss', $body, $user_id, $story_id);
$stmt->execute();
$stmt->close();
header("Location: view_story.php?id=$story_id");
?>
