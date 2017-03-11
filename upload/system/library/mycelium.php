<?php

class MyceliumException extends Exception {}
/**
 * Mycelium Library for OpenCart
 */
class Mycelium {

	/** @var int $version */
	public $version = '1.0.0';
	
	public $api_developer_endpoint = 'https://admin.gear.mycelium.com';
	
	public $api_gateway_endpoint = 'https://gateway.gear.mycelium.com';
	
	public $api_pay_endpoint = 'https://gateway.gear.mycelium.com/pay/';
	
	public $gatewaySecret;
	
	private $gatewayId;

	/** @var Registry $registry */
	private $registry;

	/** @var Log $logger */
	public $logger;

	/**
	 * Mycelium Library constructor
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		$this->registry = $registry;

		// Setup logging
		$this->logger = new Log('mycelium.log');
		
		// Setup encryption
		$fingerprint = substr(sha1(sha1(__DIR__)), 0, 24);
		$this->encryption = new Encryption($fingerprint);
		
		$this->setup();

	}
	
	public function setup()
	{
		$this->gatewaySecret = @unserialize($this->encryption->decrypt($this->setting('gateway_secret')));
		$this->gatewayId = $this->setting('gateway_id');
		return;
	}
	
	public function isSetup()
	{
		if($this->gatewaySecret)
			return true;
		
		return false;
	}

	/**
	 * Magic getter for Registry items
	 *
	 * Allows use of $this->db instead of $this->registry->get('db') for example
	 *
	 * @return mixed
	 */
	public function __get($name) {
		return $this->registry->get($name);
	}

	/**
	 * Imports gateway settings from mycelium site to fill the admin form
	 * 
	 * It is like a fast copy&paste
	 * 
	 * @param string $id The mycelium gateway identifier
	 * @return void
	 */
	public function importSettings($id)
	{
		// TODO, not being able to auth the signature
		$json = $this->apiCall('/api/gateways', 'POST', array(), ['Accept-Version: v1']);
		return $json;
	}
	
	/**
	 * Creates a new order and retrieves an address and payment id from mycelium
	 * 
	 * @param float $amount The amount in the currency set at mycelium settings (USD)
	 * @param $callback_data A optional string with data which will be returned to callback
	 * @return array with mycelium response
	 */
	public function createOrder($amount, $callback_data = '', $keychain_id = null)
	{
		$uri = '/gateways/' . $this->gatewayId . '/orders?amount=' . $amount;
		
		if($keychain_id != null)
			$uri .= '&keychain_id=' . $keychain_id;
			
		if(!empty($callback_data))
			$uri .=  '&callback_data=' . $callback_data;
		
		$res = $this->apiCall($this->api_gateway_endpoint, $uri, 'POST');
		$json = @json_decode($res, true);
		
		if(empty($json))
		{
			// maybe log this? :P
			$this->log('error', $this->language->get('log_response_error') . " Data: " . $res);
			throw new MyceliumException('Invalid json response from Mycelium');
		}
		
		
		// all should be good here
		$json['pay_url'] = $this->api_pay_endpoint . $json['payment_id'];
		$json['gateway_id'] = $this->gatewayId;
		return $json;
	}
	
	/**
	 * Asks Mycelium about a payment status
	 * 
	 * @param string $paymentId The mycelium payment id.
	 * @return array
	 */
	public function retrieveOrder($paymentId)
	{
		$res =  $this->apiCall($this->api_gateway_endpoint, "/gateways/" . $this->gatewayId . "/orders/" . $paymentId, 'GET');
		
		$res = @json_decode($res, true);
		
		if(!empty($res))
		{
			$res['pay_url'] = $this->api_pay_endpoint . $res['payment_id'];
			$res['gateway_id'] = $this->gatewayId;
			return $res;
		}
		
		throw new MyceliumException('Unable to retrieve order from Mycelium endpoint.');
	}
	
	/**
	 * Checks the signature provided against the data received at callback
	 * 
	 * @param string $signature
	 * @param string $uri
	 * @param string $method
	 * @return bool
	 */
	public function checkCallbackSignature($signature, $uri, $method = 'GET')
	{
		if($signature == base64_encode(hash_hmac('sha512', $method . $uri . hash('sha512', '', true), $this->gatewaySecret, true)))
			return true;
		
		return false;
	}


	/**
	 * Logs with an arbitrary level.
	 * @param string $level The type of log.
	 *						Should be 'error', 'warn', 'info', 'debug', 'trace'
	 *						In normal mode, 'error' and 'warn' are logged
	 *						In debug mode, all are logged
	 * @param string $message The message of the log
	 * @param int $depth How deep to go to find the calling function
	 * @return void
	 */
	public function log($level, $message, $depth = 0) {
		$level = strtoupper($level);
		$prefix = '[' . $level . ']';

		// Debug formatting
		if ($this->setting('debug') === '1') {
			$depth += 1;
			$prefix .= '{';
			$backtrace = debug_backtrace();
			if (isset($backtrace[$depth]['class'])) {
				$class = preg_replace('/[a-z]/', '', $backtrace[$depth]['class']);
				$prefix .= $class . $backtrace[$depth]['type'];
			}
			if (isset($backtrace[$depth]['function'])) {
				$prefix .= $backtrace[$depth]['function'];
			}
			$prefix .= '}';
		}

		if ('ERROR' === $level || 'WARN' === $level || $this->setting('debug') === '1') {
			$this->logger->write($prefix . ' ' . $message);
		}
	}

	/**
	 * Better setting method for mycelium settings
	 *
	 * Automatically persists to database on set and combines getting and setting into one method
	 * Assumes mycelium_ prefix
	 *
	 * @param string $key Setting key
	 * @param string $value Setting value if setting the value
	 * @return string|null|void Setting value, or void if setting the value
	 */
	public function setting($key, $value = null) {
		// Normalize key
		$key = 'mycelium_' . $key;
		
		// Set the setting
		if (func_num_args() === 2) {
			if (!is_array($value)) {
				$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0' WHERE `code` = 'mycelium' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '0'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1' code `group` = 'mycelium' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '0'");
			}
			return $this->config->set($key, $value);
		}

		// Get the setting
		return $this->config->get($key);
	}
	
	/**
	 * Makes the call to mycelium endpoint
	 * 
	 * @param string $endpoint The api endpoint where to send the request
	 * @param string $method The request http method
	 * @param array $payload The data which is sent along the request
	 * @param array $headers Optional headers to be sent along the request
	 * @throws MyceliumException on invalid gateway secret
	 * @return array $res Mycelium response json decoded
	 */
	public function apiCall($endpoint, $uri, $method = 'POST', $payload = array(), $headers = array())
	{
		
		if($this->isSetup())
		{
			// generate signature
			
			$request_body = '';
			$mt = explode(' ', microtime());
			$nonce = $mt[1] . $mt[0];
			
			$signature = base64_encode(hash_hmac('sha512', $method . $uri . hash('sha512', $nonce . $request_body, true), $this->gatewaySecret, true));
			
			array_push($headers, 'Content-Type: application/json');
			array_push($headers, 'X-Nonce: ' . $nonce);
			array_push($headers, 'X-Signature: ' . $signature);
			
			
			
			
			// curl
			$curl = curl_init();

			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => $endpoint . $uri,
			    CURLOPT_HTTPHEADER => $headers,
			    CURLOPT_POSTFIELDS => '',
			    CURLOPT_HEADER => 0
			));
			
			curl_setopt($curl, CURLOPT_POST, 0);
			if($method == 'POST')
				curl_setopt($curl, CURLOPT_POST, 1);
			
			
			$res = curl_exec($curl);
			curl_close($curl);
			return $res;
		}
		else
		{
			throw new MyceliumException('Gateway secret empty/not set');
		}
	}
}
