<?php

/**
 * This class provides properties for handling KWICK! API user sessions.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Session {
	private $sessionKey;
	private $sessionSecret;
	private $userId;
	private $expires;

	/**
	 * Creates and initialized a new instance.
	 */
	public function __construct() {
	}

	/**
	 * Gets the current value of a property.
	 *
	 * @param string $name Property name.
	 * @return mixed Current property value.
	 */
	public function __get($name) {
		$value = null;

		switch ($name) {
			case 'sessionKey':
				$value = $this->sessionKey;
				break;
			case 'sessionSecret':
			case 'secret':
				$value = $this->sessionSecret;
				break;
			case 'userId':
				$value = (int)$this->userId;
				break;
			case 'expires':
				$value = (int)$this->expires;
				break;
			default:
				throw new InvalidArgumentException('Unknown property specified');
		}

		return $value;
	}

	/**
	 * Sets the value of a property.
	 *
	 * @param string $name  Property name.
	 * @param mixed  $value New property value.
	 */
	public function __set($name, $value) {
		switch ($name) {
			case 'sessionKey':
				if (empty($value)) {
					throw new InvalidArgumentException('Invalid session key specified');
				}

				$this->sessionKey = $value;

				break;
			case 'sessionSecret':
			case 'secret':
				if (empty($value)) {
					throw new InvalidArgumentException('Invalid session secret specified');
				}

				$this->sessionSecret = $value;

				break;
			case 'userId':
				if (!is_numeric($value) || intval($value) < 1) {
					throw new InvalidArgumentException('Invalid user ID specified (not numeric or lighter than 1)');
				}

				$this->userId = (int)$value;

				break;
			case 'expires':
				if (!is_numeric($value) || intval($value) < 0) {
					throw new InvalidArgumentException('Invalid expiration timestamp specified (not numeric or lighter than 0)');
				}

				$this->expires = (int)$value;

				break;
			default:
				throw new InvalidArgumentException('Unknown property specified');
		}
	}

	/**
	 * Checks if the current session is expired.
	 *
	 * @return bool True, if the session expired, otherwise true.
	 */
	public function isExpired() {
		return ($this->expires !== 0 && $this->expires < time());
	}

	/**
	 * Checks if the current session is infinite.
	 *
	 * @return bool True, if the session is infinite, otherwise false.
	 */
	public function isInfinite() {
		return $this->expires === 0;
	}
	
	/**
	 * Checks if the session is valid (all properties set correctly).
	 *
	 * @return bool True, if the session is valid, otherwise false;
	 */
	public function isValid() {
		return (!empty($this->sessionKey) && !empty($this->sessionSecret) && is_numeric($this->userId) && intval($this->userId) > 0 && is_numeric($this->expires) && intval($this->expires) >= 0);
	}
}
