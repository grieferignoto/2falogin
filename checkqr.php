<?php
	require_once 'includes/config.php';
	require_once 'includes/GoogleAuthenticator.php';
	
if($_SERVER["REQUEST_METHOD"] === "POST"){
    session_start();
	if(isset($_SESSION['userid'])){
		$ga = new PHPGangsta_GoogleAuthenticator();
		$checkcodice = $ga->verifyCode($_SESSION['qrsecret'], $_POST['2facode'], 2);
		if($checkcodice){
			if($_SESSION['has2fa'] === 'no'){
			$successqry = "UPDATE utenti SET string2fa = '".$_SESSION['qrsecret']."' WHERE userid = '".$_SESSION['userid']."'";
			$conn->query($successqry)
			or die(mysqli_error($conn));
			}
			$_SESSION['loginstatus'] = "OK";
			$response_array['successmsg']="QROK";	
		} else {
			$response_array['successmsg']="QRNO";
		}
		header('HTTP/1.1 200 OK');
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($response_array);
	}
} else {
	header('HTTP/1.1 403 Forbidden');
	die();
}
?>