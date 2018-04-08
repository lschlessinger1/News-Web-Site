<nav>
    <ul>
      <li><a href="home.php">Home</a></li>
      <li><a href="new_story.php">Create Story</a></li>
      <?php

          if (isset($_SESSION['user_id'])) {
            $username = $_SESSION['username'];
            echo "<li><a href='view_profile.php'>$username</a></li>";
            echo "<li><a href='logout.php'>Logout</a></li>";
          } else {
            echo "<li><a href='login.php'>Log In</a></li>";
            echo "<li><a href='register.php'>Register</a></li>";
          }
      ?>
    </ul>
</nav>
