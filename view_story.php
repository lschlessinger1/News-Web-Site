<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: home.php");
}

$story_id  = $_GET['id'];
$logged_in = isset($_SESSION['user_id']);

require 'database.php';
$query             = "SELECT id, user_id, title, link, commentary FROM stories WHERE id='" . $story_id . "'";
$select_story_stmt = $mysqli->prepare($query);
if (!$select_story_stmt) {
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
}

$select_story_stmt->execute();
$select_story_stmt->bind_result($story_id, $user_id, $title, $link, $commentary);
$select_story_stmt->fetch();

$safe_story_id   = htmlspecialchars($story_id);
$safe_title      = htmlspecialchars($title);
$safe_link       = htmlspecialchars($link);
$safe_commentary = htmlspecialchars($commentary);

$select_story_stmt->close();

$curr_user_owns_story = $logged_in && $user_id == $_SESSION['user_id'];

?>
<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    <meta charset="utf-8" />
    <title>
        <?php echo $safe_title;?>
    </title>
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
        table td {
            padding-left: 10px;
            padding-right: 10px;
        }
        .comment {
            padding-left: 10px;
        }
    </style>
    <link rel="stylesheet" href="nav_style.css">
</head>

<body>
    <div id="main">
        <header>
            <?php include( 'includes/nav.php'); ?>
            <h1>View Story</h1>
        </header>
        <p>
            <label><strong>Title:</strong>
            </label>
            <br>
            <span><?php echo $safe_title; ?></span>
        </p>
        <p>
            <label><strong>Link:</strong>
            </label>
            <br>
    <?php
    if (empty($safe_link)) {
        echo "No link";
    } else {
        echo "<a href='$safe_link'>$safe_link</a>";
    }
    ?>
  </p>
  <p>
      <label for="commentary"><strong>Commentary:</strong>
      </label>
      <br>
      <textarea id='commentary' rows="5" cols="40" name="commentary" disabled><?php echo $safe_commentary; ?></textarea>
  </p>
    <?php
    if ($curr_user_owns_story) {
        $token = $_SESSION['token'];
        echo "<p><a href='edit_story.php?id=$safe_story_id'>Edit Story</a></p>";
        echo "<p><form action='delete_story.php' method='POST'>";
        echo "<input type='hidden' name='id' value='$safe_story_id'/>";
        echo "<input type='hidden' name='token' value='$token'/>";
        echo "<input type='submit' value='Delete Story' />";
        echo "</form></p>";
    }
    ?>
  <h2>Comments</h2>
  <?php
  $error_messages = array(
      1 => "You must be logged in to update this comment",
      2 => "Comment body cannot be empty",
      3 => "Story ID cannot be empty"
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
  if ($logged_in) {
      $token = $_SESSION['token'];
      echo "<form action='create_comment.php' method='POST'><p>";
      echo "<label for='comment'><strong>Comment:</strong></label>";
      echo "<br>";
      echo "<textarea rows='5' cols='40' name='body' required></textarea>";
      echo "<p><input type='submit' value='Create comment' /></p>";
      echo "<input type='hidden' name='story_id' value='$safe_story_id'/>";
      echo "<input type='hidden' name='token' value='$token'/>";
      echo "</p></form>";
      echo "<br>";
  }
  ?>
    <table>
    <?php
    $query                = "SELECT comments.id, comments.body, comments.user_id, comments.story_id, users.username FROM comments JOIN users ON comments.user_id=users.id WHERE comments.story_id=?";
    $select_comments_stmt = $mysqli->prepare($query);
    if (!$select_comments_stmt) {
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $select_comments_stmt->bind_param('s', $story_id);

    $select_comments_stmt->execute();

    $select_comments_stmt->bind_result($comment_id, $comment_body, $comment_user_id, $comment_story_id, $comment_username);

    while ($select_comments_stmt->fetch()) {
        $safe_comment_id       = htmlspecialchars($comment_id);
        $safe_comment_body     = htmlspecialchars($comment_body);
        $safe_comment_user_id  = htmlspecialchars($comment_user_id);
        $safe_comment_story_id = htmlspecialchars($comment_story_id);
        $safe_comment_username = htmlspecialchars($comment_username);

        echo "<tr>";
        echo "<td>";
        echo "<p>$safe_comment_username</p>";
        echo "<p class='comment'>$safe_comment_body</p>";
        echo "</td>";
        if ($logged_in && ($comment_user_id == $_SESSION['user_id'])) {
            // current user owns this comment
            $token = $_SESSION['token'];
            echo "<td>";
            echo "<p><a href='edit_comment.php?id=$safe_comment_id'>Edit comment</a></p>";
            echo "</td>";
            echo "<td>";
            echo "<p><form action='delete_comment.php' method='POST'>";
            echo "<input type='hidden' name='id' value='$safe_comment_id'/>";
            echo "<input type='hidden' name='story_id' value='$safe_comment_story_id'/>";
            echo "<input type='hidden' name='token' value='$token'/>";
            echo "<input type='submit' value='Delete comment' />";
            echo "</form></p>";
            echo "</td>";
        }
        echo "</tr>";
    }

    $select_comments_stmt->close();
    ?>
</table>

</div>
</body>

</html>
