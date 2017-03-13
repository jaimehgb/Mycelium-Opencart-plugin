<?php
/**
 * Mycelium Payment Admin Controller
 */
class ControllerPaymentMycelium extends Controller {

	/** @var array $error Validation errors */
	private $error = array();

	/** @var MyceliumLibrary $mycelium */
	private $mycelium;

	/**
	 * Mycelium Payment Admin Controller Constructor
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);

		// Make langauge strings and Mycelium Library available to all
		$this->load->language('payment/mycelium');

		$this->mycelium = new Mycelium($registry);

		// Setup logging
		$this->logger = new Log('mycelium.log');
		
	}

	/**
	 * Primary settings page
	 * @return void
	 */
	public function index() {
		// Saving settings
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['action'] === 'save' && $this->validate())
		{
			
			// encrypt secret :P
			$this->setting('gateway_secret', $this->mycelium->encryption->encrypt(serialize($this->request->post['mycelium_gateway_secret'])));
			
			$this->setting('gateway_name', $this->request->post['mycelium_gateway_name']);
			$this->setting('gateway_id', $this->request->post['mycelium_gateway_id']);
			
			// uncomment this once we successfully authenticate to mycelium admin endpoint
			/*
			$this->setting('confirmations', $this->request->post['mycelium_confirmations']);
			$this->setting('gateway_xpub', $this->request->post['mycelium_gateway_xpub']);
			*/

			$this->setting('expiration_period', $this->request->post['mycelium_expiration_period']);
			$this->setting('sort_order', $this->request->post['mycelium_sort_order']);
			$this->setting('geo_zone_id', $this->request->post['mycelium_geo_zone_id']);
			$this->setting('status', $this->request->post['mycelium_status']);
			$this->setting('paid_status', $this->request->post['mycelium_paid_status']);
			$this->setting('complete_status', $this->request->post['mycelium_complete_status']);
			$this->setting('shifty_enabled', $this->request->post['mycelium_shifty_enabled']);
			$this->setting('address_reuse_time', abs($this->request->post['mycelium_reuse_time']));
			
			$this->setting('back_url', $this->request->post['mycelium_back_url']);
			$this->setting('callback_url', $this->request->post['mycelium_callback_url']);
			$this->setting('return_url', $this->request->post['mycelium_return_url']);
			$this->setting('debug', $this->request->post['mycelium_debug']);
			

			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		else if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->request->post['action'] == 'import')
		{
			// import settings from mycelium, check if gateway id is set
			// TODO
		}

		$this->document->setTitle($this->language->get('heading_title'));
		
		
		// load from language file
		$data['entry_gateway_name'] = $this->language->get('entry_gateway_name');
		$data['entry_gateway_id'] = $this->language->get('entry_gateway_id');
		$data['entry_gateway_secret'] = $this->language->get('entry_gateway_secret'); 
		$data['entry_confirmations'] = $this->language->get('entry_confirmations');
		$data['entry_xpub'] = $this->language->get('entry_xpub');
		$data['entry_expiration_period'] = $this->language->get('entry_expiration_period');
		$data['entry_callback_url'] = $this->language->get('entry_callback_url');
		$data['entry_return_url'] = $this->language->get('entry_return_url');
		$data['entry_back_url'] = $this->language->get('entry_back_url');
		$data['entry_shifty'] = $this->language->get('entry_shifty');
		$data['entry_reuse_time'] = $this->language->get('entry_reuse_time');
		
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_paid_status'] = $this->language->get('entry_paid_status');
		$data['entry_confirmed_status'] = $this->language->get('entry_confirmed_status');
		$data['entry_complete_status'] = $this->language->get('entry_complete_status');
		$data['entry_debug'] = $this->language->get('entry_debug');
		
		
		// #HEADER and globals
		$data['heading_title'] = $this->language->get('heading_title');
		

		$data['text_general'] = $this->language->get('text_general');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_statuses'] = $this->language->get('text_statuses');
		$data['text_advanced'] = $this->language->get('text_advanced');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_are_you_sure'] = $this->language->get('text_are_you_sure');

		$data['help_gateway_name'] = $this->language->get('help_gateway_name');
		$data['help_gateway_id'] = $this->language->get('help_gateway_id');
		$data['help_gateway_secret'] = $this->language->get('help_gateway_secret');
		$data['help_expiration_period'] = $this->language->get('help_expiration_period');
		$data['help_confirmations'] = $this->language->get('help_confirmations');
		$data['help_xpub'] = $this->language->get('help_xpub');
		$data['help_paid_status'] = $this->language->get('help_paid_status');
		$data['help_confirmed_status'] = $this->language->get('help_confirmed_status');
		$data['help_complete_status'] = $this->language->get('help_complete_status');
		$data['help_callback_url'] = $this->language->get('help_callback_url');
		$data['help_return_url'] = $this->language->get('help_return_url');
		$data['help_back_url'] = $this->language->get('help_back_url');
		$data['help_debug'] = $this->language->get('help_debug');
		$data['help_shifty'] = $this->language->get('help_shifty');
		$data['help_reuse_time'] = $this->language->get('help_reuse_time');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_copy_from'] = $this->language->get('button_copy_from');
		$data['button_clear'] = $this->language->get('button_clear');
		$data['button_continue'] = $this->language->get('button_continue');

		$data['tab_settings'] = $this->language->get('tab_settings');
		$data['tab_log'] = $this->language->get('tab_log');

		$data['url_action'] = $this->url->link('payment/mycelium', 'token=' . $this->session->data['token'], 'SSL');
		$data['url_import'] = $this->url->link('payment/mycelium', 'token=' . $this->session->data['token'], 'SSL');
		$data['url_cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		$data['url_reset'] = $this->url->link('payment/mycelium/reset', 'token=' . $this->session->data['token'], 'SSL');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/mycelium', 'token=' . $this->session->data['token'], 'SSL')
		);


		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		
		
		// load from settings
		$data['mycelium_gateway_name'] = (isset($this->request->post['mycelium_gateway_name'])) ? $this->request->post['mycelium_gateway_name'] : $this->setting('gateway_name');
		$data['mycelium_gateway_id'] = (isset($this->request->post['mycelium_gateway_id'])) ? $this->request->post['mycelium_gateway_id'] : $this->setting('gateway_id');
		// extract secret and decrypt
		$data['mycelium_gateway_secret'] = (isset($this->request->post['mycelium_gateway_secret'])) ? $this->request->post['mycelium_gateway_secret'] : @unserialize($this->mycelium->encryption->decrypt($this->setting('gateway_secret')));
		$data['mycelium_confirmations'] = (isset($this->request->post['mycelium_confirmations'])) ? $this->request->post['mycelium_confirmations'] : $this->setting('confirmations');
		$data['mycelium_gateway_xpub'] = (isset($this->request->post['mycelium_gateway_xpub'])) ? $this->request->post['mycelium_gateway_xpub'] : $this->setting('gateway_xpub');
		$data['mycelium_expiration_period'] = (isset($this->request->post['mycelium_expiration_period'])) ? $this->request->post['mycelium_expiration_period'] : $this->setting('expiration_period');
		$data['mycelium_shifty_enabled'] = (isset($this->request->post['mycelium_shifty_enabled'])) ? $this->request->post['mycelium_shifty_enabled']  :$this->setting('shifty_enabled');
		$data['mycelium_sort_order'] = (isset($this->request->post['mycelium_sort_order'])) ? $this->request->post['mycelium_sort_order'] : $this->setting('sort_order');
		$data['mycelium_geo_zone_id'] = (isset($this->request->post['mycelium_geo_zone_id'])) ? $this->request->post['mycelium_geo_zone_id'] : $this->setting('geo_zone_id');
		$data['mycelium_status'] = (isset($this->request->post['mycelium_status'])) ? $this->request->post['mycelium_status'] : $this->setting('status');
		$data['mycelium_reuse_time'] = (isset($this->request->post['mycelium_reuse_time'])) ? $this->request->post['mycelium_reuse_time'] : $this->setting('address_reuse_time');
		
		// #ORDER STATUSES
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		$data['mycelium_paid_status'] = (isset($this->request->post['mycelium_paid_status'])) ? $this->request->post['mycelium_paid_status'] : $this->setting('paid_status');
		$data['mycelium_complete_status'] = (isset($this->request->post['mycelium_complete_status'])) ? $this->request->post['mycelium_complete_status'] : $this->setting('complete_status');

		// #ADVANCED
		$data['mycelium_callback_url'] = (isset($this->request->post['mycelium_callback_url'])) ? $this->request->post['mycelium_callback_url'] : $this->setting('callback_url');
		$data['mycelium_return_url'] = (isset($this->request->post['mycelium_return_url'])) ? $this->request->post['mycelium_return_url'] : $this->setting('return_url');
		$data['mycelium_back_url'] = (isset($this->request->post['mycelium_back_url'])) ? $this->request->post['mycelium_back_url'] : $this->setting('back_url');
		
		$default_notify_url = $this->url->link('payment/mycelium/callback', $this->config->get('config_secure'));
		$default_notify_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_notify_url);
		$default_notify_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_notify_url);
		$data['default_notify_url'] = $default_notify_url;

		$default_return_url = $this->url->link('payment/mycelium/success', $this->config->get('config_secure'));
		$default_return_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_return_url);
		$default_return_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_return_url);
		$data['default_return_url'] = $default_return_url;
		
		$default_back_url = $this->url->link('payment/mycelium/cancel', $this->config->get('config_secure'));
		$default_back_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_back_url);
		$default_back_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_back_url);
		$data['default_back_url'] = $default_back_url;

		$data['mycelium_debug'] = (isset($this->request->post['mycelium_debug'])) ? $this->request->post['mycelium_debug'] : $this->setting('debug');

		// #LOG
		$file = DIR_LOGS . 'mycelium.log';
		$data['log'] = '';

		$matches_array = array();

		if (file_exists($file)) {
			$lines = file($file, FILE_USE_INCLUDE_PATH, null);
			foreach ($lines as $line_num => $line) {
				if (preg_match('/^([^\\[]*)\\[([^\\]]*)\\](\\{([^}]*)\\})?(.*)/', $line, $matches)) {
					unset($matches[3]);
					$level = strtolower($matches[2]);
					$matches[0] = '';
					$matches[1] = '<span class="ml-log-date">' . $matches[1] . '</span>';
					$matches[2] = '<span class="ml-log-level">[<span>' . $matches[2] . '</span>]</span>';
					if (!empty($matches[4])) {
						$matches[4] = '<span class="ml-log-locale">{<span>' . $matches[4] . '</span>}</span>';
						$matches[4] = preg_replace('/((->)|(::))/', '<span>$1</span>', $matches[4]);
					}
					$matches[5] = '<span class="ml-log-message">' . $matches[5] . '</span>';
					$line = '<span class="ml-log ml-log-' . $level . '">' . implode('', $matches) . '</span>';
				}

				$data['log'] .= '<div>' . $line . "</div>\n";
			}
		}

		$data['url_clear'] = $this->url->link('payment/mycelium/clear', 'token=' . $this->session->data['token'], 'SSL');


		// #LAYOUT
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		// #NOTIFICATIONS
		$data['error_warning'] = '';
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		$data['success'] = '';
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$data['error_notify_url'] = '';
		if (isset($this->error['notify_url'])) {
			$data['error_notify_url'] = $this->error['notify_url'];
		}

		$data['error_return_url'] = '';
		if (isset($this->error['return_url'])) {
			$data['error_return_url'] = $this->error['return_url'];
		}
		
		$data['error_back_url'] = '';
		if (isset($this->error['back_url'])) {
			$data['error_back_url'] = $this->error['back_url'];
		}

		$data['error_request'] = false;
		if (isset($this->error['request'])) {
			$data['error_request'] = true;
		}
		
		$data['error_status'] = false;
		if(isset($this->error['status'])) {
			$data['error_status'] = $this->error['status'];
		}

		$this->response->setOutput($this->load->view('payment/mycelium.tpl', $data));
	}


	/**
	 * Clears the mycelium log
	 * @return void
	 */
	public function clear() {
		$file = DIR_LOGS . 'mycelium.log';
		$handle = fopen($file, 'w+');
		fclose($handle);

		$this->session->data['success'] = $this->language->get('success_clear');
		$this->response->redirect($this->url->link('payment/mycelium', 'token=' . $this->session->data['token'], 'SSL'));
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
	private function log($level, $message, $depth = 0) {
		$depth += 1;
		$this->mycelium->log($level, $message, $depth);
	}

	/**
	 * Convenience wrapper for mycelium settings
	 *
	 * Automatically persists to database on set and combines getting and setting into one method
	 * Assumes mycelium_ prefix
	 *
	 * @param string $key Setting key
	 * @param string $value Setting value if setting the value
	 * @return string|null|void Setting value, or void if setting the value
	 */
	private function setting($key, $value = null) {
		// Set the setting
		if (func_num_args() === 2) {

			return $this->mycelium->setting($key, $value);
		}

		// Get the setting
		return $this->mycelium->setting($key);
	}

	/**
	 * Validate the primary settings for the Mycelium extension
	 * @return boolean True if the settings provided are valid
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/mycelium')) {
			$this->error['warning'] = $this->language->get('warning_permission');
		}
		if (!empty($this->request->post['mycelium_notify_url']) && false === filter_var($this->request->post['mycelium_callback_url'], FILTER_VALIDATE_URL)) {
			$this->error['notify_url'] = $this->language->get('error_notify_url');
		}
		if (!empty($this->request->post['mycelium_return_url']) && false === filter_var($this->request->post['mycelium_return_url'], FILTER_VALIDATE_URL)) {
			$this->error['return_url'] = $this->language->get('error_return_url');
		}
		if (!empty($this->request->post['mycelium_back_url']) && false === filter_var($this->request->post['mycelium_back_url'], FILTER_VALIDATE_URL)) {
			$this->error['back_url'] = $this->language->get('error_back_url');
		}
		if ($this->request->post['mycelium_status']) {
			// check if there is enough data to make it live
			if(
				!$this->request->post['mycelium_gateway_secret'] ||
				!$this->setting('gateway_secret') ||
				!$this->request->post['mycelium_gateway_id'] ||
				!$this->setting('gateway_id')
			)
				$this->error['status'] = $this->language->get('error_status');
		}

		return !$this->error;
	}


	/**
	 * Install the extension by setting up some smart defaults
	 * @return void
	 */
	public function install() {
		$this->load->model('localisation/order_status');
		$this->load->model('payment/mycelium');
		$this->model_payment_mycelium->install();
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
		$default_paid = null;
		$default_confirmed = null;
		$default_complete= null;
		foreach ($order_statuses as $order_status) {
			if ($order_status['name'] == 'Processing') {
				$default_paid = $order_status['order_status_id'];
			} elseif ($order_status['name'] == 'Processed') {
				$default_confirmed = $order_status['order_status_id'];
			} elseif ($order_status['name'] == 'Complete') {
				$default_complete = $order_status['order_status_id'];
			}
		}
		// default urls
		$default_notify_url = $this->url->link('payment/mycelium/callback', $this->config->get('config_secure'));
		$default_notify_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_notify_url);
		$default_notify_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_notify_url);

		$default_return_url = $this->url->link('payment/mycelium/success', $this->config->get('config_secure'));
		$default_return_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_return_url);
		$default_return_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_return_url);
		
		$default_back_url = $this->url->link('payment/mycelium/cancel', $this->config->get('config_secure'));
		$default_back_url = str_replace(HTTP_SERVER, HTTP_CATALOG, $default_back_url);
		$default_back_url = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $default_back_url);
		
		$this->load->model('setting/setting');
		$default_settings = array(
			'mycelium_gateway_name' => null,
			'mycelium_gateway_secret' => null,
			/*'mycelium_developer_key' => null,*/
			'mycelium_gateway_id' => null,
			/*'mycelium_gateway_xpub' => null,
			'mycelium_confirmations' => '1',
			'mycelium_default_currency' => null,*/
			'mycelium_expiration_period' => '900',
			'mycelium_callback_url' => $default_notify_url,
			'mycelium_return_url' => $default_return_url,
			'mycelium_back_url' => $default_back_url,
			'mycelium_shifty_enabled' => '0',
			'mycelium_address_reuse_time' => '1800',
			/*'mycelium_auto_redirect' => 'false',*/
			/*'mycelium_test_pubkey' => null,
			'mycelium_test_mode' => 'false',
			'mycelium_convert_to' => 'BTC',
			'mycelium_email_notifications' => 'false',*/
			'mycelium_sort_order' => null,
			'mycelium_geo_zone_id' => '0',
			'mycelium_status' => '0',
			'mycelium_paid_status' => $default_paid,
			'mycelium_confirmed_status' => $default_confirmed,
			'mycelium_complete_status' => $default_complete,
			'mycelium_valid_settings' => 'false',
			'mycelium_debug' => '0',
		);
		$this->model_setting_setting->editSetting('mycelium', $default_settings);
	}

	/**
	 * Uninstall the extension by removing the settings
	 * @return void
	 */
	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('mycelium');
		$this->load->model('payment/mycelium');
		$this->model_payment_mycelium->uninstall();
	}
}
