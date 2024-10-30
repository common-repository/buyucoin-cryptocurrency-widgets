<?php

class BUCW_Posttype
{
    public function __construct()
    {
      //creating posttype for plugin settings panel
        add_action( 'init',array($this,'bucw_post_type'));
        
        if(is_admin()){
            add_filter( 'manage_bucw_posts_columns',array($this,'bucw_set_custom_edit_dlt_columns'), 10, 2 );
            add_action( 'manage_bucw_posts_custom_column' ,array($this,'bucw_custom_dlt_column'), 10, 2 );
            add_action( 'add_meta_boxes',array($this,'bucw_register_shortcode_meta_box'));
            add_action( 'save_post', array( $this,'bucw_save_bucw_shortcode'),10, 3 );
            
        }
        
        require_once BUCW_PLUGIN_DIR . 'admin/bucw-settings.php';
        // integrating cmb2 metaboxes in post type
		add_action( 'cmb2_admin_init','bucw_add_metaboxex');
    }
    function bucw_save_bucw_shortcode( $post_id, $post, $update ) {
        // Autosave, do nothing
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
                return;
        // AJAX? Not used here
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) 
                return;
        // Check user permissions
        if ( ! current_user_can( 'edit_post', $post_id ) )
                return;
        // Return if it's a post revision
        if ( false !== wp_is_post_revision( $post_id ) )
                return;
        /*
        * In production code, $slug should be set only once in the plugin,
        * preferably as a class property, rather than in each function that needs it.
        */
        $post_type = get_post_type($post_id);
    
        // If this isn't a 'ccpw' post, don't update it.
        if ( "bucw" != $post_type ) return;
            // - Update the post's metadata.
            if(isset($_POST['bucw_ticker_position'])&& in_array(sanitize_text_field($_POST['bucw_ticker_position']),array('header','footer'))){
                update_option('bucw-p-id',$post_id);
                update_option('bucw-shortcode',"[bucw id=".$post_id."]");
                }
    
         //   delete_transient( 'ccpw-coins' ); // Site Transient
    }
/*
|--------------------------------------------------------------------------
| Register Custom Post Type of Crypto Widget
|--------------------------------------------------------------------------
*/   
function bucw_post_type()
{
    $labels = array(
        'name' => _x('BuyUcoin Cryptocurrency Widgets', 'Post Type General Name', 'bucw'),
        'singular_name' => _x('BuyUcoin Cryptocurrency Widgets', 'Post Type Singular Name', 'bucw'),
        'menu_name' => __('BuyUcoin Widget', 'bucw'),
        'name_admin_bar' => __('Post Type', 'bucw'),
        //'archives' => __('Item Archives', 'bucw'),
        // 'attributes' => __('Item Attributes', 'bucw'),
        // 'parent_item_colon' => __('Parent Item:', 'bucw'),
        'all_items' => __('All Widgets', 'bucw'),
        'add_new_item' => __('Add New Shortcode', 'bucw'),
        'add_new' => __('Add New', 'bucw'),
        'new_item' => __('New Item', 'bucw'),
        'edit_item' => __('Edit Item', 'bucw'),
        'update_item' => __('Update Item', 'bucw'),
        'view_item' => __('View Item', 'bucw'),
        'view_items' => __('View Items', 'bucw'),
        'search_items' => __('Search Item', 'bucw'),
        'not_found' => __('Not found', 'bucw'),
        'not_found_in_trash' => __('Not found in Trash', 'bucw'),
        'featured_image' => __('Featured Image', 'bucw'),
        'set_featured_image' => __('Set featured image', 'bucw'),
        'remove_featured_image' => __('Remove featured image', 'bucw'),
        'use_featured_image' => __('Use as featured image', 'bucw'),
        'insert_into_item' => __('Insert into item', 'bucw'),
        'uploaded_to_this_item' => __('Uploaded to this item', 'bucw'),
        'items_list' => __('Items list', 'bucw'),
        'items_list_navigation' => __('Items list navigation', 'bucw'),
        'filter_items_list' => __('Filter items list', 'bucw'),
    );
    $args = array(
        'label' => __('BuyUcoin', 'bucw'),
        'description' => __('Post Type Description', 'bucw'),
        'labels' => $labels,
        'supports' => array('title','author'),
        'taxonomies' => array(''),
        'hierarchical' => false,
        'public' => true, // it's not public, it shouldn't have it's own permalink, and so on
        'show_ui' => true,
        'show_in_nav_menus' => true, // you shouldn't be able to add it to menus
        'menu_position' => 9,
        'show_in_admin_bar' => false,
        'show_in_menu' => true,
        'can_export' => true,
        'has_archive' => false, // it shouldn't have archive page
        'rewrite' => false, // it shouldn't have rewrite rules
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'menu_icon' => BUCW_PLUGIN_URL . '/assets/images/buc.svg',
        'capability_type' => 'post',
        'menu_position'      => null,
    );
    register_post_type('bucw', $args);
}
/*
|--------------------------------------------------------------------------
| Register  meta boxes for shortcode
|--------------------------------------------------------------------------
*/ 
    function bucw_register_shortcode_meta_box()
    {
        add_meta_box('bucw-shortcode', 'BuyUcoin Cryptocurrency Widgets Shortcode', array($this,'bucw_shortcode_meta'), 'bucw', 'side', 'high');
    }
    /*
    Plugin Shortcode meta section
    */
    function bucw_shortcode_meta()
    {
        $id = get_the_ID();
        $dynamic_attr = '';
        _e(' <p>Paste this shortcode anywhere in Page/Post.</p>', 'bucw');

        $element_type = get_post_meta($id, 'pp_type', true);
        $dynamic_attr .= "[bucw id=\"{$id}\"";
        $dynamic_attr .= ']';
        ?>
            <input style="width:100%" onClick="this.select();" type="text" class="regular-small bucw-shortcode" data-id =<?php echo esc_attr($id);?> name="my_meta_box_text" id="my_meta_box_text" value="<?php echo esc_attr(htmlentities($dynamic_attr)); ?>" readonly/>
        <?php
    }
/*
|--------------------------------------------------------------------------
| Set Custom Column for Post Type
|--------------------------------------------------------------------------
*/ 
function bucw_set_custom_edit_dlt_columns($columns) {
    $author = $columns['author'];
    $date = $columns['date'];
    unset($columns['author']);
    unset($columns['date']);
    $columns['type'] = __( 'Widget Type', 'bucw' );
    $columns['shortcode'] = __( 'Shortcode', 'bucw' );
    $columns['author'] = $author;
    $columns['date'] = $date;
    return $columns;
 }
function bucw_custom_dlt_column( $column, $post_id ) {
    switch ( $column ) {
        case 'type' :
               $type=get_post_meta( $post_id , 'type' , true ); 
             switch ($type){				
                case "list":
                    echo  __('Table Widget', 'bucw');
                 break;
                 case "card":
                    echo __('Card Widget', 'bucw');
                    break;
                default:
                     echo __('Ticker Widget','bucw');
             }
             
           break;
         case 'shortcode' :
             echo '<code>[bucw id="'.esc_html($post_id).'"]</code>'; 
         break;
         default:
             _e('Not Matched','bucw');
     }
 }
}


