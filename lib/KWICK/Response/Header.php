<?php

/**
 * Provides properties for HTTP headers.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Response_Header {
	private $name;
	private $value;
	
	/**
	 * Creates a new instance.
	 *
	 * @param string $header Header name
	 * @param string $value  Header value
	 */
	public function __construct($name, $value) {
		if (empty($name)) {
			throw new InvalidArgumentException('Invalid name specified');
		}
		
		if (empty($value)) {
			throw new InvalidArgumentException('Invalid value specified');
		}
		
		$this->name = $name;
		$this->value = $value;
	}
	
	/**
	 * Gets the current value of the specified property.
	 *
	 * @param string $name Property name.
	 * @return mixed Current property value.
	 */
	public function __get($name) {
		$value = null;
		
		switch ($name) {
			case 'Name':
				$value = $this->name;
				break;
			case 'Value':
				$value = $this->value;
				break;
			default:
				throw new InvalidArgumentException('Invalid property specified');
		}
		
		return $value;
	}
}
