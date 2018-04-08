<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    <meta charset="utf-8" />
    <title>News Web Site: Login</title>
    <style type="text/css">
        body {
            width: 760px;
            /* how wide to make your web page */

            background-color: teal;
            /* what color to make the background */

            margin: 0 auto;
            padding: 0;
            font: 12px/16px Verdana, sans-serif;
            /* default font */
        }
        div#main {
            background-color: #FFF;
            margin: 0;
            padding: 10px;
        }
    </style>
    <link rel="stylesheet" href="nav_style.css">
</head>

<body>
    <div id="main">
        <header>
            <?php include( 'includes/nav.php'); ?>
            <h1>Log In</h1>
        </header>
<?php
$error_messages = array (
    1 => "Invalid username/password combination",
    2 => "You must be logged in to create a story"
);

if (isset($_GET['error_code'])) {
  $error_code = (int)$_GET['error_code'];
} else {
  $error_code = 0;
}
// https://www.w3schools.com/php/func_array_key_exists.asp
if (array_key_exists($error_code, $error_messages)) {
// END CITATION
    echo "<p><strong>Error: $error_messages[$error_code]</strong><p>";
}

?>

 <form action="verify_login.php" method="POST">
    <p>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required/>
    </p>
    <p>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required />
    </p>
    <p>
        <input type="submit" value="Log in" />
    </p>
</form>

<p>Don't have an account? <a href="register.php">Register here</a>
</p>

</div>
</body>

</html>
