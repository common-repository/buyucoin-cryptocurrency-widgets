<?php
/*
 Plugin Name:BuyUcoin Cryptocurrency Widgets
 Plugin URI:https://cryptocurrencyplugins.com/
 Description:BuyUcoin cryptocurrency widgets to show bitcoin, ethereum and other crypto coins live prices inside your website using BuyUcoin exchange API.
 Version:1.0.2
 Requires at least: 4.5
 Tested up to:5.8.1
 Requires PHP:5.6
 Stable tag:1.0.2
 License:GPL2
 Author:Cool Plugins
 Author URI:https://coolplugins.net/
 License URI:https://www.gnu.org/licenses/gpl-2.0.html
 Domain Path:/languages
 Text Domain:BUCW
*/
if ( !defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}
if (!defined('BUCW_VERSION')){
	define('BUCW_VERSION', '1.0.2');
}
/*** Defined constent for later use */
define('BUCW_FILE', __FILE__ );

if (!defined('BUCW_PLUGIN_URL')){
define('BUCW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if (!defined('BUCW_PLUGIN_DIR')){
define('BUCW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if (!class_exists('BuyUcoin_Widgets'))
 {
final class BuyUcoin_Widgets 
{
	/**
	 * The unique instance of the plugin.
	 *
	 */
	private static $instance;
	/**
	 * Gets an instance of our plugin.
	 *
	 */
	public static function get_instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Constructor.
	 */
	 private function __construct()
	{
	}
	// register all hooks
	public function registers() 
	{
		$thisPlugin=self::$instance;
		/*** Installation and uninstallation hooks */
		register_activation_hook(__FILE__, array('BuyUcoin_Widgets', 'bucw_activate'));
		register_deactivation_hook(__FILE__, array('BuyUcoin_Widgets', 'bucw_deactivate'));
		/*** Load required files */
		add_action( 'plugins_loaded',array($thisPlugin,'BUCW_load_files'));
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(self::$instance,'BUCW_template_settings_page'));
		if(is_admin()){
			add_action('init','bucw_fetch_currency_name');
			add_action('updated_post_meta', array($this,'bucw_delete_tarnsient'), 10, 4);
			// $this->bucw_installation_date();

		}
	}
	/* function bucw_installation_date(){
		 $get_installation_time = strtotime("now");
   	 	  add_option('bucw_activation_time', $get_installation_time ); 
	    }
		 */
	function bucw_delete_tarnsient($meta_id, $post_id, $meta_key = '', $meta_value = '')
	{
		$cache_name = 'bucw_coins_name-'.$post_id;
		if ($meta_key == '_edit_lock') {
		delete_transient($cache_name );
		}
	}


	/*** Load required files */
	public function BUCW_load_files() {
		load_plugin_textdomain('BUCW', false, basename(dirname(__FILE__)) . '/languages/');
        require_once BUCW_PLUGIN_DIR . '/includes/bucw-post-type.php';
		new BUCW_Posttype();
        require_once BUCW_PLUGIN_DIR . '/includes/bucw-db-helper.php';
        require_once BUCW_PLUGIN_DIR . 'includes/bucw-function.php';
        require_once BUCW_PLUGIN_DIR . 'includes/bucw-shortcode.php';
	//	require_once BUCW_PLUGIN_DIR . 'admin/class.review-notice.php';
        new BuyUcoin_Widgets_Shortcode();
		if( is_admin() ){
            if ( bucw_get_post_type_page() == "bucw") {
				require_once BUCW_PLUGIN_DIR . 'admin/cmb2/init.php';
                require_once BUCW_PLUGIN_DIR . 'admin/cmb2/cmb2-conditionals.php';
                require_once BUCW_PLUGIN_DIR . 'admin/cmb2/cmb-field-select2/cmb-field-select2.php';
            }
		}
	}
	/*** Add links in plugin install list */
		public function BUCW_template_settings_page($links){
			$links[] = '<a style="font-weight:bold" href="'. esc_url( get_admin_url(null, 'edit.php?post_type=bucw') ) .'">Shortcodes Settings</a>';
			return $links;
		}
		/*
			On activation save some settings for later use
		*/
		public static function bucw_activate() {
			$thisPlugin=self::$instance;
			update_option("BUCW-v",BUCW_VERSION);
			update_option("BUCW-type","Free");
			update_option("BUCW-installDate",date('Y-m-d h:i:s') );
			update_option("BUCW-already-rated","no");
			update_option('BUCW_do_activation_redirBUCW', true);
           	$thisPlugin->bucw_create_table();
		}
		public static function bucw_deactivate(){
			$db = new bucw_database();
			$db->drop_table();
			delete_option("BUCW-v");
			delete_option("BUCW-type");
			delete_option("BUCW-installDate");
			delete_option("BUCW-already-rated");
			delete_transient('bucw_allcoins_data');
		}
		
/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function bucw_create_table()
	{

		global $wpdb;
       

		$table_name = $wpdb->base_prefix . 'bucw_coins';
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $table_name . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`currToName` varchar(200) NOT NULL,
			`marketName` varchar(200) NOT NULL UNIQUE,
			`baseCurrency` varchar(250) NOT NULL,
			`quoteCurrency` varchar(100) NOT NULL,
			`LBRate` decimal(20,6),
			`c24`  decimal(20,6),
			`c24p` decimal(20,6),
			`h24` decimal(24,2),
			`l24` decimal(24,2),
			`v24` decimal(24,2),
			`last_updated` TIMESTAMP NOT NULL DEFAULT NOW() ON UPDATE NOW(),
			PRIMARY KEY (id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta($sql);

		
	}
} //class end here
}  
/*** THANKS - CoolPlugins.net  */
$BUCW=BuyUcoin_Widgets::get_instance();
$BUCW->registers();

 	
