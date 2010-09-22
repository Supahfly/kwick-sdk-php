<?php

/**
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */

// Include base functions
require_once('base.php');

// Create a client instance
$kwickClient = new KWICK_Client($apiKey, $secret, $baseUrl);

// Create an auth service instance
$authService = new KWICK_Service_Auth($kwickClient);

// Try to load session
$kwickSession = $authService->loadSession($storedSessionKey);
if (is_null($kwickSession)) {
  die("Die Session ist ungültig!");
}

// Create users service instance
$usersService = new KWICK_Service_Users($kwickClient, $kwickSession);

// Get and check current user
$currentUser = $usersService->getLoggedInUser();
if ($currentUser !== $kwickSession->userId) {
  die("Mismatch between current user and logged in user");
}

// Get the user id by specifying the username
$uid = $usersService->getId('Jens');
echo "Jens has the user id " . $uid . "\n\n";

// Get the vCard for one user.
$vCard = $usersService->getVCard($currentUser);
echo "* Your user id is     : " . $vCard[$currentUser]['userid'] . "\n";
echo "* Your username is    : " . $vCard[$currentUser]['username'] . "\n";
echo "* Your birthday is    : " . $vCard[$currentUser]['birthday'] . "\n";
echo "* You are coming from : " . $vCard[$currentUser]['city'] . "\n\n";

// Get vCards for more than one user.
$users = array(1, 2, 3302941, 4478134);
$vCards = $usersService->getVCard($users);
foreach ($vCards as $vCard) {
	echo "User " . $vCard['userid'] . ":\n";
	echo "* Username......: " . $vCard['username'] . "\n";
	echo "* Realname......: " . $vCard['firstname'] . " " . $vCard['lastname'] . "\n";
	echo "* Birthday......: " . $vCard['birthday'] . "\n";
	echo "* City..........: " . $vCard['city'] . "\n";
	
	$isAppUser = $usersService->isAppUser($uid);
	if ($isAppUser) {
		echo "* This user has authorized this application\n";
	}
	
	echo "\n";
}


?>
