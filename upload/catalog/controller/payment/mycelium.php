<?php
/**
 * Mycelium Payment Controller
 */
class ControllerPaymentMycelium extends Controller {


	/** @var Mycelium $mycelium */
	private $mycelium;

	/**
	 * mycelium Payment Controller Constructor
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);

		// Make language strings and mycelium Library available to all
		$this->load->language('payment/mycelium');
		$this->mycelium = new Mycelium($registry);

		// Setup logging
		$this->logger = new Log('mycelium.log');

	}

	/**
	 * Displays the Payment Method (a redirect button)
	 * @return void
	 */
	public function index() {
		$data['url_redirect'] = $this->url->link('payment/mycelium/confirm', $this->config->get('config_secure'));
		$data['button_confirm'] = $this->language->get('button_confirm');

		if (isset($this->session->data['error_mycelium'])) {
			unset($this->session->data['error_mycelium']);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/mycelium.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/mycelium.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/mycelium.tpl', $data);
		}
	}

	/**
	 * Generates the gateway itself
	 * @return void
	 */
	public function confirm() {
		$this->load->model('checkout/order');
		$this->load->model('payment/mycelium');
		if (!isset($this->session->data['order_id'])) {
			$this->response->redirect($this->url->link('checkout/cart'));
			return;
		}
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if (false === $order_info) {
			$this->response->redirect($this->url->link('checkout/cart'));
			return;
		}
		
		
		// generate new order
		try{
			$keychain_id = $this->model_payment_mycelium->getFreeKeychainId();
			$extra = 'opencart_order_id_' . $order_info['order_id'];
			$extra .= '/mycelium_keychain_id_' . $keychain_id;
			$order = $this->mycelium->createOrder($order_info['total'], $extra, $keychain_id);
		}catch(Exception $e){
			$this->session->data['error_mycelium'] = 'Sorry, but there was a problem communicating with Mycelium for Bitcoin checkout.';
			$this->response->redirect($this->url->link('checkout/checkout'));
			return;
		}
			
		
		$this->session->data['mycelium_invoice'] = $order['payment_id'];
		
		
		// layout
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['gateway_url'] = $order['pay_url'];
		$data['payment_id'] = $order['payment_id'];
		$data['gateway_id'] = $order['gateway_id'];
		$data['address'] = $order['address'];
		$data['bitcoin_amount'] = $order['amount_in_btc'];
		
		$data['return_url'] = $this->setting('return_url');
		$data['cancel_url'] = $this->setting('back_url');
		$data['shifty_enabled'] = $this->setting('shifty_enabled');
		$data['expiration_time'] = $this->setting('expiration_period');
		
		// language
		$data['please_send'] = $this->language->get('please_send');
		$data['to_address'] = $this->language->get('to_address');
		$data['success_text'] = $this->language->get('success_text');
		$data['expired_text'] = $this->language->get('expired_text');
		$data['back_button'] = $this->language->get('back_button');
		
		$this->response->setOutput($this->load->view('default/template/payment/mycelium_hosted.tpl', $data));
	}

	/**
	 * Convenience wrapper for mycelium logs
	 * @param string $level The type of log.
	 *					  Should be 'error', 'warn', 'info', 'debug', 'trace'
	 *					  In normal mode, 'error' and 'warn' are logged
	 *					  In debug mode, all are logged
	 * @param string $message The message of the log
	 * @param int $depth Depth addition for debug backtracing
	 * @return void
	 */
	public function log($level, $message, $depth = 0) {
		$depth += 1;
		$this->mycelium->log($level, $message, $depth);
	}

	/**
	 * Convenience wrapper for mycelium settings
	 *
	 * Automatically persists to database on set and combines getting and setting into one method
	 * Assumes 'mycelium_' prefix
	 *
	 * @param string $key Setting key
	 * @param string $value Setting value if setting the value
	 * @return string|null|void Setting value, or void if setting the value
	 */
	public function setting($key, $value = null) {
		// Set the setting
		if (func_num_args() === 2) {
			return $this->mycelium->setting($key, $value);
		}

		// Get the setting
		return $this->mycelium->setting($key);
	}
	
	
	/**
	 * Order expired or cancelled page
	 * 
	 * Clears the session and redirects to checkout page
	 * 
	 * @return void
	 */
	public function cancel()
	{
		$this->load->model('checkout/order');
		
		$this->session->data['order_id'] = null;
		$this->session->data['mycelium_invoice'] = null;
		
		$this->response->redirect($this->url->link('checkout/checkout'));
		return;
	}


	/**
	 * Success return page
	 *
	 * Progresses the order if valid, and redirects to OpenCart's Checkout Success page
	 *
	 * @return void
	 */
	public function success() {
		$this->load->model('checkout/order');
		$order_id = $this->session->data['order_id'];

		if (is_null($order_id)) {
			$this->response->redirect($this->url->link('checkout/success'));
			return;
		}

		$order = $this->model_checkout_order->getOrder($order_id);
		
		try {
			$order = $this->mycelium->retrieveOrder($this->session->data['mycelium_invoice']);
		} catch (Exception $e) {
			$this->response->redirect($this->url->link('checkout/success'));
			return;
		}

		switch ($order['status']) {
			case '1':
				$order_status_id = $this->setting('paid_status');
				$order_message = $this->language->get('text_progress_paid');
				break;
			case '2':
				$order_status_id = $this->setting('complete_status');
				$order_message = $this->language->get('text_progress_complete');
				break;
			case '4':
				$order_status_id = $this->setting('complete_status');
				$order_message = $this->language->get('text_progress_complete');
				break;
			default:
				$this->response->redirect($this->url->link('checkout/checkout'));
				return;
		}

		// Progress the order status
		$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
		$this->session->data['mycelium_invoice'] = null;
		$this->response->redirect($this->url->link('checkout/success'));
		
	}

	/**
	 * IPN Handler
	 * @return void
	 */
	public function callback() {
		$this->load->model('checkout/order');
		$this->load->model('payment/mycelium');
		
		$signature_header = (isset($this->request->server['HTTP_X_SIGNATURE'])) ? $this->request->server['HTTP_X_SIGNATURE'] : '';
		$uri = $this->request->server['REQUEST_URI'];
		
		// we have to replace the redirect url with a url-encoded url
		// when we retrieve it from $_SERVER['REQUEST_URI'] it comes like https://blabla.com/index.php?redirect_url=https://blahblah.com/redirect
		// to reproduce the same signature it should be like redirect_url=https%3A%2F%2Fblahblah.com%2Findex.php%3Froute%3Dpayment%2Fmycelium%2Fsuccess
		// so yeah, a couple of hours here trying to hash the same signature until I found that :P
		$uri = str_replace($this->url->link('payment/mycelium/success'), urlencode($this->url->link('payment/mycelium/success')), $uri);
		
		// hmmm also ampersands are encoded as &amp; when retrived from $this->url ...
		$uri = str_replace('&amp;', '&', $uri);
		
		// check signature
		if(!$this->mycelium->checkCallbackSignature($signature_header, $uri, 'GET'))
		{
			// invalid signature
			$this->log('warn', 'Invalid signature at callback. Either someone is trying to submit fake callbacks or the Gateway Secret was not loaded properly.');
			return;
		}
		
		$mycelium_id = $this->request->get['order_id'];
		$amount = $this->request->get['amount'];
		$status = $this->request->get['status'];
		$amount_btc = $this->request->get['amount_in_btc'];
		$amount_btc_paid = $this->request->get['amount_paid_in_btc'];
		$address = $this->request->get['address'];
		$callback_data = $this->request->get['callback_data'];
		$callback_data = explode('/', $callback_data);
		
		$opencart_order_id = str_replace('opencart_order_id_', '', $callback_data[0]);
		$keychain_id = str_replace('mycelium_keychain_id_', '', $callback_data[1]);
		

		switch ($status) {
			case '1':
				// paid but not confirmed yet
				$order_status_id = $this->setting('paid_status');
				$order_message = $this->language->get('text_progress_paid');
				break;
			case '2':
				// paid, confirmed
				$order_status_id = $this->setting('complete_status');
				$order_message = $this->language->get('text_progress_complete');
				$this->model_payment_mycelium->freeKeychainId($keychain_id);
				break;
			case '4':
				// paid, confirmed, overpaid actually
				$order_status_id = $this->setting('complete_status');
				$order_message = $this->language->get('text_progress_complete');
				$this->model_payment_mycelium->freeKeychainId($keychain_id);
				break;
			case '5':
				// expired, do nothing
				$this->response->redirect($this->url->link('checkout/checkout'));
				return;
			case '6':
				// canceled
				$this->response->redirect($this->url->link('checkout/checkout'));
				$this->model_payment_mycelium->freeKeychainId($keychain_id);
				return;
			default:
				$this->response->redirect($this->url->link('checkout/checkout'));
				return;
		}

		// Progress the order status
		$this->model_checkout_order->addOrderHistory($opencart_order_id, $order_status_id);
	}
}
