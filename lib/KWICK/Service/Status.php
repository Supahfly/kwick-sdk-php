<?php
/**
 * @see KWICK_Service
 */
require_once 'KWICK/Service.php';

/**
 * This class implements the methods for access the status service.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Service_Status extends KWICK_Service {
	/**
	 * Creates and initializes a new instance.
	 *
	 * @param KWICK_Client   $client   Client instance.
	 * @param KWICK_Session  $session  Session instance.
	 */
	public function __construct(KWICK_Client $client, KWICK_Session $session) {
		parent::__construct($client, $session);
	}

	/**
	 * Gets the last status messages for the specified user. The number of
	 * returned messages is defined by the optional parameter limit. If no
	 * user is specified the current session user is used.
	 *
	 * @param int $uid   User id (optional).
	 * @param int $limit Number of status messages (optional).
	 * @return array Status messages.
	 */
	public function get($uid = null, $limit = 1) {
		if (is_null($uid)) {
			$uid = $this->session->userId;
		}
		elseif (!is_numeric($uid) || intval($uid) < 1) {
			throw new InvalidArgumentException('Invalid user id specified');
		}
		
		if (!is_numeric($limit) || intval($limit) < 1) {
			throw new InvalidArgumentException('Invalid limit specified');
		}
		
		$parameters = array(
			'uid' => intval($uid),
			'limit' => intval($limit),
		);
		
		return $this->client->callMethod('status.get', $parameters, $this->session);
	}
	
	/**
	 * Sets the status message for the current user.
	 *
	 * @param string $message New status message.
	 * @result bool
	 */
	public function set($message, $publishExternal = false) {
		if (empty($message)) {
			throw new InvalidArgumentException('Invalid message specified');
		}
		
		if (!is_bool($publishExternal)) {
			throw new InvalidArgumentException('Invalid value for publishExternal specified');
		}
		
		$parameters = array(
			'status' => $message,
			'publish_external' => $publishExternal,
		);
		
		return (bool)$this->client->callMethod('status.set', $parameters, $this->session);
	}
}

?>
