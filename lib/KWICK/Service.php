<?php

/**
 * This abstract class is the foundation for all client-side service
 * implementations. It provides properties for handling the client instance
 * and the optional session instance.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
abstract class KWICK_Service {
	/**
	 * @var KWICK_Client
	 */
	private $client;

	/**
	 * @var KWICK_Session
	 */
	private $session;
	
	/**
	 * Creates a new instance.
	 *
	 * @param KWICK_Client  $client  The client instance.
	 * @param KWICK_Session $session The optional user session.
	 */
	public function __construct(KWICK_Client $client, KWICK_Session $session = null) {
		$this->client = $client;
		$this->session = $session;
	}
	
	/**
	 * Gets the value of the specified property.
	 *
	 * @param string $name Property name.
	 * @return mixed Property value.
	 */
	public function __get($name) {
		$value = null;
		
		switch ($name) {
			case 'client':
				$value = $this->client;
				break;
			case 'session':
				$value = $this->session;
				break;
			default:
				throw new InvalidArgumentException('Invalid property specified');
		}
		
		return $value;
	}
}
