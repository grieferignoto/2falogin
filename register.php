<?php
	require('includes/config.php');
 if($_SERVER["REQUEST_METHOD"] == "POST"){
   if(isset($_POST['username'], $_POST['email'], $_POST['passwd'])){
	$checkuser = "SELECT * FROM utenti WHERE username= '".$conn->real_escape_string(trim($_POST['username']))."'";
    $result = $conn->query($checkuser) 
	or die(mysqli_error($conn));
    if(mysqli_num_rows($result) >= 1){
        header('HTTP/1.1 500 Internal Server Error');
		header('Content-Type: application/json; charset=UTF-8');
		$response_array['errormsg']="Username già utilizzato";
		die(json_encode($response_array));
    } 
	else { 
		$checkmail = "SELECT * FROM utenti WHERE email= '".$conn->real_escape_string(trim($_POST['email']))."'";
		$result = $conn->query($checkmail) 
		or die(mysqli_error($conn));
		if(mysqli_num_rows($result) >= 1){
			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: application/json; charset=UTF-8');
			$response_array['errormsg']="Email già utilizzata";
			die(json_encode($response_array));
        }else{
			$passwd = password_hash(trim($_POST['passwd']), PASSWORD_DEFAULT);
			$createuser = "INSERT INTO utenti (`username`, `email`, `passwd`) VALUES ('".$conn->real_escape_string(trim($_POST['username']))."', '".$conn->real_escape_string(trim($_POST['email']))."', '".$passwd."')";
			$result = $conn->query($createuser)
			or die(mysqli_error($conn));
		}
	}
   } else {
	   header('HTTP/1.1 400 Bad Request');
	   die();
	   }
 } else {
	 header('HTTP/1.1 403 Forbidden');
	die();
 }
?>