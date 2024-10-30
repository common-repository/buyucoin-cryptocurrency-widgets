<?php
/**
 * This file is responsible for all database realted functionality.
 */
class bucw_database {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function __construct()
	{

		global $wpdb;

		$this->table_name = $wpdb->base_prefix . 'bucw_coins';
		$this->primary_key = 'id';
		$this->version = '1.0';

	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_columns()
	{
		return array(
			'id' => '%d',
			'currToName' => '%s',
			'quoteCurrency' => '%s',
			'baseCurrency' => '%s',
			'marketName' => '%s',
			'LBRate' =>'%f',
			'c24' => '%f',
			'c24p' => '%f',
			'h24' => '%f',
			'l24' => '%f',
			'v24' => '%f'
			
		);
	}

	function bucw_insert($coins_data){
		if(is_array($coins_data) && count($coins_data)>1){
		
		return $this->wp_insert_rows($coins_data,$this->table_name,true,'marketName');
		}
	} 
	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function get_column_defaults()
	{
		
		return array(
			'currToName' =>'',
			'marketName' => '',
			'baseCurrency' => '',
			'quoteCurrency' => '',
			'LBRate' => '',
			'c24' => '',
			'c24p' => '',
			'h24' => '',
			'l24' => '',
			'v24'=>'',		
			'last_updated' => date('Y-m-d H:i:s'),
		);
	}
	/**
	 *  A method for inserting multiple rows into the specified table
	 *  Updated to include the ability to Update existing rows by primary key
	 *  
	 *  Usage Example for insert: 
	 *
	 *  $insert_arrays = array();
	 *  foreach($assets as $asset) {
	 *  $time = current_time( 'mysql' );
	 *  $insert_arrays[] = array(
	 *  'type' => "multiple_row_insert",
	 *  'status' => 1,
	 *  'name'=>$asset,
	 *  'added_date' => $time,
	 *  'last_update' => $time);
	 *
	 *  }
	 *
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename);
	 *
	 *  Usage Example for update:
	 *
	 *  wp_insert_rows($insert_arrays, $wpdb->tablename, true, "primary_column");
	 *
	 *
	 * @param array $row_arrays
	 * @param string $wp_table_name
	 * @param boolean $update
	 * @param string $primary_key
	 * @return false|int
	 *
	 */
function wp_insert_rows($row_arrays , $wp_table_name, $update = false, $primary_key = null) {
	
	global $wpdb;
	$wp_table_name = esc_sql($wp_table_name);
	// Setup arrays for Actual Values, and Placeholders
	$values        = array();
	$place_holders = array();
	$query         = "";
	$query_columns = "";

	$floatCols=array( 'LBRate', 'c24', 'c24p', 'h24', 'l24','v24' );
	$query .= "INSERT INTO `{$wp_table_name}` (";
	foreach ($row_arrays as $count => $row_array) {
		foreach ($row_array as $key => $value) {
			if ($count == 0) {
				if ($query_columns) {
					$query_columns .= ", `" . $key . "`";
				} else {
					$query_columns .= "`" . $key . "`";
				}
			}
			
			$values[] = $value;
			
			$symbol = "%s";
			if (is_numeric($value)) {
						$symbol = "%d";
				}
		
			if(in_array( $key,$floatCols)){
				$symbol = "%f";
			}
			if (isset($place_holders[$count])) {
				$place_holders[$count] .= ", '$symbol'";
			} else {
				$place_holders[$count] = "( '$symbol'";
			}
		}
		// mind closing the GAP
		$place_holders[$count] .= ")";
	}
	
	$query .= " $query_columns ) VALUES ";
	
	$query .= implode(', ', $place_holders);
	
	if ($update) {
		$update = " ON DUPLICATE KEY UPDATE `$primary_key`=VALUES( `$primary_key` ),";
		$cnt    = 0;
		foreach ($row_arrays[0] as $key => $value) {
			if ($cnt == 0) {
				$update .= "`$key`=VALUES(`$key`)";
				$cnt = 1;
			} else {
				$update .= ", `$key`=VALUES(`$key`)";
			}
		}
		$query .= $update;
	}

	$sql = $wpdb->prepare($query, $values);
	
	if ($wpdb->query($sql)) {
		return true;
	} else {
		return false;
	}
}
public function bucw_get_all_coins_name(){
	global $wpdb;
	$coins=array();
	$results=array();
	$cache = get_transient('bucw_coins_id');
		if (false === $cache) {
			$coin_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table_name WHERE `quoteCurrency` LIKE '%INR%' ORDER BY `wp_bucw_coins`.`LBRate` DESC "));
		if (is_array($coin_data) && isset($coin_data)) {
        	foreach ($coin_data as $key=>$coin) {
				$coins['currency_name'] = isset($coin_data[$key]->currToName)?$coin_data[$key]->currToName:'';
				$coins['market_name'] = isset($coin_data[$key]->marketName)?$coin_data[$key]->baseCurrency.'-'.$coin_data[$key]->quoteCurrency:'';
				$coins['baseCurrency'] = isset($coin_data[$key]->baseCurrency)?$coin_data[$key]->baseCurrency:'';
				$coins['marketName'] = isset($coin_data[$key]->marketName)?$coin_data[$key]->marketName:'';
				$results[]=$coins;
				set_transient('bucw_coins_id', $results, 24 * HOUR_IN_SECONDS);
			}
			return $results;
		}else{
			return false;
		} 
	}else {
		return $cache;
	}
}
	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	 */
	public function create_table()
	{

		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $this->table_name . " (
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

		update_option($this->table_name . '_db_version', $this->version);
	}

	/**
	 * Drop database table
	 */
	public function drop_table(){
		global $wpdb;

		$wpdb->query("DROP TABLE IF EXISTS " . $this->table_name);

	}
}