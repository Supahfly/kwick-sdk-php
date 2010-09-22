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
if (is_null($kwickSession) || $kwickSession->isExpired()) {
  die("No session available or session expired");
}


// Create a user service instance.
$usersService = new KWICK_Service_Users($kwickClient, $kwickSession);

$currentUser = $usersService->getLoggedInUser();

// Create a friends service instance.
$friendsService = new KWICK_Service_Friends($kwickClient, $kwickSession);

// Get the available buddy lists
$lists = $friendsService->getLists();
if (count($lists) > 0) {
	foreach ($lists as $list) {
		echo "* " . $list['groupname'] . " - ID: " . $list['id'] . "\n";
	}
}
else {
	echo "You have no buddy lists\n\n";
}

// Get the buddies
$friends = $friendsService->get();
if (count($friends) > 0) {
	echo "Your buddies:\n";
	foreach ($friends as $friend) {
		echo "* " . $friend['username'] . " (" . $friend['status_info']['text'] . ")\n";
	}
}
else {
	echo "You have no buddies :(\n";
}

?>
