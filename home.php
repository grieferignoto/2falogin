<?php
$inactive = 3600; 
ini_set('session.gc_maxlifetime', $inactive); // durata massima sessione: 1hr

session_start();

if (isset($_SESSION['durata']) && (time() - $_SESSION['durata'] > $inactive)) {
    session_unset();
    session_destroy();
}
$_SESSION['durata'] = time(); //refresh della sessione

if(isset($_SESSION['loginstatus'])){
	if($_SESSION['loginstatus'] === 'OK'){
	echo '<html>
			<head>
				<title>PHP Test</title>
			</head>
			<body>
				<div style="position: absolute; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%);">
				<center>
				<h2>TEST WELCOME PAGE</h2>
				<a href="logout.php">LOGOUT</a>
				</center>
				</div>
			</body>
		  </html>';
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	header("Location: index.html");
	die();
}
?>