<?php
// Include config file
require_once 'includes/config.php';
require_once 'includes/GoogleAuthenticator.php';

$ga = new PHPGangsta_GoogleAuthenticator();
$secret = $ga->createSecret();
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] === "POST"){
 
    $username = $conn->real_escape_string(trim($_POST["username"]));
	$password = $conn->real_escape_string(trim($_POST['password']));
	$ga = new PHPGangsta_GoogleAuthenticator();
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT userid, username, passwd, string2fa FROM utenti WHERE username = ?";
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($userid, $username, $hashed_password, $secret2fa);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
                            session_start();
                            $_SESSION['userid'] = $userid;
                            header('HTTP/1.1 200 OK');
							header('Content-Type: application/json; charset=UTF-8');
							$response_array['successmsg']="Login effettuato con successo";
							if($secret2fa === null){
								$response_array['has2fa']='no';
								$_SESSION['has2fa'] = 'no';
								$secret2fa = $ga->createSecret();
								$qrCodeUrl = $ga->getQRCodeGoogleUrl('Cucinotta2FA ('.$username.')', $secret2fa);								
								$response_array['qrUrl']=$qrCodeUrl;
							} else {
								$response_array['has2fa']='yes';
								$_SESSION['has2fa'] = 'yes';
							}
							$_SESSION['qrsecret'] = $secret2fa;
							echo json_encode($response_array);
                        } 
						else{
                            $stmt->close();
							header('HTTP/1.1 500 Internal Server Error');
							header('Content-Type: application/json; charset=UTF-8');
							$response_array['errormsg']="Username o password non validi";
							die(json_encode($response_array));                            
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
					$stmt->close();
					header('HTTP/1.1 500 Internal Server Error');
					header('Content-Type: application/json; charset=UTF-8');
					$response_array['errormsg']="Username o password non validi";
					die(json_encode($response_array));
                }
            } else{
				$stmt->close();
                header('HTTP/1.1 500 Internal Server Error');
				header('Content-Type: application/json; charset=UTF-8');
				$response_array['errormsg']="Errore nel server, riprova!";
				die(json_encode($response_array));
            }
        $stmt->close();
        }
        

    }
}
 else {
	header('HTTP/1.1 403 Forbidden');
	die();
}
?>
	