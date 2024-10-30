<?php

class BuyUcoin_Widgets_Shortcode
{
    /**
     * @var array
     */
    private $options;
    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
        $this->registers();
    }
    /**
     * Register all hooks 
     *
     */
    public  function registers()
    {
        require_once BUCW_PLUGIN_DIR . 'includes/bucw-function.php';
        $thisPlugin = $this;
     	/*** ECT main shortcode */
	    add_shortcode('bucw', array(  $thisPlugin,'bucw_shortcodes'));
        add_action( 'wp_ajax_bucw_get_ticker_data', array($this,'bucw_get_ticker_data'));
        add_action( 'wp_ajax_nopriv_bucw_get_ticker_data', array($this,'bucw_get_ticker_data'));
        add_action( 'wp_footer', array($this,'bucw_ticker_in_footer'));
        add_action('admin_enqueue_scripts', array($this, 'bucw_admin_script'));

        
       
    }

        /*
|--------------------------------------------------------------------------
| Added ticker shortcode in footer hook for footer ticker
|--------------------------------------------------------------------------
*/ 
	function bucw_ticker_in_footer(){
       
		if (!wp_script_is('jquery', 'done')) {
			wp_enqueue_script('jquery');
		}
		 $id=get_option('bucw-p-id');
        
		if($id){
				$ticker_position = get_post_meta($id,'bucw_ticker_position', true );
              
    			$type = get_post_meta($id,'type', true );
                if($type=="ticker"){
    			if($ticker_position=="header"||$ticker_position=="footer"){
					 $shortcode=get_option('bucw-shortcode');
                    echo do_shortcode(wp_kses_post($shortcode));
				 }
				}
			}	
	}
 
    public function bucw_get_ticker_data(){
        $output='';
        $coin_html='';
        $nonce = isset($_POST['nonce']) ?sanitize_text_field($_POST['nonce']) : "";

         if ( !wp_verify_nonce($nonce, 'bucw_crypto_widget')) {
          die("*ok*");
         }
        $data=isset($_POST['data'])?filter_var_array($_POST['data'],FILTER_SANITIZE_STRING):'';
        if(!empty($data)){
            $req_data =isset($_POST['data'])?filter_var_array($_POST['data'],FILTER_SANITIZE_STRING):'';
            $select_coin = isset($req_data['select_coin'])?$req_data['select_coin']:'';
            $post_id = isset($req_data['post_id']) ? $req_data['post_id'] : '';
            $widget_type=isset($req_data['widget_type']) ? $req_data['widget_type'] : '';
            $mb_format = isset($req_data['mb_format']) ? $req_data['mb_format'] : '';
            bucw_fetch_currency_name();
            $ticker_type = isset($req_data['ticker_type'])?$req_data['ticker_type']:'';
            $all_coin_data = bucw_get_selected_coins_info($select_coin,$post_id);           
            $widget_type = $req_data['widget_type'];
        if(isset($all_coin_data) && is_array($all_coin_data)){
            foreach($all_coin_data as $key=>$value){
                $coin_price = $value['LBRate'];
                $market_name = $value['baseCurrency'].'-'.$value['quoteCurrency'];
                $percent_change_24h = bucw_format_number($value['c24p']);
                $coin_id = $value['baseCurrency'];
                $coin_name = ucwords(strtolower($value['currency_name']));
                $h24 =($mb_format=="on")?bucw_mb_format($value['h24']): bucw_format_number($value['h24']);
                $l24 = ($mb_format=="on")?bucw_mb_format($value['l24']):bucw_format_number($value['l24']);
                $v24 = $value['v24'];
                $c24  =bucw_format_number($value['c24']);
                $fiat_currency = substr($value['market_name'], 0, 3); 
               $coin_price_html=  bucw_currency_symbol($fiat_currency).bucw_format_number($coin_price);
               $display_changes = '';
                $change_class = "up";
                $change_sign_minus = "-";
                $coin_svg = BUCW_PLUGIN_URL . '/assets/buyucoin-icons/' . strtolower($coin_id) . '.svg';
                $coin_logo_html = '<img id="' . esc_attr($coin_id) . '" alt="' . esc_attr($coin_id) . '" src="' . esc_url($coin_svg) . '">';
                $change_sign = '<i class="bucw_icon-up" aria-hidden="true"></i>';
                if (strpos($percent_change_24h, $change_sign_minus) !== false) {
                    $change_sign = '<i class="bucw_icon-down" aria-hidden="true"></i>';
                    $change_class = "down";
                }
                require BUCW_PLUGIN_DIR . 'layouts/'.$widget_type.'/'.sanitize_file_name($widget_type.'.php');
            }
            }
            
            $output .=$coin_html; 
            
            echo wp_kses_post($output);
            wp_die();
        }
    }
    /*** ECT main shortcode */
    public function bucw_shortcodes($atts) {
        $atts = shortcode_atts( array(
            'id'  => '',
        ), $atts, 'bucew' );
        $post_id= (int)$atts['id'];
       /*
        *	Return if post status is anything other than 'publish'
        */
        if( get_post_status( $post_id ) != "publish" ){
            return;
        }   
        $widget_type = sanitize_text_field(get_post_meta($post_id,'type',true));
        $ticker_style = sanitize_text_field(get_post_meta($post_id,'styles',true));
        $select_coins = get_post_meta($post_id,'select-coin',true);
        $select_coin = get_post_meta($post_id,'select-coins',true);
        $ticker_speed = sanitize_text_field(get_post_meta($post_id,'ticker_speed',true ));
        $ticker_height = sanitize_text_field(get_post_meta($post_id,'ticker_height',true));
        $height = get_post_meta($post_id,'ticker_height',true);
        $font_size = get_post_meta($post_id,'font_size',true);
        $mb_format=!empty(get_post_meta($post_id,'display_format',true))?get_post_meta($post_id,'display_format',true):"";      
        $height = !empty($height)? $height:"120";
        $font_family = get_post_meta($post_id,'bucw-font',true);
        $back_color = get_post_meta($post_id,'back_color', true );
        $font_color = get_post_meta($post_id,'font_color', true );
        $bg_color=!empty($back_color)? "background-color:".sanitize_hex_color($back_color).";":"background-color:#fff;";
        $font_sizes=!empty($font_size)? "font-size:".sanitize_text_field($font_size)."px !important;":"font-size:14px;";
        $font_familys=!empty($font_family)? "font-family:".sanitize_text_field($font_family).";":"font-family: inherit;";
        $bg_coloronly=!empty($back_color)? ":".$back_color."d9;":":#ddd;";
        $ticker_height = !empty($height)? "height:".sanitize_text_field($height)."px !important;":"height:initial";
        $fnt_color=!empty($font_color)? "color:".sanitize_hex_color($font_color).";":"color:#000;";
        $custom_css = get_post_meta($post_id,'custom_css', true );
        $per_up_color = get_post_meta($post_id,'display_up_color',true);
        $per_low_color = get_post_meta($post_id,'display_low_color',true);
        $up_color = !empty($per_up_color)?"color:".sanitize_hex_color($per_up_color).";":"color:#006400;";
        $down_color= !empty($per_low_color)?"color:".sanitize_hex_color($per_low_color).";":"color:#FF0000;";
        $custom_font = get_post_meta($post_id,'add_font_family',true);
        $thisPlugin = $this;
        $ticker_position = get_post_meta($post_id,'bucw_ticker_position',true);
        $header_ticker_position = get_post_meta($post_id,'bucw_header_ticker_position', true );
        $ticker_top=!empty($header_ticker_position)? "top:".sanitize_text_field($header_ticker_position)."px !important;":"top:0px !important;";
        $this->bucw_enqueue_assets($widget_type, $post_id,$font_family);
        $output='';
        if($widget_type=='card'){
            $select_coins = $select_coin;
        }
        $all_coin_data = bucw_get_selected_coins_info($select_coins,$post_id);
        $settings['widget_type']=$widget_type;
        $settings['select_coin']=$select_coins ;
        $settings['mb_format']=$mb_format ;
        $display_changes = get_post_meta($post_id,'display_changes', true );
        $settings['ticker_speed']=$ticker_speed;
        $settings['ticker_height']=$ticker_height;
        $settings['post_id']=$post_id;	
        $dynamic_styles = '';
        $nonce = wp_create_nonce('bucw_crypto_widget');

            $settings_json=json_encode($settings);
            
            // if($widget_type=='list'){
                $dynamic_styles.="#bucw-table-widget-".esc_attr($post_id).".coins-table{".esc_attr($bg_color)."}
    #bucw-table-widget-".esc_attr($post_id).".coins-table thead tr th,
    #bucw-table-widget-".esc_attr($post_id).".coins-table tbody tr td{".esc_attr($bg_color)." ".esc_attr($fnt_color)." ".esc_attr($font_sizes)."". esc_attr($font_familys)."}
    #bucw-table-widget-".esc_attr($post_id).".coins-table tbody tr td .up{".esc_attr($up_color)."}
    #bucw-table-widget-".esc_attr($post_id).".coins-table tbody tr td .down{".esc_attr($down_color)."}
    .bucw_no_data{display:none}";
            // }elseif($widget_type=="card"){
                $dynamic_styles.="#bucw-card-widget-".esc_attr($post_id)." .bucw-main-wrapper{".esc_attr($bg_color)."}
    #bucw-card-widget-".esc_attr($post_id)." .bucw-coinname-wrapper, 
    #bucw-card-widget-".esc_attr($post_id)." .bucw-coin-price,
    #bucw-card-widget-".esc_attr($post_id)." .coins-value{".esc_attr($fnt_color)."".esc_attr($font_sizes)."". esc_attr($font_familys)."}
    #bucw-card-widget-".esc_attr($post_id)." .bucw-coins-changes .up,
    #bucw-card-widget-".esc_attr($post_id)." .bucw-coinname-wrapper .up{".esc_attr($up_color)."}
    #bucw-card-widget-".esc_attr($post_id)." .bucw-coins-changes .down,
    #bucw-card-widget-".esc_attr($post_id)." .bucw-coinname-wrapper .down{".esc_attr($down_color)."}
    .bucw_no_data{display:none}
    ";
    
            // }else{
                $dynamic_styles.="#bucw-ticker-widget-".esc_attr($post_id)."{".esc_attr($bg_color)."}
                .ph-item{".esc_attr($bg_color)."}
                .ph-item{".esc_attr($bg_color)."}
        .ph-item{".esc_attr($ticker_height)."}
        .ph-row .big{background-color:".esc_attr($font_color).";}
        #bucw-ticker-widget-".esc_attr($post_id)." .bucw-coin-changes.up {".esc_attr($up_color)."}
        #bucw-ticker-widget-".esc_attr($post_id)." .bucw-coin-changes.down{".esc_attr($down_color)."}
        #bucw-ticker-widget-".esc_attr($post_id)." span.bucw-coin-name,#bucw-ticker-widget-".esc_attr($post_id)." span.bucw_quatcls,
        #bucw-ticker-widget-".esc_attr($post_id)." span.bucw-coin-price, 
        #bucw-ticker-widget-".esc_attr($post_id)." .bucw-price-value{".esc_attr($fnt_color)."".esc_attr($font_sizes)."". esc_attr($font_familys)."}
        ul#bucw-ticker-widget-".esc_attr($post_id)."{".esc_attr($ticker_height)."}
        .bucw-header-ticker-fixedbar{".esc_attr($ticker_top)."}
        .bucw_no_data{display:none}";

            // }
           

    
    $ticker_speed =(int) get_post_meta($post_id,'ticker_speed', true ) ;
    $t_speed=$ticker_speed;
    if($ticker_position=="footer"||$ticker_position=="header"){
        $cls='ccpw-sticky-ticker';
        if($ticker_position=="footer"){
            $container_cls='bucw-footer-ticker-fixedbar';
        }else{
            $container_cls='bucw-header-ticker-fixedbar';
        }					 
    }else{
         $cls='bucw-ticker-cont';
         $container_cls='';
    }
        $output .= '
            <div class="ph-item preloader-placeholder '.esc_attr($container_cls).'">
            <div class="ph-col-3 ">
            <div class="ph-row">
            <div class="ph-col-12 big"></div>
            </div>
            </div>
            <div class="ph-col-3 ">
            <div class="ph-row">
            <div class="ph-col-12 big"></div>
            </div>
            </div>
            <div class="ph-col-3 ">
            <div class="ph-row">
            <div class="ph-col-12 big"></div>
            </div>
            </div>
            </div>';
         
            if($widget_type=='card'){
                $card_id = "bucw-card-widget-" . esc_attr($post_id);
                $id = "bucw-table-widget-" . esc_attr($post_id);
                $cls='bucw-main-wrapper';	
                $output.='<div id="'.esc_attr($card_id).'" class="bucw-card-wrp" style="display:none;">';			
                $output .= '<div id="'.esc_attr($id).'" class="'.esc_attr($cls).'" data-type="'.esc_attr($widget_type).'" data-nonce="'.esc_attr($nonce) .'">
                <div></div>
                <script id="ticker_settings" type="application/json">'.$settings_json.'</script></div></div>';
            }elseif($widget_type=='list'){
                $id = "bucw-table-widget-" . esc_attr($post_id);
                $cls='bucw-widget';		
                $cls='bucw-main-wrapper';	
                $output .= '<div id="'.esc_attr($id).'" class="'.esc_attr($cls).' coins-table" data-type="'.esc_attr($widget_type).'" data-nonce="'.esc_attr($nonce) .'">
                <table class="bucw_table"><thead style="display:none;">
                    <th>'.__('Name','ccpw').'</th>
                    <th>'.__('Price','ccpw').'</th>';
                   
                    $output .='<th>'.__('24H(%)','ccpw').'</th>
                    <th>'.__('High 24H','ccpw').'</th>
                    <th>'.__('Low 24H','ccpw').'</th>
                    <th>'.__('Volume 24H','ccpw').'</th>';
                $output .='</thead><tbody>';
                $output .= '</tbody>';
                $output.='<script id="ticker_settings" type="application/json">'.$settings_json.'</script></table></div>';
            }else{
                    $id = "bucw-ticker-widget-" . esc_attr($post_id);
                    $tickercss = 'bucw-ticker';
                    $output .= '<div id="'.esc_attr($id).'" class="bucw-container '.esc_attr($container_cls).' '.esc_attr($id).' " data-tickerspeed="'.esc_attr($t_speed).'" data-nonce="'.esc_attr($nonce ).'">
                    <div class="bucw-ticker-wrp">
                                    <ul class="'.esc_attr($tickercss).'" id="'.esc_attr($id).'"></ul>';
                                    $output.='<script id="ticker_settings" type="application/json">'.$settings_json.'</script>';
                    $output.='</div></div> ';
            }
            $output.='<div class="bucw_no_data"> '.__("No Data Found","bucw").'</div>';
        $dltcss= $dynamic_styles;        
        wp_add_inline_style('bucw-custom-icons',$dltcss);
        $dltv='<!-- BuyUcoin Cryptocurrency Widgets - Version:- '.BUCW_VERSION.' By Cool Plugins -->';	
        return  $output;
    }
    
    /*
|--------------------------------------------------------------------------
| loading required assets according to the widget type
|--------------------------------------------------------------------------
*/  
function bucw_enqueue_assets($type, $post_id,$font_family){
    if (!wp_script_is('jquery', 'done')) {
		wp_enqueue_script('jquery');
	}
    if($font_family!='unset' && !empty($font_family)){
    $family = str_replace(" ", "+",$font_family);
    $url = 'https://fonts.googleapis.com/css?family='.$family.'';
    wp_enqueue_style('bucw-goog-font',esc_url($url), array(), BUCW_VERSION, null, 'all'); 
    }
    wp_enqueue_style('bucw-custom-icons', BUCW_PLUGIN_URL.'assets/css/bucw-icons.css');
    wp_enqueue_style('placeholder-loading',BUCW_PLUGIN_URL.'assets/css/bucw-placeholder-loading.css', array(), BUCW_VERSION, null, 'all');
	$prefi = 'bucw-';
    $file_name = $prefi.$type.'.css';
    $full_path = 'layouts/'.$type.'/'.$file_name;
    wp_enqueue_style('dlt-card-style-'.$type.'',BUCW_PLUGIN_URL.$full_path,array(),BUCW_VERSION,null,'all');
    if($type=='card' || $type=='list'){
        
         wp_enqueue_script('bucw-list-scripts', BUCW_PLUGIN_URL.'/assets/js/bucw-list.js', array('jquery'), BUCW_VERSION, true);
        wp_localize_script( 'bucw-list-scripts', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )  ) );
    }else{
        wp_enqueue_script('bucw_bxslider_js', BUCW_PLUGIN_URL.'assets/js/bucw-bxslider.js', array('jquery'), BUCW_VERSION, true);
        wp_enqueue_script('bucw-ticker', BUCW_PLUGIN_URL.'/assets/js/bucw-ticker.js', array('jquery'), BUCW_VERSION, true);
         wp_localize_script( 'bucw-ticker', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
}
public function bucw_admin_script()
{
   
         wp_enqueue_style('bucw-admin',BUCW_PLUGIN_URL.'assets/css/admin.css',array(),BUCW_VERSION,null,'all');
    
}

}