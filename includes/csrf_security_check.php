<?php
if(!hash_equals($_SESSION['token'], $_POST['token'])){
  die("Request forgery detected");
}
?>
