<?php

/**
 * Define the metabox and field configurations.
 * Below defined filter is used to add custom font
 * add_filter('load_custom_font','load_font');
* function load_font(){
* $font = array("test"=>"test","test1"=>"test1");
*	return $font;
* }
* 
*/
function bucw_update_font(){
    $font = apply_filters('load_custom_font',array("unset"=>'Default',"Abel"=>'Abel', "Abril Fatface"=>"Abril Fatface", "Acme"=>"Acme", "Alegreya"=>"Alegreya", 
    "Alex Brush"=>"Alex Brush", "Amaranth"=>"Amaranth", "Amatic SC"=>"Amatic SC", "Anton"=>"Anton", 
    "Arbutus Slab"=>"Arbutus Slab", "Architects Daughter"=>"Architects Daughter", "Archivo"=>"Archivo",
     "Archivo Black"=>"Archivo Black", "Arima Madurai"=>"Arima Madurai", "Asap"=>"Asap", 
     "Bad Script"=>"Bad Script", "Baloo Bhaina"=>"Baloo Bhaina", "Bangers"=>"Bangers", 
    "Berkshire Swash"=>"Berkshire Swash", "Bitter"=>"Bitter", "Boogaloo"=>"Boogaloo",
     "Bree Serif"=>"Bree Serif", "Bungee Shade"=>"Bungee Shade", "Cantata One"=>"Cantata One", 
     "Catamaran"=>"Catamaran", "Caveat"=>"Caveat", "Caveat Brush"=>"Caveat Brush",
     "Ceviche One"=>"Ceviche One", "Chewy"=>"Chewy", "Contrail One"=>"Contrail One", 
     "Crete Round"=>"Crete Round", "Dancing Script"=>"Dancing Script", "Exo 2"=>"Exo 2",
      "Fascinate"=>"Fascinate", "Francois One"=>"Francois One", 
     "Freckle Face"=>"Freckle Face", "Fredoka One"=>"Fredoka One", "Gloria Hallelujah"=>"Gloria Hallelujah", 
     "Gochi Hand"=>"Gochi Hand", "Great Vibes"=>"Great Vibes",
      "Handlee"=>"Handlee","Inconsolata"=>"Inconsolata",
      "Indie Flower"   =>"Indie Flower",
      "Kaushan Script"=>"Kaushan Script",
      "Lalezar" =>"Lalezar",
      "Lato"=>"Lato",
      "Libre Baskerville"=>"Libre Baskerville",
      "Life Savers"=>"Life Savers",
      "Lobster"=>"Lobster",
      "Lora"=>"Lora",
      "Luckiest Guy"=>"Luckiest Guy",
      "Marcellus SC"=>"Marcellus SC",
      "Monoton"=> "Monoton",
      "Montserrat"=>"Montserrat",
      "News Cycle"=>"News Cycle",
      "Nothing You Could Do"=>"Nothing You Could Do",
      "Noto Serif"=>"Noto Serif", 
     "Oleo Script Swash Caps"=>"Oleo Script Swash Caps", "Open Sans"=>"Open Sans", "Open Sans Condensed"=>"Open Sans Condensed", "Oranienbaum"=>"Oranienbaum",
      "Oswald"=>"Oswald", "PT Sans"=>"PT Sans", "PT Sans Narrow"=>"PT Sans Narrow", "PT Serif"=>"PT Serif", "Pacifico"=>"Pacifico", "Patrick Hand"=>"Patrick Hand", 
      "Peralta"=>"Peralta", "Permanent Marker"=>"Permanent Marker", "Philosopher"=>"Philosopher", "Play"=>"Play", "Playfair Display"=>"Playfair Display", "Playfair Display SC"=>"Playfair Display SC",
       "Poiret One"=>"Poiret One", "Press Start 2P"=>"Press Start 2P", "Prosto One"=>"Prosto One", "Quattrocento"=>"Quattrocento", "Questrial"=>"Questrial", "Quicksand"=>"Quicksand", "Raleway"=>"Raleway", 
       "Rancho"=>"Rancho", "Righteous"=>"Righteous", "Roboto"=>"Roboto", "Roboto Condensed"=>"Roboto Condensed", "Roboto Slab"=>"Roboto Slab", "Rubik"=>"Rubik", "Rye"=>"Rye", "Satisfy"=>"Satisfy", 
       "Shadows Into Light"=>"Shadows Into Light", "Shojumaru"=>"Shojumaru", "Sigmar One"=>"Sigmar One", "Skranji"=>"Skranji", "Slabo 27px"=>"Slabo 27px", 
       "Special Elite"=>"Special Elite", "Tinos"=>"Tinos", "Ultra"=>"Ultra", "UnifrakturMaguntia"=>"UnifrakturMaguntia", "VT323"=>"VT323",
        "Yanone Kaffeesatz"=>"Yanone Kaffeesatz"));
    return $font;
}
function bucw_add_metaboxex()
{
   
    global $post;
    $new_array=array();
$post_id = isset($_GET['post'])?sanitize_text_field($_GET['post']):'';


$get_ticker_url = get_permalink($post_id);
    $prefix = 'bucw_';
    $cmb = new_cmb2_box(array(
        'id' => $prefix.'generate_shortcode',
        'title' => __('BuyUcoin Widget Settings', 'cmb2'),
        'object_types' => array('bucw'), // Post type
        'context' => 'normal',
        'priority' => 'high',
        'show_names' => true, // Show field names on the left
    ));
    $cmb->add_field(array(
    'name' => 'Widget Type<span style="color:red;">*</span>',
    'id' => 'type',
    'type' => 'select',
    'default' => 'ticker',
    'options' => array(
       'ticker' => __('Crypto Ticker', 'cmb2'),
        'list' => __('List', 'cmb2'),
        'card' => __('Card', 'cmb2'),
    //    / 'sms-ticker' => __('SMS Ticker', 'cmb2'),
    ),
));

    $cmb->add_field(
        array(
            'name' => 'Select Coin<span style="color:red;">*</span>',
            'id' => 'select-coin',
            'type' => 'pw_multiselect',
             'options' =>  bucw_get_all_data(),
             'attributes' => array(
                'data-conditional-id' => 'type',
                'data-conditional-value' => wp_json_encode( array( 'ticker', 'list' ) ),
                )
        ));
        $cmb->add_field(
            array(
                'name' => 'Select Coin<span style="color:red;">*</span>',
                'id' => 'select-coins',
                'type' => 'select',
                 'options' =>  bucw_get_all_data(),
                 'attributes'    => array(
                    'data-conditional-id'     => 'type',
                    'data-conditional-value'  => 'card',
                    'data-conditional-invert' => true
                ),
            ));

// $cmb->add_field(array(
//     'name' => 'Display 24 Hours changes? (Optional)',
//     'desc' => 'Select if you want to display Currency changes in price',
//     'id' => 'display_changes',
//     'type' => 'checkbox',
//     'attributes' => array(
//         'data-conditional-id'    =>'type',
//          'data-conditional-value' =>'ticker',
//        )
// ));

$cmb->add_field(array(
    'name' => 'Ticker Speed',
    'desc' => 'Low value = high speed. (Best between 45 - 125)',
    'id' => 'ticker_speed',
    'type' => 'text',
    'default' => '20',
     'attributes' => array(
                'data-conditional-id' => 'type',
             'data-conditional-value' => 'ticker',
                )
));
$cmb->add_field(array(
    'name' => 'Ticker Height ',
    'id' => 'ticker_height',
    'type' => 'text',
    'default' => '70',
    'attributes' => array(
    'data-conditional-id' => 'type',
    'data-conditional-value' => 'ticker',
    )
));
$cmb->add_field(array(
    'name' => 'Where Do You Want to Display Ticker? (Optional)',
    'desc' => '<br>Select the option where you want to display ticker.<span class="warning">Important: Do not add shortcode in a page if Header/Footer position is selected.</span>',
    'id' => 'bucw_ticker_position',
    'type' => 'radio_inline',
    'options' => array(
        'header' => __('Header', 'cmb2'),
        'footer' => __('Footer', 'cmb2'),
        'shortcode' => __('Anywhere', 'cmb2'),
    ),
    'default' => 'shortcode',

    'attributes' => array(
        // 'required' => true,
        'data-conditional-id' => 'type',
        'data-conditional-value' => 'ticker',
    ),

));

$cmb->add_field(array(
    'name' => 'Ticker Position(Top)',
    'desc' => 'Specify Top Margin (in px) - Only For Header Ticker',
    'id' => 'bucw_header_ticker_position',
    'type' => 'text',
    'default' => '33',
    'attributes' => array(
        // 'required' => true,
        'data-conditional-id' => 'type',
        'data-conditional-value' => 'ticker',
    ),
));
$cmb->add_field(array(
    'name' => 'Background Color',
    'desc' => 'Select background color',
    'id' => 'back_color',
    'type' => 'colorpicker',
    'default' => '#eee',
));
$cmb->add_field(array(
    'name' => 'Font Color',
    'desc' => 'Select font color',
    'id' => 'font_color',
    'type' => 'colorpicker',
    'default' => '#000',
));
$cmb->add_field(array(
    'name' => '24 Hours Changes Up Color?',
    'id' => 'display_up_color',
    'type' => 'colorpicker',
    'default' => '#006400',
    // 'attributes' => array(
    //         'data-conditional-id'    =>'type',
    //      'data-conditional-value' =>'ticker',
    //    )
   
));
$cmb->add_field(array(
    'name' => '24 Hours Changes Low Color?',
    'id' => 'display_low_color',
    'type' => 'colorpicker',
    'default' => '#FF0000',
    // 'attributes' => array(
    //      'data-conditional-id'    =>'type',
    //      'data-conditional-value' =>'ticker',
    //    )
   
));
	$cmb->add_field( array(
    'name' => 'Enable Number Formatting? (Optional)',
    'desc' => 'Select if you want to enable number formatting (Million/Billion)',
    'id'   => 'display_format',
    'type' => 'checkbox',
     'attributes' => array(
        // 'required' => true,
        'data-conditional-id'     => 'type',
            'data-conditional-value'  => wp_json_encode( array( 'list', 'card' ) ),
    ),
   
    
        ) );
$cmb->add_field( array(
    'name'          => __( 'Font', 'cmb2' ),
    'desc'          => __( 'Field description (optional)', 'cmb2' ),
    'id'            => 'bucw-font',
    'type'          => 'select',
    'options'=>bucw_update_font(),
    'show_names'    => true,
    'default'          => 'unset',
   
) );

$cmb->add_field(array(
    'name' => 'Font Size',
    'desc' => 'Select font Size(eg:14)',
    'id' => 'font_size',
    'type' => 'text',
    'default' => '',
));

$cmb->add_field(array(
    'name' => 'Custom CSS',
    'desc' => 'Enter custom CSS',
    'id' => 'custom_css',
    'type' => 'textarea',

));


  
}




