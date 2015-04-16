<?php

try {
  $host = 'localhost';
  $root = 'fishe_sdd';
  $password = 'GvCQ~rlX(N}V';
  $dbconn = new PDO("mysql:host=$host;dbname=fishe_shuttlecatchers;",$root,$password);

} catch (PDOException $e) {
  die("Database Error: ". $e->getMessage());
}
