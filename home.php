<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>

<head>
    <!--[if lt IE 9]>
<script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
    <meta charset="utf-8" />
    <title>News Web Site</title>
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
            <h1>Simple News Web Site</h1>
        </header>
        <h2>Stories</h2>
        <table>
          <?php
          require 'database.php';

          if ($logged_in) {
              $query = "SELECT stories.id, stories.user_id, stories.title, stories.link, story_saves.user_id AS saved_by FROM stories LEFT JOIN story_saves ON stories.id=story_saves.story_id";
          } else {
              $query = "SELECT * FROM stories";
          }
          $stmt = $mysqli->prepare($query);

          if (!$stmt) {
              printf("Query Prep Failed: %s\n", $mysqli->error);
              exit;
          }

          $stmt->execute();

          $result = $stmt->get_result();

          $stories = array();

          while ($row = $result->fetch_assoc()) {
              if (!array_key_exists($row['id'], $stories)) {
                  $stories[$row['id']] = array();
              }
              array_push($stories[$row['id']], $row);
          }

          foreach ($stories as $key => $story) {
              if ($logged_in) {
                  $saved_by_list = array();
                  foreach ($story as $index => $story_save) {
                      array_push($saved_by_list, $story_save['saved_by']);
                  }

                  if (empty($saved_by_list)) {
                      // no one saved this story, so show it
                      $show_unsave = false;
                  } else if (in_array($_SESSION['user_id'], $saved_by_list)) {
                      // user saved this story, show unsave button and story
                      $show_unsave = true;
                  } else {
                      //  user did not save the story, show save button and story
                      $show_unsave = false;
                  }
              }
              // show 1 story
              $safe_story_id   = htmlspecialchars($story[0]['id']);
              $safe_user_id    = htmlspecialchars($story[0]['user_id']);
              $safe_title      = htmlspecialchars($story[0]['title']);
              $safe_link       = htmlspecialchars($story[0]['link']);
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
                  if ($show_unsave == $_SESSION['user_id']) {
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

              if ($logged_in && ($safe_user_id == $_SESSION['user_id'])) {
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
</div>
</body>

</html>
