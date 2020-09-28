<?php
  try 
  {
    $db_main = new PDO($DATABASE['dsn'], $DATABASE['user'], $DATABASE['passwd']);
  } 
  catch (PDOException $e) 
  {
    echo 'Connection failed: ' . $e->getMessage();
  }
  $db_main->exec("SET NAMES 'utf8'");
  $db_main->exec("SET CHARACTER SET utf8");
  $db_main->exec("SET CHARACTER_SET_CONNECTION=utf8");
  $db_main->exec("SET SQL_MODE = ''");
  global $db_main;
?>