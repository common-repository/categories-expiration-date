<?php
/*
Plugin Name: Categories Expiration Date
Description: Set an expiration date for certain categories
Version: 0.1
Author: Team Bright Vessel
Author URI: https://brightvessel.com
Text Domain: cat-expiration-date
*/

if (!defined('ABSPATH'))
    die();

define('BV_EXPIRED_CATEGORIES_PATH',plugin_dir_path(__FILE__));

/*
 * Require installation hooks
 */
require_once BV_EXPIRED_CATEGORIES_PATH.'/etc/install.php';

global $wpdb;
if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}categories_expiration'") == "{$wpdb->prefix}categories_expiration") {
    $used_categories_query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}categories_expiration", OBJECT);
    $exclude = array();
    foreach($used_categories_query as $used_category){
        $exclude[] = $used_category->cat_id;
    }
    $args = array('exclude' => $exclude);
    $all_categories = get_categories($args);
    $new_args = array('include' => $exclude);
    $used_categories = get_categories($new_args);

    if(empty($exclude))
        $used_categories = array();
}

/*
 * Add new postmeta if the category has an expiration date 
 */
function bv_expired_categories_add_new_arrival_meta( $post_id ) {

    // If this is just a revision, don't check.
    if ( wp_is_post_revision( $post_id ) )
        return;

    global $used_categories_query;

    foreach($used_categories_query as $used_category){

        $actual_category = get_category($used_category->cat_id);

        // Get all categories and look if the one with the slug new-arrivals is there
        $categories = wp_get_post_categories($post_id);
        $is_new_arrival = array_filter(
          $categories,
          function ($e) use ($actual_category) {
            return get_category($e)->slug == $actual_category->slug;
          }
        );
     
        // If it is there, then add a post meta
        if($is_new_arrival){
            $date = date('Y-m-d');
            $expiration_date = date('Ymd', strtotime($date. ' + '.$used_category->expiration.' days'));
            if (!metadata_exists('post', $post_id, $actual_category->slug.'_expires_at')){
                add_post_meta($post_id,$actual_category->slug.'_expires_at',$expiration_date,true);
            }
        }
    }



    // Check old posts saved as new arrivals
 	bv_expired_categories_check_expired_arrivals();
}
add_action( 'save_post', 'bv_expired_categories_add_new_arrival_meta' );

/*
 * Check if there are any expired categories
 */
function bv_expired_categories_check_expired_arrivals(){
	global $wpdb, $table_prefix, $used_categories_query;

    foreach($used_categories_query as $used_category){

        $actual_category = get_category($used_category->cat_id);

    	$actual_date = date('Ymd');

    	$new_arrivals_id = $used_category->cat_id;

    	// Get all postmetas where the actual date token is smaller than the token stored before
    	$metas = $wpdb->get_results( "SELECT * FROM {$wpdb->postmeta} WHERE meta_value <= {$actual_date} AND meta_key = '{$actual_category->slug}_expires_at'", 'OBJECT_K' );

    	// Loop the metas, remove them and remove the relationship on the database (unassign the category)
    	foreach($metas as $meta){
    		$post_id = $meta->post_id;
    		$wpdb->get_results("DELETE FROM {$wpdb->term_relationships} WHERE object_id = {$post_id} AND term_taxonomy_id = {$new_arrivals_id}");
    		delete_post_meta($post_id,$actual_category->slug.'_expires_at');
    	}
    }
}

/*
 * Plugin Menu
 */
add_action( 'admin_menu', 'bv_expired_categories_menu' );
function bv_expired_categories_menu() {
    if(current_user_can('manage_categories')){
        add_options_page( 'Categories Expiration', 'Categories Expiration', 'manage_options', 'brightvessel-categories-expiration', 'bv_expired_categories_options' );
    }
}

function bv_expired_categories_options() {
    $section = 'main';
    if(isset($_GET['bv_section']))
        $section = $_GET['bv_section'];
    switch($section){
        case 'assignations':
            if(isset($_POST['clear_assignations']) && wp_verify_nonce($_GET['nonce'], 'bv_expired_categories_assign'))
                bv_expired_categories_clear_assignations(intval($_POST['clear_assignations']));
            require_once 'views/assignations.php';
        break;
        case 'tokens':
            if(isset($_POST['clear_tokens']) && wp_verify_nonce($_GET['nonce'], 'bv_expired_categories_token'))
                bv_expired_categories_clear_tokens(intval($_POST['clear_tokens']));
            require_once 'views/tokens.php';
        break;
        default:
            if(isset($_POST['category_expiration_id']) && wp_verify_nonce($_GET['nonce'], 'bv_expired_categories_create')){
                $cat_id = $_POST['category_expiration_id'];
                $expires_id = $_POST['category_expiration_expires'];
                bv_expired_categories_add_category_expiration(intval($cat_id),intval($expires_id));
            }

            if(isset($_GET['delete']) && wp_verify_nonce($_GET['nonce'], 'bv_expired_categories_delete'))
                bv_expired_categories_delete_category_expiration(intval($_GET['delete']));

            if(isset($_POST['edit_cat']) && wp_verify_nonce($_GET['nonce'], 'bv_expired_categories_edit')){
                $cat_id = intval($_POST['edit_cat']);
                $expires_in = intval($_POST['edit_expires']);
                bv_expired_categories_edit_category_expiration($cat_id,$expires_in);
            }
            require_once 'views/categories.php';
        break;
    }
}

function bv_expired_categories_css(){
    $src = plugins_url( 'assets/admin.css', __FILE__ );
    $handle = "expiredCategoriesCustomFrontCss";
    wp_register_script($handle, $src);
    wp_enqueue_style($handle, $src, array(), false, false);
}
add_action('admin_head', 'bv_expired_categories_css');

function bv_expired_categories_clear_assignations($cat_id){
    global $wpdb;
    $wpdb->get_results("DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = {$cat_id}");
}

function bv_expired_categories_clear_tokens($cat_id){
    global $wpdb;
    $actual_category = get_category($cat_id);
    $category = $actual_category->slug.'_expires_at';
    $date = date('Ymd');
    $used_categories_query = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}categories_expiration WHERE cat_id = {$cat_id}", OBJECT);
    $expires_in_n_days = $used_categories_query[0]->expiration;
    $expiration_date = date('Ymd', strtotime($date. ' + '.$expires_in_n_days.' days'));
    $wpdb->get_results("UPDATE {$wpdb->postmeta} SET meta_value = '{$expiration_date}' WHERE meta_key = '{$category}'");
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'bv_expired_categories_add_action_links' );
function bv_expired_categories_add_action_links ( $links ) {
    $mylinks = array(
        '<a href="' . admin_url( 'options-general.php?page=brightvessel-categories-expiration' ) . '">Settings</a>',
    );
    return array_merge( $links, $mylinks );
}

function bv_expired_categories_add_category_expiration($cat_id,$expires_in){
    global $wpdb;

    $wpdb->query( 
        $wpdb->prepare( 
            "
                INSERT INTO {$wpdb->prefix}categories_expiration
                ( id, cat_id, expiration )
                VALUES ( NULL, %d, %d )
            ", 
            $cat_id,
            $expires_in 
        ) 
    );
}

function bv_expired_categories_delete_category_expiration($cat_id){
    global $wpdb;

    $wpdb->query( 
        $wpdb->prepare( 
            "
                DELETE FROM {$wpdb->prefix}categories_expiration WHERE cat_id = %d
            ", 
            $cat_id
        ) 
    );
}

function bv_expired_categories_edit_category_expiration($cat_id,$expires_in){
    global $wpdb;

    $wpdb->query( 
        $wpdb->prepare( 
            "
                UPDATE {$wpdb->prefix}categories_expiration SET expiration = %d WHERE cat_id = %d
            ",
            $expires_in, 
            $cat_id
        ) 
    );
}