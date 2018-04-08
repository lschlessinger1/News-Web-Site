<?php
require 'database.php';
session_start();

$error_code = 0;
// check that username and password are valid
if (empty($_POST['username'])) {
    // invalid username
    $error_code = 1;
    header("Location: register.php?error_code=$error_code");
    exit;
} else {
    $username = $_POST['username'];
}

if (empty($_POST['password'])) {
    // invalid password
    $error_code = 2;
    header("Location: register.php?error_code=$error_code");
    exit;
} else {
    $password = $_POST['password'];
}

if (!preg_match('/^[\w_\-]+$/', $username)) {
    // invalid username
    $error_code = 3;
    header("Location: register.php?error_code=$error_code");
    exit;
}

$min_password_length = 4;
if (strlen($password) <= $min_password_length) {
    // invalid password
    $error_code = 4;
    header("Location: register.php?error_code=$error_code");
    exit;
}

// check if another user has already registered this username
$safe_username    = $mysqli->real_escape_string($username);
$check_user_query = "SELECT id FROM users WHERE username='" . $safe_username . "'";
$res              = $mysqli->query($check_user_query);

if ($res->num_rows > 0) {
    // username taken
    $error_code = 5;
    header("Location: register.php?error_code=$error_code");
    exit;
}

$query = "INSERT INTO users (username, crypted_password) VALUES (?, ?)";
$stmt  = $mysqli->prepare($query);
if (!$stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$crypted_password = password_hash($password, PASSWORD_DEFAULT);
$stmt->bind_param('ss', $username, $crypted_password);
$stmt->execute();

$_SESSION['user_id']  = $mysqli->insert_id;
$_SESSION['username'] = $username;
$_SESSION['token']    = bin2hex(openssl_random_pseudo_bytes(32)); // generate a 32-byte random string

$stmt->close();
header("Location: home.php");
?>
