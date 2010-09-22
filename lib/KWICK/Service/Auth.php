<?php

/**
 * This class implements the methods for access the auth service.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Service_Auth extends KWICK_Service {
	/**
	 * Creates and initializes a new instance.
	 *
	 * @param KWICK_Client   $client   Client instance.
	 */
	public function __construct(KWICK_Client $client) {
		parent::__construct($client);
	}
	
	/**
	 * Checks if the session specified by its id is still valid.
	 *
	 * @param string $sessionKey The session key.
	 * @return bool True, if the session is valid.
	 */
	public function checkSession($sessionKey) {
		if (empty($sessionKey)) {
			throw new InvalidArgumentException('Invalid session key specified');
		}
		
		$parameters = array(
			'skey' => $sessionKey,
		);
		
		return (bool)$this->client->callMethod('auth.checkSession', $parameters);
	}
	
	/**
	 * Creates the token which is required for the user login and
	 * application authorization at KWICK!.
	 *
	 * @return string Auth token
	 */
	public function createToken() {
		return $this->client->callMethod('auth.createToken');
	}
	
	/**
	 * Loads a user session specified by its key.
	 *
	 * @param string $sessionKey The session key.
	 * @return KWICK_Session The session or null, if not found.
	 */
	public function loadSession($sessionKey) {
		if (empty($sessionKey)) {
			throw new InvalidArgumentException('Invalid session key specified');
		}
		
		$session = null;
		
		$parameters = array(
			'skey' => $sessionKey,
		);
		
		$response = $this->client->callMethod('auth.loadSession', $parameters);
		if (is_array($response)) {
			$session = new KWICK_Session();
			$session->sessionKey = $response['session_key'];
			$session->sessionSecret = $response['secret'];
			$session->userId = (int)$response['uid'];
			$session->expires = (int)$response['expires'];
		}
		
		return $session;
	}
	
	/**
	 * Loads a user session specified by the authorization token.
	 *
	 * @param string $authToken The authorization token.
	 * @return KWICK_Session The session or null, if not found.
	 */
	public function getSession($authToken) {
		if (empty($authToken)) {
			throw new InvalidArgumentException('Invalid token specified');
		}
		
		$parameters = array(
			'auth_token' => $authToken
		);
		
		$session = null;
		
		$response = $this->client->callMethod('auth.getSession', $parameters);
		if (is_array($response)) {
			$session = new KWICK_Session();
			$session->sessionKey = $response['session_key'];
			$session->sessionSecret = $response['secret'];
			$session->userId = (int)$response['uid'];
			$session->expires = (int)$response['expires'];
		}
		
		return $session;
	}
	
	/**
	 * Expires the specified user session.
	 *
	 * @param KWICK_Session $session The user session.
	 * @return bool True, if the session was expired, otherwise false.
	 */
	public function expireSession(KWICK_Session $session) {
		if (!$session->isValid()) {
			throw new InvalidArgumentException('Invalid session specified');
		}
		
		if ($session->isExpired()) {
			throw new InvalidArgumentException('The session is already expired');
		}
		
		return $this->client->callMethod('auth.expireSession', array(), $session);
	}
	
	/**
	 * Revokes the authorization for this application from the user.
	 *
	 * @param KWICK_Session $session The user session.
	 * @return bool True, if authorization was revoked, otherwise false.
	 */
	public function revokeAuthorization(KWICK_Session $session) {
		if (!$session->isValid()) {
			throw new InvalidArgumentException('Invalid session specified');
		}
		
		return $this->client->callMethod('auth.revokeAuthorization', array(), $session);
	}
}

?>
