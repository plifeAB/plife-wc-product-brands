<?php
if(! defined("ABSPATH"))
{
    exit;
}
class PlifeProductBrandsDbUpgrades {
    private $version;
    private $db_version;
    private $option_name ;
    public  function __construct()
    {
        $this->version = '1.0.0';
        $this->option_name = 'plife_product_brands_db_version';
        $this->db_version = get_option($this->option_name);
        if(empty($this->db_version) || version_compare($this->db_version,$this->version,"<"))
        {
            $this->upgrades();
        }
    }
    function upgrades()
    {
        if( version_compare($this->db_version,'1.0.0','<') ) {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table_name = $wpdb->prefix . 'plife_wc_product_brands';
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
		                id mediumint(9) NOT NULL AUTO_INCREMENT,
		                title text  NULL,
		                description text NOT NULL,
		                src text NOT NULL,
		                UNIQUE KEY id (id)
	                ) $charset_collate;";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $res = dbDelta( $sql );
            if($res) {
                $this->version_update();
            }
        }
    }
    function version_update()
    {
        if( empty( get_option($this->option_name,true) ) ) {
            add_option($this->option_name,$this->version);
        }  else {
            update_option($this->option_name,$this->version);
        }
    }
}
if(is_admin())
{
    //update_option('plife_product_brands_db_version',"0.0.0");
    new PlifeProductBrandsDbUpgrades();
}