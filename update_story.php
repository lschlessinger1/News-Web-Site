<?php
require 'database.php';
session_start();
require 'includes/csrf_security_check.php';

$story_id   = $_POST['id'];
$error_code = 0;
if (!isset($_SESSION['user_id'])) {
    $error_code = 1;
    header("Location: edit_story.php?id=$story_id&error_code=$error_code");
    exit;
} else {
    $user_id = $_SESSION['user_id'];
}

// check that title, link and commentary are valid
if (empty($_POST['title'])) {
    // invalid title
    $error_code = 2;
    header("Location: edit_story.php?id=$story_id&error_code=$error_code");
    exit;
} else {
    $title = $_POST['title'];
}

// https://www.w3schools.com/php/filter_validate_url.asp
if (!empty($_POST['link']) && !(filter_var($_POST['link'], FILTER_VALIDATE_URL))) {
    //  END CITATION
    // invalid link
    $error_code = 3;
    header("Location: edit_story.php?id=$story_id&error_code=$error_code");
    exit;
} else {
    $link = $_POST['link'];
}

if (empty($story_id)) {
    // invalid story_id
    $error_code = 4;
    header("Location: edit_story.php?id=$story_id&error_code=$error_code");
    exit;
}

$query = "UPDATE stories SET title=?, link=?, commentary=? WHERE id=? AND user_id=?";
$stmt  = $mysqli->prepare($query);
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}
$commentary = trim($_POST['commentary']);
$stmt->bind_param('sssss', $title, $link, $commentary, $story_id, $user_id);
$stmt->execute();
$stmt->close();
header("Location: view_story.php");
?>
