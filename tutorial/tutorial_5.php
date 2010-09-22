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

// Create a status service instance
$statusService = new KWICK_Service_Status($kwickClient, $kwickSession);

// Set the state message
$msg = "is testing the KWICK! SDK";
if ($statusService->set($msg)) {
	printf("Your state message was set!\n\n");
}
else {
	printf("Your state message was not set because an error occurred!\n\n");
}

// Create a friends service instance
$friendsService = new KWICK_Service_Friends($kwickClient, $kwickSession);

// Get buddies
$friends = $friendsService->get();

// Get last five state messages for each buddy
foreach ($friends as $friend) {
	printf("%s:\n", $friend['username']);
	
	$uid = $friend['userid'];
	$msgs = $statusService->get($uid, 5);
	if (count($msgs) > 0) {
		foreach ($msgs as $msg) {
			printf("* [%s] %s\n", date('d.m.Y H:i', $msg['timestamp']), $msg['body']);
			
			// Show comments if available
			$comments = $msg['comments'];
			if (is_array($comments) && count($comments) > 0) {
				printf("  Comments:\n");
				foreach ($comments as $comment) {
					printf("    [%s] %s: %s\n", date('d.m.Y H:i', $comment['timestamp']), $comment['username'], $comment['text']);
				}
			}
		}
	}
	else {
		printf("* has no status messages\n");
	}
	
	printf("\n");
}

?>
