<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  $error_code = 2;
  header ("Location: login.php?error_code=$error_code");
}

$token = $_SESSION['token'];
?>
<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    <meta charset="utf-8" />
    <title>News Web Site: Create Story</title>
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
            <h1>Create Story</h1>
        </header>
  <?php
  $error_messages = array (
      1 => "You must be logged in to create a story",
      2 => "Title cannot be empty",
      3 => "Invalid link URL"
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

 <form action="create_story.php" method="POST">
    <p>
        <label for="title"><strong>Title:</strong>
        </label>
        <br>
        <input type="text" name="title" id="title" required/>
    </p>
    <p>
        <label for="link"><strong>Link:</strong>
        </label>
        <br>
        <input type="url" name="link" id="link" />
    </p>
    <p>
        <label for="commentary"><strong>Commentary:</strong>
        </label>
        <br>
        <textarea id="commentary" rows="5" cols="40" name="commentary" placeholder="Story commentary"></textarea>
    </p>
    <input type="hidden" name="token" value="<?php echo $token;?>" />
    <p>
        <input type="submit" value="Create Story" />
    </p>
</form>

</div>
</body>

</html>
