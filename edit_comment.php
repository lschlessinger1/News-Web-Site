<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
}

$comment_id = $_GET['id'];
$token      = $_SESSION['token'];
?>
<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    <meta charset="utf-8" />
    <title>News Web Site: Edit Comment</title>
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
  <?php include('includes/nav.php'); ?>
  <h1>Edit Comment</h1>
</header>
         <?php
          require 'database.php';
          $query = "SELECT body, story_id FROM comments WHERE id=? AND user_id=?";
          $stmt  = $mysqli->prepare($query);
          if (!$stmt) {
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
          }

          $stmt->bind_param('ss', $comment_id, $_SESSION['user_id']);

          $stmt->execute();

          $stmt->bind_result($body, $story_id);

          $stmt->fetch();

          $stmt->close();

          $error_messages = array(
              1 => "You must be logged in to update this comment",
              2 => "Comment body cannot be empty",
              3 => "Story ID cannot be empty",
              4 => "Invalid comment ID"
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

          $safe_comment_id = htmlspecialchars($comment_id);
          $safe_body       = htmlspecialchars($body);
          $safe_story_id   = htmlspecialchars($story_id);
          ?>
 <form action="update_comment.php" method="POST">
    <p>
        <label for="body"><strong>Body:</strong>
        </label>
        <br>
        <textarea id="body" rows="5" cols="40" name="body" required><?php echo $safe_body; ?></textarea>
    </p>
    <input type="hidden" name="id" value="<?php echo $safe_comment_id; ?>" />
    <input type="hidden" name="story_id" value="<?php echo $safe_story_id; ?>" />
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
