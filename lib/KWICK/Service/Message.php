<?php
/**
 * @see KWICK_Service
 */
require_once 'KWICK/Service.php';

/**
 * This class implements the client side of the Message service.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Service_Message extends KWICK_Service {
	/**
	 * Creates and initializes a new instance.
	 *
	 * @param KWICK_Client $client
	 * @param KWICK_Session $session
	 */
	public function __construct(KWICK_Client $client, KWICK_Session $session) {
		parent::__construct($client, $session);
	}

	/**
	 * Gets an overview of the messages system.
	 *
	 * @return array Structure with information about the number of messages in each folder.
	 */
	public function getStats() {
		$parameters = array();

		$serviceResponse = $this->client->callMethod('message.getStats', $parameters, $this->session);

		return $serviceResponse;
	}

	/**
	 * Gets the messages for the folders "received", "sent" and "parked".
	 *
	 * @param int $page Page number (-1 = all)
	 * @return array Structure with messages.
	 */
	public function get($page = -1) {
		if (!is_numeric($page)) {
			throw new InvalidArgumentException('Invalid page number specified');
		}

		$parameters = array();
		
		if (intval($page < 0)) {
			$parameters['page'] = -1;
		}
		else {
			$parameters['page'] = intval($page);
		}
		
		$serviceResponse = $this->client->callMethod('message.get', $parameters, $this->session);
		if (!is_array($serviceResponse)) {
			throw new KWICK_Exception('Invalid response received');
		}

		return $serviceResponse;
	}

	/**
	 * Receives a single channel for a specified communication partner.
	 *
	 * @param int $partner User id of the communication partner
	 * @param int $channel Channel id (default 0)
	 * @return array Channel content
	 */
	public function receive($partner, $channel = 0) {
		if (!is_numeric($partner) || intval($partner) < 1) {
			throw new InvalidArgumentException('Invalid user id for partner specified');
		}

		if (!is_numeric($channel) || intval($channel) < 0) {
			throw new InvalidArgumentException('Invalid channel specified');
		}

		$parameters = array();
		$parameters['partner_id'] = intval($partner);
		$parameters['channel']    = intval($channel);

		$serviceResponse = $this->client->callMethod('message.receive', $parameters, $this->session);

		return $serviceResponse;
	}
	
	/**
	 * Sends a message with the specified text to the specified receipient at
	 * the specified channel.
	 * 
	 * @param int    $receipient User id of the receipient.
	 * @param string $text       Message text.
	 * @param int    $channel    Channel (default 0)
	 * @param bool   $isAnswer   True if this is an answer (default false)
	 * @return bool True, if the message was sent.
	 */
	public function send($receipient, $text, $channel = 0, $isAnswer = false) {
		if (!is_numeric($receipient) || intval($receipient) < 1) {
			throw new InvalidArgumentException('Invalid user id specified for receipient');
		}

		if (empty($text)) {
			throw new InvalidArgumentException('Invalid messages text specified');
		}

		if (!is_numeric($channel) || intval($channel) < 0) {
			throw new InvalidArgumentException('Invalid channel number specified');
		}

		if (!is_bool($isAnswer)) {
			throw new InvalidArgumentException('Invalid value specified for isAnswer');
		}

		$parameters = array();

		$parameters['receipient'] = intval($receipient);
		$parameters['message']    = $text;
		$parameters['channel']    = intval($channel);
		$parameters['is_answer']  = $isAnswer;

		$serviceResponse = $this->client->callMethod('message.send', $parameters, $this->session);

		return (bool)$serviceResponse;
	}

	/**
	 * Deletes a messages channel.
	 *
	 * @param int $partner User id of the communication partner
	 * @param int $channel Channel id (default 0).
	 * @return bool True if channel was deleted.
	 */
	public function delete($partner, $channel = 0) {
		if (!is_numeric($partner) || intval($partner) < 1) {
			throw new InvalidArgumentException('Invalid user id for partner specified');
		}
		
		if (!is_numeric($channel) || intval($channel) < 0) {
			throw new InvalidArgumentException('Invalid channel specified');
		}
		
		$parameters = array();
		$parameters['partner_id'] = intval($partner);
		$parameters['channel']    = intval($channel);

		$serviceResponse = $this->client->callMethod('message.delete', $parameters, $this->session);

		return (bool)$serviceResponse;
	}
}