<?php

/**
 * The client implements properties and methods
 * for the low-level access to the KWICK! API.
 *
 * @author KWICK! Community <developer@kwick.de>
 * @version 1.0
 */
class KWICK_Client {
	const USER_AGENT = 'kwick.sdk/1.0';

	/**
	 * @var string The API key which identifies the application.
	 */
	private $apiKey;

	/**
	 * @var string The application-specific secret key which is used to
	 *             generate request signatures for global methods.
	 */
	private $secret;

	/**
	 * @var string The base URL of the remote API webservice of KWICK!.
	 */
	private $baseUrl;

	/**
	 * @var string
	 */
	private $lastError;

	/**
	 * @var int
	 */
	private $lastErrorCode;
	
	/**
	 * @var KWICK_Response
	 */
	private $lastResponse;

	/**
	 * Creates and initializes a new instance.
	 *
	 * @param string $apiKey  The API key which identifies the application.
	 * @param string $secret  The application-specific secret key.
	 * @param string $baseUrl The base URL of the KWICK! API.
	 */
	public function __construct($apiKey, $secret, $baseUrl = 'http://api.kwick.de/service') {
		if (empty($apiKey)) {
			throw new InvalidArgumentException('Invalid API key specified (null or empty)');
		}

		if (empty($secret)) {
			throw new InvalidArgumentException('Invalid secret specified (null or empty)');
		}

		if (empty($baseUrl)) {
			throw new InvalidArgumentException('Invalid base URL specified (null or empty)');
		}

		if (!function_exists('curl_init')) {
			throw new InvalidArgumentException('Required extension php_curl is not available/enabled');
		}

		$this->apiKey = $apiKey;
		$this->secret = $secret;
		$this->baseUrl = $baseUrl;

		$this->lastError = null;
		$this->lastErrorCode = -1;
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
			case 'apiKey':
				$value = $this->apiKey;
				break;
			case 'secret':
				$value = $this->secret;
				break;
			case 'baseUrl':
				$value = $this->baseUrl;
				break;
			case 'lastError':
				$value = $this->lastError;
				break;
			case 'lastErrorCode':
				$value = $this->lastErrorCode;
				break;
			case 'lastResponse':
				$value = $this->lastResponse;
				break;
			default:
				throw new InvalidArgumentException('Invalid property specified');
		}

		return $value;
	}
	
	/**
	 * Calls the specified method and returns the service response.
	 *
	 * @param string        $method     The API method specifier.
	 * @param array         $parameters The optional method-specific request parameters.
	 * @param KWICK_Session $session    The optional KWICK! API user session instance.
	 * @return KWICK_Response Service response.
	 */
	public function callMethod($method, array $parameters = array(), KWICK_Session $session = null) {
		if (empty($method)) {
			throw new InvalidArgumentException('Invalid method specified (null or empty)');
		}

		$this->resetErrors();

		$parameters = $this->prepareParameters($method, $parameters, $session);
		$rawResponse = $this->doPostRequest($parameters);
		$response = KWICK_Response::parse($rawResponse);
		
		$this->lastResponse = $response;
		
		$body = $response->getBody();
		
		if (is_array($body) && array_key_exists('error_code', $body)) {
			$errorCode = (int)$body['error_code'];
			$errorMsg  = $body['error_msg'];
			throw new KWICK_Remote_Exception($errorMsg, $errorCode);
		}
		
		return $body;
	}

	/**
	 * Cleans up the errors.
	 */
	private function resetErrors() {
		$this->lastError = null;
		$this->lastErrorCode = -1;
	}

	/**
	 * Prepare the request parameters by adding some standard parameters and the request
	 * signature.
	 *
	 * @param string        $method     The API method specifier.
	 * @param array         $parameters The optional method-specific request parameters.
	 * @param KWICK_Session $session    The optional KWICK! API user session instance.
	 * @return array The final request parameters.
	 */
	public function prepareParameters($method, array $parameters = array(), KWICK_Session $session = null) {
		if (empty($method)) {
			throw new InvalidArgumentException('Invalid method specified (null or empty)');
		}

		$parameters['method']  = $method;
		$parameters['v']       = '2.0';
		$parameters['api_key'] = $this->apiKey;
		$parameters['format']  = 'JSON';
		
		if (!is_null($session)) {
			$parameters['session_key'] = $session->sessionKey;
		}

		$signature = $this->generateSignature($parameters, $session);
		$parameters['sig'] = $signature;

		return $parameters;
	}

	/**
	 * Generate the request signature.
	 *
	 * @param array         $parameters The request parameters.
	 * @param KWICK_Session $session    The optional session.
	 * @return string The request signature.
	 */
	public function generateSignature(array $parameters, KWICK_Session $session = null) {
		$paramStr = '';

		// Sort parameters and create the string from them
		ksort($parameters);
		foreach ($parameters as $name => $value) {
			// Do not append an old signature
			if ($name !== 'sig') {
				$paramStr .= sprintf('%s=%s', $name, $value);
			}
		}

		// Append correct secret
		if (is_null($session)) {
			$paramStr .= $this->secret;
		}
		else {
			$paramStr .= $session->secret;
		}

		return md5($paramStr);
	}

	/**
	 * Performs the real POST request and returns the complete service response including
	 * all headers.
	 *
	 * @param array $postParameters POST parameters.
	 * @return string Raw service response.
	 */
	public function doPostRequest(array $postParameters) {
		$rawResponse = null;

		try {
			$ch = curl_init($this->baseUrl);

			if (is_null($ch) || $ch === false) {
				throw new KWICK_Exception('Could not initialize cURL');
			}

			$options = array(
				CURLOPT_USERAGENT      => KWICK_Client::USER_AGENT,		// The content for the User-Agent header
				CURLOPT_POST           => true,							// Create a POST request
				CURLOPT_RETURNTRANSFER => true,							// Return the response as string at curl_exec()
				CURLOPT_POSTFIELDS     => $postParameters,
				CURLOPT_HTTPHEADER     => array('Expect: '),			// Additional request headers
				CURLOPT_HEADER         => true,							// Include response headers in output
			);

			curl_setopt_array($ch, $options);

			$rawResponse = curl_exec($ch);
			if ($rawResponse === false || intval(curl_error($ch)) !== 0) {
				$this->lastError = curl_error($ch);
				$this->lastErrorCode = curl_errno($ch);

				throw new KWICK_Exception(sprintf('Request failed: %s (code %d)', $this->lastError, $this->lastErrorCode));
			}

			curl_close($ch);
		}
		catch (Exception $ex) {
			error_log(sprintf('%s(%d) [ERROR] %s in %s(%d)', __FILE__, __LINE__, $ex->getMessage(), $ex->getFile(), $ex->getLine()));

			if ($ex instanceof KWICK_Exception) {
				throw $ex;
			}
			else {
				$this->lastError = sprintf('%s in %s(%d)', $ex->getMessage(), $ex->getFile(), $ex->getLine());
				$this->lastErrorCode = $ex->getCode();
			}
		}

		return $rawResponse;
	}
}

?>
