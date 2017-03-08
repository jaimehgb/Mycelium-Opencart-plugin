<?php
/**
 * Mycelium Payment Model
 */
class ModelPaymentMycelium extends Model {

	/** @var Mycelium Class $mycelium */
	private $mycelium;

	/**
	 * Mycelium Payment Model Construct
	 * @param Registry $registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->language('payment/mycelium');
		$this->mycelium = new Mycelium($registry);
	}

	/**
	 * Returns the Mycelium Payment Method if available
	 * @param  array $address Customer billing address
	 * @return array|void Mycelium Payment Method if available
	 */
	public function getMethod($address)	{

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('cod_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		// All Geo Zones configured or address is in configured Geo Zone
		if (!$this->config->get('mycelium_geo_zone_id') || $query->num_rows) {
			if($this->mycelium->setting('shifty_enabled'))
				$title = $this->language->get('text_title_alts');
			else
				$title = $this->language->get('text_title');
			return array(
				'code'	   => 'mycelium',
				'title'	  => $title,
				'terms'	  => '',
				'sort_order' => $this->config->get('cod_sort_order')
			);
		}
	}
	
	public function getFreeKeychainId()
	{
		// unlock all addresses used more time ago than set in the admin panel
		$time_unlockable = time() - $this->mycelium->setting('address_reuse_time');
		$this->db->query("UPDATE `" . DB_PREFIX . "mycelium_keychains` SET `locked` = 0 WHERE `locked` = 1 AND `last_used` < '$time_unlockable';");
		
		// retrieve the smallest keychain from among the available, update it first to make sure we are the only one to extract that address
		$code = md5(rand(0,10000));
		$time = time();
		$this->db->query("UPDATE `" . DB_PREFIX . "mycelium_keychains` SET `locked` = 1, `extract_code` = '$code', `last_used` = '$time' WHERE `locked` = 0 LIMIT 1;");
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "mycelium_keychains` WHERE `extract_code` = '$code' LIMIT 1");
		
		if($result->num_rows > 0)
		{
			return $result->row['keychain_id'];
		}
		else
		{
			// no free addresses found, generate new one
			$this->db->query("INSERT INTO `" . DB_PREFIX . "mycelium_keychains` (`locked`, `last_used`) VALUES (1, '$time');");
			$id = $this->db->getLastId();
			$this->mycelium->log('info', "New address generated: " . $id);
			return $id;
		}
	}
	
	public function freeKeychainId($id)
	{
		// callback received, so payment made, so lets make this id available :D
		if(is_numeric($id) && $id > 0)
		{
			$this->db->query("UPDATE `" . DB_PREFIX . "mycelium_keychains` SET `locked` = 0 WHERE `keychain_id` = $id LIMIT 1;");
		}
	}
	
}
