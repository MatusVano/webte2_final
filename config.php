<?php
$hostname = "localhost";
$username = "xkissj";
$password = "kHOzq4vSNRzkHnY";
$dbname = "final";

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
