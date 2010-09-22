<?php

/**
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */

// Include base functions
require_once('base.php');

// Create client instance
$kwickClient = new KWICK_Client($apiKey, $secret, $baseUrl);

// Create auth service instance
$authService = new KWICK_Service_Auth($kwickClient);

// Create a new auth token
$token = $authService->createToken();
if (is_null($token)) {
	die("Could not create a token for login/authorization");
}

// Print the login url
printf("Go to: %s?api_key=%s&auth_token=%s\n", $loginUrl, $apiKey, $token);

// Try to get the session (works only if you have visited the login page from above
$session = null;
for ($cnt = 0; $cnt < 10; $cnt++) {
	try {
		$session = $authService->getSession($token);
		if ($session instanceof KWICK_Session) {
			break;
		}
		
		$session = null;
	}
	catch(KWICK_Remote_Exception $ex) {
		//
	}
	
	printf("\n[%s] Wait...", date('Y-m-d H:i:s'));
	sleep(10);
}

// Check received session
if ($session instanceof KWICK_Session) {
	printf("Session created with key: %s\n", $session->sessionKey);
}
else {
	printf("No session was created!\n");
}

?>
