<?php
// This is a *good* example of how you can implement password-based user authentication in your web application.
require 'database.php';
session_start();

// Use a prepared statement
$query = "SELECT COUNT(*), id, crypted_password FROM users WHERE username=?";
$stmt  = $mysqli->prepare($query);

// Bind the parameter
$stmt->bind_param('s', $user);
$user = $_POST['username'];
$stmt->execute();

// Bind the results
$stmt->bind_result($cnt, $user_id, $pwd_hash);
$stmt->fetch();

$pwd_guess = $_POST['password'];
// Compare the submitted password to the actual password hash
if ($cnt == 1 && password_verify($pwd_guess, $pwd_hash)) {
    // Login succeeded!
    $_SESSION['user_id']  = $user_id;
    $_SESSION['username'] = $user;
    $_SESSION['token']    = bin2hex(openssl_random_pseudo_bytes(32)); // generate a 32-byte random string

    // Redirect to your target page
    header("Location: home.php");
    exit;
} else {
    // Login failed; redirect back to the login screen
    $error_code = 1;
    header("Location: login.php?error_code=$error_code");
    exit;
}
?>
