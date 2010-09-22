<?php

/**
 * This class implements the methods for access the friends service.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Service_Friends extends KWICK_Service {
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
	 * Gets the list of friends of a specified user. If no user is specified
	 * the list is generated for the current user.
	 *
	 * With the second parameter a special friend list can be specified. Is no
	 * list specified or 0, than a friends will be returned.
	 *
	 * With the last parameter you can control if the online state of a
	 * friends is included.
	 *
	 * @param int  $uid        User id.
	 * @param int  $flid       List id (optional).
	 * @return array List with friends.
	 */
	public function get($uid = null, $flid = 0) {
		$parameters = array();
		
		if (is_numeric($uid) && intval($uid) > 0) {
			$parameters['uid'] = (int)$uid;
		}
		
		if (is_numeric($flid) && intval($flid) > 0) {
			$parameters['flid'] = (int)$flid;
		}
		
		$friends = array();
		
		try {
			$friends = $this->client->callMethod("friends.get", $parameters, $this->session);
		}
		catch (KWICK_Remote_Exception $ex) {
			// Code 4000 = No profile access
			if ($ex->getCode() != 4000) {
				throw $ex;
			}
		}
		
		return $friends;
	}
	
	/**
	 * Gets a list with the names and ids of all definied friends lists of
	 * the current user.
	 *
	 * @return array Definied friends lists (empty if no lists defined)
	 */
	public function getLists() {
		$parameters = array();
		return $this->client->callMethod("friends.getLists", $parameters, $this->session);
	}
	
	/**
	 * Checks for the friends of the current user if they have also
	 * authorized the application. For each friend id a 0 means no and
	 * a 1 means yes.
	 *
	 * @return array Flag for each friend with the friend id as key.
	 */
	public function getAppUsers() {
		return $this->client->callMethod("friends.getAppUsers", array(), $this->session);
	}
	
	/**
	 * Checks for a list of user ids if they are friends of the current user.
	 *
	 * @param
	 * @return
	 */
	public function areFriends($uids) {
		if (count($uids) < 1) {
			throw new InvalidArgumentException('You have to specify at least one user id');
		}
		
		$parameters = array();
		$parameters['uids'] = implode(',', $uids);
		
		return $this->client->callMethod('friends.areFriends', $parameters, $this->session);
	}
}

?>
