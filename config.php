<?php
$hostname = "localhost";
$username = "xrevaj";
$password = "VS0pgxPsMepHPBh";
$dbname = "final";

$db = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
