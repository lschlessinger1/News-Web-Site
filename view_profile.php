<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: home.php");
}
?>
<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    <meta charset="utf-8" />
    <title>News Web Site:
        <?php echo $_SESSION[ 'username']; ?>
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
    </style>
    <link rel="stylesheet" href="nav_style.css">
</head>

<body>
    <div id="main">
        <header>
            <?php include( 'includes/nav.php'); ?>
            <h1><?php echo $_SESSION['username']; ?> Profile Details</h1>
        </header>
        <h2>My Stories</h2>
        <table>
          <?php
          require 'database.php';
          $user_query = "SELECT stories.id, stories.user_id, stories.title, stories.link, story_saves.story_id, story_saves.user_id FROM stories LEFT JOIN story_saves ON stories.id=story_saves.story_id WHERE stories.user_id=?";
          $stmt       = $mysqli->prepare($user_query);

          if (!$stmt) {
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
          }

          $stmt->bind_param('s', $_SESSION['user_id']);

          $stmt->execute();

          $stmt->bind_result($story_id, $user_id, $title, $link, $story_saves_story_id, $story_saves_user_id);

          // http://php.net/manual/en/mysqli-stmt.num-rows.php
          $stmt->store_result();
          $num_user_stories = $stmt->num_rows;
          // END CITATION
          if ($num_user_stories == 0) {
              echo "<p>You have not submitted any stories yet</p>";
          } else {
              $story_text = $num_user_stories == 1 ? 'story' : 'stories';
              echo "<p>You have submitted <i>$num_user_stories</i> $story_text.</p>";
          }

          while ($stmt->fetch()) {
              $safe_story_id = htmlspecialchars($story_id);
              $safe_user_id  = htmlspecialchars($user_id);
              $safe_title    = htmlspecialchars($title);
              $safe_link     = htmlspecialchars($link);

              $view_story_link = $safe_link;
              if (empty($safe_link)) {
                  //No link; go to the view story page instead
                  $view_story_link = "view_story.php?id=$safe_story_id";
              }

              echo "<tr>";
              echo "<td>";
              echo "<p><a href='$view_story_link'>$safe_title</a></p>";
              echo "<p><a href='view_story.php?id=$safe_story_id'>View comments</a></p>";
              echo "</td>";
              if (isset($_SESSION['user_id'])) {
                  if ($story_saves_user_id == $_SESSION['user_id']) {
                      $submit_val = "Unsave Story";
                      $action     = 'unsave_story.php';
                  } else {
                      $action     = 'save_story.php';
                      $submit_val = "Save Story";
                  }
                  $token = $_SESSION['token'];
                  echo "<td>";
                  echo "<p><form action='$action' method='POST'>";
                  echo "<input type='hidden' name='id' value='$safe_story_id'/>";
                  echo "<input type='hidden' name='token' value='$token'/>";
                  echo "<input type='submit' value='$submit_val' />";
                  echo "</form></p>";
                  echo "</td>";
              }
              if (($user_id == $_SESSION['user_id'])) {
                  // current user owns this story
                  $token = $_SESSION['token'];
                  echo "<td>";
                  echo "<p><a href='edit_story.php?id=$safe_story_id'>Edit Story</a></p>";
                  echo "</td>";
                  echo "<td>";
                  echo "<p><form action='delete_story.php' method='POST'>";
                  echo "<input type='hidden' name='id' value='$safe_story_id'/>";
                  echo "<input type='hidden' name='token' value='$token'/>";
                  echo "<input type='submit' value='Delete Story' />";
                  echo "</form></p>";
                  echo "</td>";
              }
              echo "</tr>";
          }

          $stmt->close();
          ?>
  </table>
  <h2>Saved Stories</h2>
    <table>
          <?php
          $query              = "SELECT stories.id, stories.user_id, stories.title, stories.link, story_saves.story_id, story_saves.user_id
                  FROM stories
                  LEFT JOIN story_saves ON stories.id=story_saves.story_id
                  WHERE story_saves.user_id =?";
          $saved_stories_stmt = $mysqli->prepare($query);

          if (!$saved_stories_stmt) {
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
          }

          $saved_stories_stmt->bind_param('s', $_SESSION['user_id']);
          $saved_stories_stmt->execute();
          $saved_stories_stmt->bind_result($story_id, $story_user_id, $story_title, $story_link, $saved_story_id, $saved_story_user_id);

          // http://php.net/manual/en/mysqli-stmt.num-rows.php
          $saved_stories_stmt->store_result();
          $num_saved_user_stories = $saved_stories_stmt->num_rows;
          // END CITATION
          if ($num_saved_user_stories == 0) {
              echo "<p>You have not saved any stories yet</p>";
          } else {
              $saved_story_text = $num_saved_user_stories == 1 ? 'story' : 'stories';
              echo "<p>You have saved <i>$num_saved_user_stories</i> $saved_story_text.</p>";
          }

          while ($saved_stories_stmt->fetch()) {
              $safe_story_id            = htmlspecialchars($saved_story_id);
              $safe_saved_story_user_id = htmlspecialchars($saved_story_user_id);
              $safe_title               = htmlspecialchars($story_title);
              $safe_link                = htmlspecialchars($story_link);

              $view_story_link = $safe_link;
              if (empty($safe_link)) {
                  //No link; go to the view story page instead
                  $view_story_link = "view_story.php?id=$safe_story_id";
              }

              echo "<tr>";
              echo "<td>";
              echo "<p><a href='$view_story_link'>$safe_title</a></p>";
              echo "<p><a href='view_story.php?id=$safe_story_id'>View comments</a></p>";
              echo "</td>";
              if (isset($_SESSION['user_id'])) {
                  if ($safe_saved_story_user_id == $_SESSION['user_id']) {
                      $submit_val = "Unsave Story";
                      $action     = 'unsave_story.php';
                  } else {
                      $action     = 'save_story.php';
                      $submit_val = "Save Story";
                  }
                  $token = $_SESSION['token'];
                  echo "<td>";
                  echo "<p><form action='$action' method='POST'>";
                  echo "<input type='hidden' name='id' value='$safe_story_id'/>";
                  echo "<input type='hidden' name='token' value='$token'/>";
                  echo "<input type='submit' value='$submit_val' />";
                  echo "</form></p>";
                  echo "</td>";
              }
              if (($user_id == $_SESSION['user_id'])) {
                  // current user owns this story
                  $token = $_SESSION['token'];
                  echo "<td>";
                  echo "<p><a href='edit_story.php?id=$safe_story_id'>Edit Story</a></p>";
                  echo "</td>";
                  echo "<td>";
                  echo "<p><form action='delete_story.php' method='POST'>";
                  echo "<input type='hidden' name='id' value='$safe_story_id'/>";
                  echo "<input type='hidden' name='token' value='$token'/>";
                  echo "<input type='submit' value='Delete Story' />";
                  echo "</form></p>";
                  echo "</td>";
              }
              echo "</tr>";
          }

          $saved_stories_stmt->close();
          ?>
    </table>
</div></body>
</html>
