<?php
/**
 * Plugin Name: Doctors or Clinics Management
 * Plugin URI: http://www.sitenex.com
 * Version: 1.0
 * Description: Doctors or clinics management system.
 * Author: Saddam Hossain
 * Author URI: http://www.sitenex.com
**/

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  return;
}

// Define contants
define('DAC_ROOT', dirname(__FILE__));
define('DAC_URL', plugins_url('/', __FILE__));
define('DAC_HOME', home_url('/'));

require_once( DAC_ROOT . '/includes/custom-post-types.php' );
require_once( DAC_ROOT . '/includes/dac-functions.php' );
require_once( DAC_ROOT . '/includes/dac-appointment-func.php' );
require_once( DAC_ROOT . '/includes/dac-meta-box-config.php' );
require_once( DAC_ROOT . '/shortcodes/shortcodes.php' );

function initialize_dac_settings() {
	require_once( DAC_ROOT . '/includes/dac-settings.php');
}
add_action('admin_menu', 'initialize_dac_settings');

function add_dac_roles() {
	global $wp_roles;
	//remove_role('professional');
	add_role( 'professional', 'Professional', array('read' => true, 'edit_posts' => true, 'edit_published_posts' => true, 'level_2' => true) );
	//remove_role('clinic');
	add_role( 'clinic', 'Clinic', array('read' => true, 'edit_posts' => true, 'edit_published_posts' => true, 'level_2' => true) );
	//remove_role('account_manager');
	add_role( 'account_manager', 'Account Manager', array('read' => true, 'edit_posts' => true, 'edit_published_posts' => true, 'level_2' => true) );
}
register_activation_hook( __FILE__, 'add_dac_roles' );
//add_action( 'init', 'add_dac_roles' );

function install_required_tables() {
	dac_install_profile_views_table();
	dac_install_appointments_table();
	dac_install_distance_table();
	dac_install_claim_expire_table();
	dac_install_reviews_table();
}
register_activation_hook( __FILE__, 'install_required_tables' );
//add_action( 'admin_init', 'install_required_tables' );

/* add image upload capability for the current user if he doesn't have it */
add_action('admin_init', 'dac_allow_image_uploads');
function dac_allow_image_uploads() {
	/*if ( current_user_can('edit_posts') && !current_user_can('upload_files') ) {*/
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if($user_role == 'professional') {
		$professional = get_role('professional');
		$professional->add_cap('upload_files');
		//$professional->add_cap('edit_pending_posts');
	}
	if($user_role == 'clinic') {
		$clinic = get_role('clinic');
		$clinic->add_cap('upload_files');
		//$clinic->add_cap('edit_pending_posts');
	}
	if($user_role == 'account_manager') {
		$account_manager = get_role('account_manager');
		$account_manager->add_cap('upload_files');
		//$account_manager->add_cap('edit_pending_posts');
	}
	/*}*/
}

function dac_custom_post_status(){
	register_post_status( 'hold', array(
		'label'                     => _x( 'Hold', 'DIVI' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Hold <span class="count">(%s)</span>', 'Hold <span class="count">(%s)</span>' ),
	) );
}
add_action( 'init', 'dac_custom_post_status' );

function dac_enqueue_scripts() {
	wp_enqueue_script('jquery');
	
	$ajaxurl = admin_url('admin-ajax.php');
	$ajax_nonce = wp_create_nonce('DAC');
	
	wp_localize_script( 'jquery', 'ajaxObj', array( 'ajaxurl' => $ajaxurl, 'ajax_nonce' => $ajax_nonce ) );
	
	wp_enqueue_style( 'rating-style', plugins_url('/css/jquery.rating.css', __FILE__ ) );
	wp_enqueue_script( 'dac-script', plugins_url('/js/dac-scripts.js', __FILE__ ) );
	wp_enqueue_script( 'gmap-script', 'http://maps.google.com/maps/api/js' );
	wp_enqueue_script( 'rating-script', plugins_url('/js/jquery.rating.js', __FILE__ ) );
}
add_action('wp_enqueue_scripts', 'dac_enqueue_scripts', 10);
?>