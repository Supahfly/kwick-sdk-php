<?php

/**
 * This class encapsulates the response of the API webservice.
 * 
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Response {
	/**
	 * @var array Received headers.
	 */
	private $headers;
	
	/**
	 * @var string Received body.
	 */
	private $body;
	
	/**
	 * @var array Received cookies.
	 */
	private $cookies;
	
	/**
	 * @var string Status line.
	 */
	private $statusLine;
	
	/**
	 * Creates a new instance.
	 */
	private function __construct() {
		$this->headers = array();
		$this->cookies = array();
		$this->body = '';
		$this->statusLine = null;
	}

	/**
	 * Adds an header.
	 *
	 * @param KWICK_Response_Header $header Header instance.
	 */
	protected function addHeader(KWICK_Response_Header $header) {
		$this->headers[$header->Name] = $header;
	}

	/**
	 * Gets the names of all available headers.
	 *
	 * @return array Available headers.
	 */
	public function getHeaderNames() {
		return array_keys($this->headers);
	}
	
	/**
	 * Gets the specified header or null, if the header is not available.
	 *
	 * @param string $name Header name.
	 * @return KWICK_Response_Header Header value
	 */
	public function getHeader($name) {
		if (empty($name)) {
			throw new InvalidArgumentException('Invalid header name specified');
		}
		
		$header = $null;
		
		if (array_key_exists($name, $this->headers)) {
			$header = $this->headers[$name];
		}
		
		return $header;
	}

	/**
	 * Adds a cookie.
	 *
	 * @param KWICK_Response_Cookie $cookie Cookie instance.
	 */
	protected function addCookie(KWICK_Response_Cookie $cookie) {
		$this->cookies[$cookie->Name] = $cookie;
	}

	/**
	 * Gets the names of all available cookies.
	 *
	 * @return array Available cookies.
	 */
	public function getCookieNames() {
		return array_keys($this->cookies);
	}
	
	/**
	 * Gets the specified cookie or null, if the cookie is not available.
	 *
	 * @param string $name         Cookie name.
	 * @return KWICK_Response_Cookie Cookie
	 */
	public function getCookie($name) {
		if (empty($name)) {
			throw new InvalidArgumentException('Invalid cookie name specified');
		}
		
		$cookie = null;
		
		if (array_key_exists($name, $this->cookies)) {
			$cookie = $this->cookies[$name];
		}
		
		return $cookie;
	}
	
	/**
	 * Sets the received body.
	 *
	 * @param string $body Received body.
	 */
	protected function setBody($body) {
		$this->body = $body;
	}

	/**
	 * Gets the received body.
	 *
	 * @return string Received body.
	 */
	public function getBody() {
		return $this->body;
	}
	
	/**
	 * Sets the status line.
	 *
	 * @param string $statusLine Status line.
	 */
	protected function setStatusLine($statusLine) {
		$this->statusLine = $statusLine;
	}

	/**
	 * Gets the status line.
	 *
	 * @return string Status line.
	 */
	public function getStatusLine() {
		return $this->statusLine;
	}
	
	/**
	 * Parses the raw response and creates a new instance of KWICK_Response.
	 *
	 * @param string $rawResponse Received raw response
	 * @return KWICK_Response created instance.
	 */
	public static function parse($rawResponse) {
		if (empty($rawResponse)) {
			throw new InvalidArgumentException('Invalid raw response specified');
		}
		
		$mainParts = explode("\r\n\r\n", $rawResponse);
		if (count($mainParts) < 2) {
			throw new KWICK_Exception('Invalid response: no headers');
		}
		
		$headerLines = explode("\r\n", $mainParts[0]);
		if (count($headerLines) < 1) {
			throw new KWICK_Exception('Invalid header');
		}
		
		$response = new KWICK_Response();
		
		foreach ($headerLines as $headerLine) {
			$header = explode(': ', $headerLine);
			if (count($header) === 1) {
				$response->setStatusLine($header[0]);
			}
			else {
				if ($header[0] === "Set-Cookie") {
					$cookie = KWICK_Response_Cookie::parse($header[1]);
					$response->addCookie($cookie);
				}
				else {
					$header = new KWICK_Response_Header($header[0], $header[1]);
					$response->addHeader($header);
				}
			}
		}
		
		$response->body = json_decode($mainParts[1], true);
		
		return $response;
	}
}

?>
