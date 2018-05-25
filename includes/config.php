<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "utenti2fa";

    // Create connection
    $conn = new mysqli($servername, $username, $password);
    // Check connection
    if ($conn->connect_error) {
        die("Connessione non avvenuta: " . $conn->connect_error);
	}
	if (!($conn->select_db($dbname))) {
    $conn->query('CREATE DATABASE '.$conn->real_escape_string($dbname).'');
    $conn->select_db($dbname);
	$initialize = "CREATE TABLE utenti (userid BIGINT NOT NULL AUTO_INCREMENT, username VARCHAR(5000), email VARCHAR(2000), passwd VARCHAR(255), string2fa VARCHAR(1024), PRIMARY KEY (userid))";
	$conn->query($initialize);
	}
?>