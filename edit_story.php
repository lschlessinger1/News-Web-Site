<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
}

$story_id = $_GET['id'];
$token    = $_SESSION['token'];
?>
<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
  <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
    <meta charset="utf-8" />
    <title>News Web Site: Edit Story</title>
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
    <link rel="stylesheet" type="text/css" href="nav_style.css">
</head>

<body>
    <div id="main">
        <header>
            <?php include( 'includes/nav.php'); ?>
            <h1>Edit Story</h1>
        </header>
          <?php
          require 'database.php';
          $query = "SELECT title, link, commentary FROM stories WHERE id=? AND user_id=?";
          $stmt  = $mysqli->prepare($query);
          if (!$stmt) {
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
          }

          $stmt->bind_param('ss', $story_id, $_SESSION['user_id']);

          $stmt->execute();

          $stmt->bind_result($title, $link, $commentary);

          $stmt->fetch();

          $stmt->close();

          $error_messages = array(
              1 => "You must be logged in to update this story",
              2 => "Title cannot be empty",
              3 => "Invalid link URL",
              4 => "Invalid story ID"
          );

          if (isset($_GET['error_code'])) {
              $error_code = (int) $_GET['error_code'];
          } else {
              $error_code = 0;
          }
          // https://www.w3schools.com/php/func_array_key_exists.asp
          if (array_key_exists($error_code, $error_messages)) {
              // END CITATION
              echo "<p><strong>Error: $error_messages[$error_code]</strong><p>";
          }

          $safe_title      = htmlspecialchars($title);
          $safe_link       = htmlspecialchars($link);
          $safe_commentary = htmlspecialchars($commentary);
          $safe_story_id   = htmlspecialchars($story_id);
          ?>
 <form action="update_story.php" method="POST">
    <p>
        <label for="title"><strong>Title:</strong>
        </label>
        <br>
        <input type="text" name="title" id="title" value="<?php echo $safe_title; ?>" required />
    </p>
    <p>
        <label for="link"><strong>Link:</strong>
        </label>
        <br>
        <input type="url" name="link" id="link" value="<?php echo $safe_link; ?>" />
    </p>
    <p>
        <label for="commentary"><strong>Commentary:</strong>
        </label>
        <br>
        <textarea id="commentary" rows="5" cols="40" name="commentary" placeholder="Enter story commentary"><?php echo $safe_commentary; ?></textarea>
    </p>
    <input type="hidden" name="id" value="<?php echo $safe_story_id; ?>" />
    <input type="hidden" name="token" value="<?php echo $token; ?>" />
    <p>
        <input type="submit" value="Update Story" />
    </p>
</form>
<p>
    <?php echo "<a href='view_story.php?id=$safe_story_id'>Back to story</a>"; ?>
</p>
</div>
</body>

</html>
