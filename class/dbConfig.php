<?php
//DB details
$dbHost = '3.211.203.97';
$dbUsername = 'legaltec';
$dbPassword = 'wSHjV&nqD8';
$dbName = 'MondayGestionDiaria';

//Create connection and select DB
$db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($db->connect_error) {
    die("Unable to connect database: " . $db->connect_error);
}