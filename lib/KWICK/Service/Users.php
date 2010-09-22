<?php

/**
 * This class implements the methods for access the users service.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Service_Users extends KWICK_Service {
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
	 * Resolves the id of a user specified by his/her login.
	 *
	 * @param string $username Login name.
	 * @return int user id.
	 */
	public function getId($username) {
		if (empty($username)) {
			throw new InvalidArgumentException('Invalid username specified');
		}
		
		$parameters = array(
			'username' => $username,
		);
		
		return $this->client->callMethod('users.getID', $parameters);
	}
	
	/**
	 * Gets the user id who owns the current session.
	 *
	 * @return int ID des aktuellen Benutzers.
	 */
	public function getLoggedInUser() {
		return (int)$this->client->callMethod('users.getLoggedInUser', array(), $this->session);
	}
	
	/**
	 * Checks if the specified user has authorized the application.
	 *
	 * @param int $uid User id.
	 * @return bool True, if the application is authorized by this user.
	 */
	public function isAppUser($uid) {
		if (!is_numeric($uid) || intval($uid) < 1) {
			throw new InvalidArgumentException('Invalid user id specified');
		}
		
		$parameters = array(
			'uid' => intval($uid),
		);
		
		return (bool)$this->client->callMethod('users.isAppUser', $parameters, $this->session);
	}
	
	/**
	 * Gets the info cards for one or more user. If only one card is requested
	 * the parameter can be an integer. Otherwise it has to be an array.
	 *
	 * @param mixed $uids One or more user ids.
	 * @return array Info card of this user.
	 */
	public function getVCard($uids) {
		if (!is_numeric($uids) && !is_array($uids)) {
			throw new InvalidArgumentException('Invalid user id specified');
		}
		elseif (is_numeric($uids) && intval($uids) < 1) {
			throw new InvalidArgumentException('Invalid user id specified');
		}
		
		$parameters = array();

		if (is_array($uids)) {
			$parameters['uids'] = implode(',', $uids);
		}
		else {
			$parameters['uids'] = intval($uids);
		}

		$vCards = $this->client->callMethod('users.getVCard', $parameters);
		return $vCards;
	}
	
	/**
	 * Gets the infobox for the current user. The infobox contains the
	 * counters for
	 *
	 *  + unseen e-mails
	 *  + spam e-mails
	 *  + received messages
	 *  + spam messages
	 *  + unseen messages
	 *  + new blog comments
	 *  + new guestbook comments
	 *  + new media comments
	 *  + new buddy requests
	 *
	 * It also contains an overview about all available notifications.
	 *
	 * @return array Infobox content
	 */
	public function getInfoBox() {
		return $this->client->callMethod('users.getInfoBox', array(), $this->session);
	}

	/**
	 * Gets the online state for the specified users.
	 *
	 * @param array $uids Array with user ids.
	 * @return Array with users who are online (state 1, 2, 3, or 4).
	 */	
	public function getOnlineState(array $uids) {
		if (count($uids) < 1) {
			throw new InvalidArgumentException('You have to specify at least one user id');
		}
		
		$parameters = array();
		$parameters['uids'] = implode(',', $uids);
		
		return $this->client->callMethod('users.getOnlineState', $parameters, $this->session);
	}
}

?>
