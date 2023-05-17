<?php
$hostname = "localhost";
$username = "xkissj";
$password = "";
$dbname = "final";

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
