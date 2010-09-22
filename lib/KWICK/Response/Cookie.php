<?php

/**
 * Provides properties for HTTP cookies.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Response_Cookie {
	private $name;
	private $expires;
	private $path;
	private $domain;
	private $value;
	
	private function __construct() {
	}
	
	/**
	 * Gets the current value of the specified property.
	 *
	 * @param string $name Property name.
	 * @return mixed Current property value.
	 */
	public function __get($name) {
		if (empty($name)) {
			throw new InvalidArgumentException();
		}
		
		$value = null;
		
		switch ($name) {
			case 'Name':
				$value = $this->name;
				break;
			case 'Value':
				$value = $this->value;
				break;
			case 'Expires':
				$value = (int)$this->expires;
				break;
			case 'Path':
				$value = $this->path;
				break;
			case 'Domain':
				$value = $this->domain;
				break;
		}
		
		return $value;
	}

	/**
	 * Parses a string and creates a cookie from it.
	 *
	 * @param string $rawCookie The cookie string (from Set-Cookie header).
	 * @return KWICK_Response_Cookie The created cookie.
	 */	
	public static function parse($rawCookie) {
		if (empty($cookie)) {
		}
		
		$cookie = null;
		
		$parts = explode('; ', $rawCookie);
		if (count($parts) < 1) {
			throw new InvalidArgumentException('Invalid cookie specified');
		}
		
		$cookieName    = null;
		$cookieValue   = null;
		$cookiePath    = null;
		$cookieDomain  = null;
		$cookieExpires = null;
		
		foreach ($parts as $part) {
			list($key, $value) = explode('=', $part);
			if (empty($key) || empty($value)) {
				throw new KWICK_Exception('Invalid cookie');
			}
			
			switch ($key) {
				case 'expires':
					$cookieExpires = $value;
					break;
				case 'path':
					$cookiePath = stripslashes($value);
					break;
				case 'domain':
					$cookieDomain = $value;
					break;
				default:
					$cookieName = $key;
					$cookieValue = $value;
			}
		}
		
		$cookie = new KWICK_Response_Cookie();
		$cookie->name    = $cookieName;
		$cookie->value   = $cookieValue;
		$cookie->expires = $cookieExpires;
		$cookie->path    = $cookiePath;
		$cookie->domain  = $cookieDomain;
		
		return $cookie;
	}
}
