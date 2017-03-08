<?php
class ModelPaymentMycelium extends Model {
    public function install()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "mycelium_keychains` (
                `keychain_id` int(11) NOT NULL AUTO_INCREMENT,
                `locked` int(1) NOT NULL DEFAULT 0,
                `last_used` int(11) UNSIGNED,
                `extract_code` VARCHAR(33) NOT NULL,
                PRIMARY KEY (`keychain_id`)
            ) ENGINE=InnoDB;
        ");
        
        // make available 20 addresses
        for($i=1; $i<=20; $i++)
        {
            //$this->db->query("INSERT INTO `" . DB_PREFIX . "mycelium_keychains` (`locked`) VALUES (0);");
        }
    }
    
    public function uninstall()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "mycelium_keychains`;");
    }
}