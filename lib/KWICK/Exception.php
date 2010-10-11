<?php

/**
 * This exception is thrown if a local error with the API occurred.
 * 
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Exception extends RuntimeException {
	/**
	 * The exception which caused this exception.
	 *
	 * @var Exception
	 */
	private $innerException;

	/**
	 * Creates a new instance.
	 *
	 * @param <type> $message
	 * @param <type> $code
	 * @param Exception $innerException
	 */
	public function __construct($message, $code = 1, Exception $innerException = null) {
		parent::__construct($message, $code);
		
		$this->innerException = $innerException;
	}

	/**
	 * Gets the exception which was the cause for this exception.
	 *
	 * @return Exception Cause
	 */
	public function getInnerException() {
		return $this->innerException;
	}
}
