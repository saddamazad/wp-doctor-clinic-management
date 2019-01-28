<?php
function check_session_status() {
	if( isset($_SESSION['membership_type']) ) {
		echo '<h1>'.$_SESSION['membership_type'].'</h1>';
	}
}
//add_action('init', 'check_session_status');

function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );

function dac_excerpt_more($more) {
    global $post;
	//return '<a href="'. get_permalink($post->ID) . '"> Read More....</a>';
	return '';
}
add_filter('excerpt_more', 'dac_excerpt_more');

function show_profile_completion_notification() {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	$user_id = get_current_user_id();

	if( ($user_role == 'professional') || ($user_role == 'clinic') ) {
		$post_id = get_user_meta( $user_id, '_dac_post_id', true );
		$cosmetic_certificate = get_post_meta( $post_id, '_dac_cosmetic_certificate', true );
		$prof_insurance = get_post_meta( $post_id, '_dac_prof_insurance', true );
		if( empty($cosmetic_certificate) || empty($prof_insurance) ) {
			$class = 'notice notice-error';
			$message = __( 'Please Upload Your Cosmetic Certificate & Insurance/Indemnity in Your Profile.', 'DIVI' );
		
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}
	}
}
add_action( 'admin_notices', 'show_profile_completion_notification' );

/*function switch_profile_post_status($post_id) {
    if ( ('professional' != get_post_type($post_id)) || ('clinic' != get_post_type($post_id)) ) {
        return;
    }

	$cosmetic_certificate = get_post_meta( $post_id, '_dac_cosmetic_certificate', true );
	$prof_insurance = get_post_meta( $post_id, '_dac_prof_insurance', true );
	if( empty($cosmetic_certificate) || empty($prof_insurance) ) {
		// unhook this function so it doesn't loop infinitely
		remove_action( 'save_post', 'switch_profile_post_status' );

		// update the post, which calls save_post again
		wp_update_post( array( 'ID' => $post_id, 'post_status' => 'pending' ) );

		// re-hook this function
		add_action( 'save_post', 'switch_profile_post_status' );
	}
}
add_action( 'save_post', 'switch_profile_post_status' );*/

add_action('admin_head', 'dac_append_post_status_list');
function dac_append_post_status_list(){
	global $post, $typenow;
	$complete = '';
	$label = '';
	//if($post->post_type == 'professional' || $post->post_type == 'clinic') {
    if (current_user_can('update_core')) {
		if($typenow == 'professional' || $typenow == 'clinic') {
			if($post->post_status == 'hold'){
				$complete = ' selected=\"selected\"';
				$label = '<span id=\"post-status-display\"> Hold</span>';
			} elseif($post->post_status == 'draft'){
				$label = '<span id=\"post-status-display\"> Active</span>';
			}
			echo '<script type="text/javascript">
					jQuery(document).ready(function(){
					   jQuery("select#post_status").append("<option value=\"hold\" '.$complete.'>Hold</option>");
					   jQuery(".misc-pub-section label").append("'.$label.'");
					   jQuery("select#post_status option[value=\"draft\"]").text("Active");
					   jQuery("select#post_status option[value=\"pending\"]").remove();
					   /*jQuery("#minor-publishing-actions").append("<a href=\"'.admin_url().'post.php?post='.$post->ID.'&action=edit&cr_action=send-email\" id=\"send-email_'.$post->ID.'\" class=\"button send_email\">Email</a>");*/
					});
				</script>';

			echo '<style type="text/css">
						.wp-admin.post-type-professional .subsubsub, .wp-admin.post-type-clinic .subsubsub { display: block !important; }
						.wp-admin.post-type-professional .subsubsub li, .wp-admin.post-type-clinic .subsubsub li { display: none; }
						.wp-admin.post-type-professional .subsubsub li.hold, .wp-admin.post-type-clinic .subsubsub li.hold { display: inline-block; }
						.wp-admin.post-type-professional .subsubsub li.all, .wp-admin.post-type-clinic .subsubsub li.all { display: inline-block; }
				</style>';
		}
	}
}

function switch_profile_post_status() {
	if( isset($_POST['save']) ) {
		$post_type = $_POST['post_type'];
		$post_id = $_POST['post_ID'];
		$cosmetic_certificate = $_POST['_dac_cosmetic_certificate'];
		$prof_insurance = $_POST['_dac_prof_insurance'];
		
		if ( ('professional' == $post_type) || ('clinic' == $post_type) ) {

			global $wpdb;
			$post_table = $wpdb->base_prefix . "posts";
			$meta_table = $wpdb->base_prefix . "postmeta";
			$distance_table = $wpdb->base_prefix . "dac_distance";
			
			/*$cosmetic_sql = "SELECT meta_value From $meta_table WHERE meta_key='_dac_cosmetic_certificate' AND post_id=$post_id";
			$cosmetic_cert = $wpdb->get_row( $wpdb->prepare( $cosmetic_sql ), ARRAY_A );
			$insurance_sql = "SELECT meta_value From $meta_table WHERE meta_key='_dac_prof_insurance' AND post_id=$post_id";
			$insurance = $wpdb->get_row( $wpdb->prepare( $insurance_sql ), ARRAY_A );
	
			$cosmetic_certificate = $cosmetic_cert['meta_value'];
			$prof_insurance = $insurance['meta_value'];*/

			if( ($cosmetic_certificate != '') && ($prof_insurance != '') ) {
				$wpdb->update( $post_table, array( 'post_status' => "draft" ), array( "ID" => $post_id));
			} else {
				$wpdb->update( $post_table, array( 'post_status' => "hold" ), array( "ID" => $post_id));
			}

			if ( 'professional' == $post_type ) {
				$address = get_post_meta($post_id, '_dac_location', true);
			} elseif( 'clinic' == $post_type ) {
				$address = get_post_meta($post_id, '_dac_clinic_location', true);
			}
			
			$city = get_post_meta($post_id, '_dac_city', true);
			$fullurl = "http://maps.googleapis.com/maps/api/directions/json?origin=".urlencode($address)."&destination=".urlencode($city);
	
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, ''.$fullurl.'');
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			//curl_setopt ($ch, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$curl_response_res = curl_exec ($ch);
			curl_close ($ch);
			 
			// decode the json
			$resp = json_decode($curl_response_res, true);
			//print_r($resp);
			//$resp['routes'][0]['legs'][0]['distance']['value'];
			//echo $resp['routes'][0]['legs'][0]['distance']['text'];
			if( $resp['routes'][0]['legs'][0]['distance']['text'] ) {
				$distance_km = $resp['routes'][0]['legs'][0]['distance']['text'];
				$distance_val = $resp['routes'][0]['legs'][0]['distance']['value'];
				//update_post_meta($post_id, '_dac_distance', $resp['routes'][0]['legs'][0]['distance']['text']);

				$distance_sql = "SELECT ID From $distance_table WHERE post_id=$post_id";
				$distance = $wpdb->get_row( $wpdb->prepare( $distance_sql ), ARRAY_A );
				if( count($distance) > 0 ) {
					$wpdb->update( $distance_table, array( 'distance' => $distance_val, "updated_datetime" => current_time( 'mysql' ) ), array( "ID" => $distance['ID']) );
				} else {
					$wpdb->insert( $distance_table, array("post_id" => $post_id, "distance" => $distance_val, "created_datetime" => current_time( 'mysql' ), "updated_datetime" => current_time( 'mysql' ) ));
				}

				$distance_meta_sql = "SELECT meta_id From $meta_table WHERE post_id=$post_id AND meta_key='_dac_distance'";
				$distance_meta = $wpdb->get_row( $wpdb->prepare( $distance_meta_sql ), ARRAY_A );
				//print_r($wpdb->last_result);
				if( count($distance_meta) > 0 ) {
					$wpdb->update( $meta_table, array( 'meta_value' => $distance_km ), array( "meta_id" => $distance_meta['meta_id']) );
				} else {
					$wpdb->insert( $meta_table, array("post_id" => $post_id, "meta_key" => '_dac_distance', "meta_value" => $distance_km ));
				}
			}
		}
	}
}
add_action( 'admin_init', 'switch_profile_post_status' );

function dac_remove_dashboard_widgets() {
    $user = wp_get_current_user();
    if ( ! $user->has_cap( 'manage_options' ) ) {
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
        remove_meta_box( 'fue-dashboard', 'dashboard', 'normal' );
    }
}
add_action( 'wp_dashboard_setup', 'dac_remove_dashboard_widgets' );

// Move the 'Right Now' dashboard widget to the right hand side
/*function wptutsplus_move_dashboard_widget() {
    $user = wp_get_current_user();
    if ( ! $user->has_cap( 'manage_options' ) ) {
        global $wp_meta_boxes;
        $widget = $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'];
        unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
        $wp_meta_boxes['dashboard']['side']['core']['dashboard_right_now'] = $widget;
    }
}
add_action( 'wp_dashboard_setup', 'wptutsplus_move_dashboard_widget' );*/

function get_clinic_list() {
	/*$args = array(
		'post_type'  => 'clinic',
		'posts_per_page' => -1,
		'post_status' => array('draft', 'publish')
	);
	$query = new WP_Query( $args );

	$clinics = array();
	global $post;
	$clinics[] = "Select Clinic";
	while($query->have_posts()): $query->the_post();
		$clinics[$post->ID] = get_the_title();
	endwhile;*/

	global $wpdb;
	$post_table = $wpdb->base_prefix . "posts";
	$clinics = array();
	$clinics[] = "Select Clinic";
	$results = $wpdb->get_results("SELECT ID, post_title FROM $post_table WHERE post_type='clinic' AND post_status='draft'", ARRAY_A);
	if( sizeof($results) > 0 ) {
		foreach($results as $clinic) {
			$clinics[$clinic['ID']] = $clinic['post_title'];
		}
	}
	
	return $clinics;
}

function get_treatments_list() {
	$args = array(
		'post_type' => 'specialties',
		'posts_per_page' => -1
	);
	$query = new WP_Query( $args );
	//category
	$treatments = array();
	global $post;
	while($query->have_posts()): $query->the_post();
		//$treatments[$post->ID] = get_the_title();
		$treatments[get_the_title()] = get_the_title();
	endwhile;
	
	return $treatments;
}

/*function disable_billing_fields( $fields ) {
	global $woocommerce;
	// if the total is more than 0 then we still need the fields
	if ( 0 != $woocommerce->cart->total ) {
		return $fields;
	}
	// return the regular billing fields if we need shipping fields
	if ( $woocommerce->cart->needs_shipping() ) {
		return $fields;
	}
	// we don't need the billing fields so empty all of them except the email
	unset( $fields['billing_country'] );
	unset( $fields['billing_first_name'] );
	unset( $fields['billing_last_name'] );
	unset( $fields['order_comments'] );
	unset( $fields['billing_company'] );
	unset( $fields['billing_address_1'] );
	unset( $fields['billing_address_2'] );
	unset( $fields['billing_city'] );
	unset( $fields['billing_state'] );
	unset( $fields['billing_postcode'] );
	unset( $fields['billing_phone'] );

	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'disable_billing_fields', 20 );*/

add_filter( 'woocommerce_checkout_fields' , 'disable_billing_fields' );
function disable_billing_fields( $fields ) {
    unset($fields['billing']['billing_first_name']);
    unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_phone']);
    unset($fields['order']['order_comments']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_company']);
    //unset($fields['billing']['billing_email']);

    unset($fields['account']['account_username']);
    //unset($fields['account']['account_password']);
    return $fields;
}

add_action('woocommerce_before_checkout_billing_form', 'show_custom_text_in_checkout', 50);
function show_custom_text_in_checkout() {
?>
	<h2>Create Your Account &amp; Setup Your Membership</h2>
    <div class="title_border">&nbsp;</div>
    <!--<p>Please Enter Your Email Address, Account Password & Select a Payment Option Below to Create Your New Trusted Treatment Account: </p>-->
<?php
}

/*function dac_unrequire_billing_email_field( $fields ) {
    $fields['billing_email']['required'] = false;
    return $fields;
}
add_filter( 'woocommerce_billing_fields', 'dac_unrequire_billing_email_field' );*/

//Add custom capibility for quote_manager
/*function dac_custom_role_caps() {

		// Add the roles you'd like to administer the custom post types
		$roles = array('clinic', 'account_manager');
		
		// Loop through each role and assign capabilities
		foreach($roles as $the_role) { 

		     $role = get_role($the_role);
			
	             $role->add_cap( 'read' );
	             $role->add_cap( 'read_professional_post');
	             $role->add_cap( 'read_private_professional_posts' );
	             $role->add_cap( 'edit_professional_post' );
	             $role->add_cap( 'edit_professional_posts' );
	             $role->add_cap( 'edit_others_professional_posts' );
	             $role->add_cap( 'edit_published_professional_posts' );
	             $role->add_cap( 'publish_professional_posts' );
	             $role->add_cap( 'delete_others_professional_posts' );
	             $role->add_cap( 'delete_private_professional_posts' );
	             $role->add_cap( 'delete_published_professional_posts' );			 
	             $role->add_cap( 'read_clinic_post');
	             $role->add_cap( 'read_private_clinic_posts' );
	             $role->add_cap( 'edit_clinic_post' );
	             $role->add_cap( 'edit_clinic_posts' );
	             $role->add_cap( 'edit_others_clinic_posts' );
	             $role->add_cap( 'edit_published_clinic_posts' );
	             $role->add_cap( 'publish_clinic_posts' );
	             $role->add_cap( 'delete_others_clinic_posts' );
	             $role->add_cap( 'delete_private_clinic_posts' );
	             $role->add_cap( 'delete_published_clinic_posts' );
	}
}
add_action( 'admin_init', 'dac_custom_role_caps');*/

function load_specific_scripts() {
	if( is_checkout() ) {
		echo '<style type="text/css">
				.woocommerce-billing-fields h3, .woocommerce-shipping-fields h3, .create-account > p:first-of-type { display: none; }
				.woocommerce-checkout form #billing_email_field { width: 100%; }
			</style>';
		
		echo '<script type="text/javascript">
				jQuery( document ).ready(function() {
					//jQuery(".create-account > p:first-of-type").remove();
				});
			</script>';
	}
	echo '<script type="text/javascript">
			jQuery( document ).ready(function() {
				jQuery(".woocommerce-order-received .shop_table.order_details a").attr("href", "#");
				jQuery(".woocommerce-order-received .shop_table.my_account_orders a").attr("href", "#");
				jQuery(".woocommerce-order-received .shop_table.my_account_orders .button.view").hide();
			});
		</script>';
}
add_action('wp_head', 'load_specific_scripts');


add_action( 'user_register', 'dac_registration_callback', 10, 1 );
function dac_registration_callback( $user_id ) {
	$userdata = array();
	$userdata['ID'] = $user_id;
	if( $_SESSION['account_type'] == 'Professional' ) {
		$userdata['role'] = 'professional';
		$post_type = 'professional';
	} elseif( $_SESSION['account_type'] == 'Clinic' ) {
		$userdata['role'] = 'clinic';
		$post_type = 'clinic';
	}
	
	if( ($_SESSION['account_type'] == "Professional") || ($_SESSION['account_type'] == "Clinic")) {
		wp_update_user($userdata);

		if ( isset( $_SESSION['first_name'] ) ) {
			update_user_meta($user_id, 'first_name', $_SESSION['first_name']);
		}
		if ( isset( $_SESSION['last_name'] ) ) {
			update_user_meta($user_id, 'last_name', $_SESSION['last_name']);
		}
	
		update_user_meta( $user_id, 'show_admin_bar_front', 'false' );
		update_user_meta( $user_id, 'show_admin_bar_admin', 'false' );
		// on payment/email confirmation "_dac_user_status" will be 'active'
		update_user_meta( $user_id, '_dac_user_status', 'pending' );
		
		if( $_SESSION['account_type'] == 'Professional' ) {
			
			/*$the_product = get_post( $_SESSION['membership_type_professional'] ); 
			$membership_type = strtolower($the_product->post_title);
			update_user_meta( $user_id, '_dac_user_level', $membership_type );*/
			
		} elseif( $_SESSION['account_type'] == 'Clinic' ) {
			
			/*$the_product = get_post( $_SESSION['membership_type_clinic'] );
			$membership_type = strtolower($the_product->post_title);
			update_user_meta( $user_id, '_dac_user_level', $membership_type );*/
			
			/*if( $membership_type == 'standard' ) {
				// set num. of administration account
				update_user_meta( $user_id, '_dac_num_of_admin_account', 1 );
			} elseif( $membership_type == 'pro' ) {
				update_user_meta( $user_id, '_dac_num_of_admin_account', 3 );
			} elseif( $membership_type == 'promoted' ) {
				update_user_meta( $user_id, '_dac_num_of_admin_account', 5 );
			}*/
			
			//$_SESSION['membership_type'] = $membership_type;
		}

		update_user_meta( $user_id, '_dac_num_of_admin_account', 2 );
		
		if( isset($_SESSION['clinic_id']) ) {
			$clinic_id = $_SESSION['clinic_id'];
		}

		if( $_SESSION['account_type'] == 'Professional' ) {
			$post_title = esc_attr( $_SESSION['first_name'] ) . ' ' .esc_attr( $_SESSION['last_name'] );
			//$post_status = 'pending';
		} elseif( $_SESSION['account_type'] == 'Clinic' ) {
			$post_title = esc_attr( $_SESSION['clinic_name'] );
			//$post_status = 'draft';
		}
		
		$defaults = array(
					  'post_type'      => $post_type,
					  'post_title'     => $post_title,
					  'post_author'    => $user_id,
					  'post_status'    => 'hold'
					);
									
		if($post_id = wp_insert_post( $defaults )) {
			// add post meta data
			/*update_post_meta($post_id, '_dac_location', esc_attr( $_SESSION['location'] ));*/
			update_post_meta($post_id, '_dac_profession', esc_attr( $_SESSION['user_profession'] ));
			update_post_meta($post_id, '_dac_title', esc_attr( $_SESSION['title'] ));
			update_post_meta($post_id, '_dac_days_full', '');
			$pin_code = substr(md5($user_id), 10, 5);
			update_post_meta($post_id, '_dac_pin_code', $pin_code);
			if( !isset($_SESSION['claim_page_id']) ) {
				update_post_meta($post_id, '_dac_date_of_birth', esc_attr( $_SESSION['birth_date'] ));
				update_post_meta($post_id, '_dac_regulatory_body', esc_attr( $_SESSION['regulatory_body'] ));
				update_post_meta($post_id, '_dac_registration_number', esc_attr( $_SESSION['registration_number'] ));
			}
			if( $_SESSION['account_type'] == 'Professional' ) {
				if( isset($clinic_id) ) {
					update_post_meta($post_id, '_dac_associated_clinic', $clinic_id);
				}
			}
			
			update_post_meta( $post_id, '_dac_user_id', $user_id );
			//add user profile to post
			update_user_meta( $user_id, '_dac_post_id', $post_id );
			
			global $wpdb;
			$table_profile_views = $wpdb->base_prefix . "dac_profile_views";
			$wpdb->insert( $table_profile_views, array("post_id" => $post_id, "created_datetime" => current_time( 'mysql' ), "updated_datetime" => current_time( 'mysql' ) ));

			$headers = array('Content-Type: text/html; charset=UTF-8');
			$message = 'Hi ' . $_SESSION['first_name'] . ", <br>";
			$message .= "Welcome to TrustedTreatment! <br>";
			$message .= "<br>";
			$message .= "Just one more step - please confirm your email address. If you're ever locked out of your account, this will help us get you back in. <br>";
			$message .= "<br>";
			$message .= "Confirm your email address <a href='".home_url()."/?confirm_uid=$user_id'>click here</a> <br>";
			$message .= "<br>";
			$message .= "Regards, <br>";
			$message .= "TrustedTreatment Support Team";

			$user = get_user_by( 'id', $user_id );
			$user_email = $user->user_email;
			wp_mail( $user_email, 'Please confirm your email', $message, $headers );
			
			if( isset($_SESSION['claim_page_id']) ) {
				$message = 'Hello Admin, <br>';
				$message .= "User registration done for the page claim bearing ID <strong>".$_SESSION['claim_page_id']."</strong><br>";
				$message .= "<br>";
				$message .= 'Please delete this page from the "Page Claim" area. <br>';
				$message .= "<br>";
				$message .= "Regards, <br>";
				$message .= "TrustedTreatment Support Team";
				
				$admin_email = get_option( 'admin_email' );
				wp_mail( $admin_email, 'Delete page from "Page Claim" area', $message, $headers );
			}
		}
	}
}

function dac_restrict_login() {
	//is there a user to check?
	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	$user_id = get_current_user_id();
	if( ($user_role == 'professional') || ($user_role == 'clinic') ) {
		// redirect them to the default place
		if( get_user_meta( $user_id, '_dac_user_status', true ) == 'pending' ) {
		    wp_redirect( home_url('/wp-login.php') );
			exit;
		}
	}
}
add_filter( 'admin_init', 'dac_restrict_login', 10 );

function confirm_user_email() {
	if( isset($_GET['confirm_uid']) && $_GET['confirm_uid'] > 0 ) {
		$user_id = $_GET['confirm_uid'];
		update_user_meta( $user_id, '_dac_user_status', 'active' );
		wp_redirect( home_url('/wp-login.php') );
		exit;
	}
}
add_action('init', 'confirm_user_email');

function hide_specific_admin_menu() {
	global $current_user;

	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {
	?>
		<style type="text/css">
			/*#menu-posts,*/ #menu-comments, #menu-posts-specialties, #menu-tools, #wp-admin-bar-comments, #wp-admin-bar-new-content, #wp-admin-bar-view-store, #menu-posts-project, #menu-media, #menu-posts-cosmetic_news, #menu-posts-claims { display: none; }
			#menu-posts-professional ul li:nth-child(3) { display: none; }
			#menu-posts-clinic ul li:nth-child(3) { display: none; }
			<?php if($user_role == 'professional') { ?>
			#menu-posts-clinic { display: none; }
			<?php } ?>
			<?php if($user_role == 'clinic'  || $user_role == 'account_manager') { ?>
			#menu-posts-professional { display: none; }
			<?php } ?>
			.wrap h1 .page-title-action, /*#publishing-action,*/ #edit-slug-box { display: none; }
			.post-type-professional #publishing-action, .post-type-clinic #publishing-action { display: none; }
			<?php if($user_role == 'account_manager') { ?>
			#menu-posts-clinic ul li:last-child, #menu-posts { display: none; }
			<?php } ?>
			#misc-publishing-actions .misc-pub-post-status { display: none; }
			.subsubsub, #posts-filter .search-box { display: none; }
			#your-profile h2:first-of-type, #your-profile .form-table:first-of-type { display: none; }
			#your-profile h2:nth-of-type(4), #your-profile .form-table:nth-of-type(4) { display: none; }
			.wp-admin.post-type-post #et_settings_meta_box, .wp-admin.post-type-post #formatdiv, .wp-admin.post-type-post #categorydiv, .wp-admin.post-type-post #tagsdiv-post_tag, .wp-admin.post-type-post #postexcerpt, .wp-admin.post-type-post #postcustom, .wp-admin.post-type-post #wp-admin-bar-view, .wp-admin.post-type-post #et_pb_toggle_builder { display: none; }
			#your-profile #cupp_container, .post-type-post #major-publishing-actions { display: none; }
		</style>
	<?php
	}
	?>
    	<style type="text/css">
			#menu-posts-specialties, .wp-admin.post-type-post #post-preview, .wp-admin.post-type-post .wrap .subsubsub { display: none; }
			.wp-admin.post-type-professional .subsubsub, .wp-admin.post-type-clinic .subsubsub, #wp-admin-bar-wp-logo { display: none; }
			.wp-admin.post-type-professional .row-actions .editinline, .wp-admin.post-type-clinic .row-actions .editinline { display: none; }
			.wp-admin.post-type-professional .row-actions .view, .wp-admin.post-type-clinic .row-actions .view { display: none; }
			<?php if (current_user_can('update_core')) { ?>
			#toplevel_page_appointments { display: none; }
			<?php } ?>
		</style>
        
        <script type="text/javascript">
			jQuery( document ).ready(function() {
				jQuery(".wp-admin.post-type-professional .inline-edit-row select[name=_status] > option[value=publish]").remove();
				jQuery(".wp-admin.post-type-professional .inline-edit-row select[name=_status] > option[value=draft]").text('Active');

				jQuery(".wp-admin.post-type-clinic .inline-edit-row select[name=_status] > option[value=publish]").remove();
				jQuery(".wp-admin.post-type-clinic .inline-edit-row select[name=_status] > option[value=draft]").text('Active');
			});
		</script>
    <?php
}
add_action('admin_head', 'hide_specific_admin_menu');

add_filter('gettext', 'change_draft_text_filter', 20, 3);
function change_draft_text_filter( $translated_text, $untranslated_text, $domain ) {

	global $typenow, $current_user;
	
	if( is_admin() && (('clinic' == $typenow) || ('professional' == $typenow) || ('post' == $typenow)) )  {
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		/*if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {*/
			//make the changes to the text
			switch( $untranslated_text ) {
			
				case 'Draft':
				  $translated_text = __( '', 'DIVI' );
				break;
			
				case 'Save Draft':
				  $translated_text = __( 'Save','DIVI' );
				break;
				
				case 'Post draft updated.':
				  $translated_text = __( 'Post updated.','DIVI' );
				break;
			}
		/*}*/
   }
   return $translated_text;
}

function rename_specific_menu() {
    global $menu, $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {
		//print_r($menu);
		$menu[31][0] = 'My Profile';
		$menu[32][0] = 'My Profile';
		/*$menu[31][0] = 'My Profile';
		$menu[30][0] = 'My Profile';*/
		$menu[70][0] = 'My Account';
	}
	$menu[5][0] = 'Profile Updates';
}  
add_action( 'admin_menu', 'rename_specific_menu' );


add_action('admin_head-post.php', 'add_css_to_posttype_header');
function add_css_to_posttype_header(){
	global $post, $current_user;
	if($post->post_type == 'clinic' || $post->post_type == 'professional'){
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		//if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {
	?>
		<style type="text/css">
			.postarea, #preview-action, #wp-admin-bar-preview { display: none; }
			#misc-publishing-actions .misc-pub-post-status, .misc-pub-section.misc-pub-curtime { display: none; }
			.post-type-professional #publishing-action, .post-type-clinic #publishing-action { display: none; }
			tr.cmb_id__dac_distance th { vertical-align: top; }
			<?php if (current_user_can('update_core')) { ?>
			#misc-publishing-actions .misc-pub-post-status { display: block; }
			<?php } ?>
		</style>
	<?php
		//}
	}
}

function draw_calendar($month, $year, $clinic_calendar = false, $user_id = '') {
	if($user_id == '') {
		global $current_user;
		get_currentuserinfo();
		$user_id = $current_user->ID;
		$post_id = get_user_meta( $user_id, '_dac_post_id', true );
		if(!$clinic_calendar) {
			$date_url = '/edit.php?post_type=professional&page=appointment-calendar';
		} else {
			$date_url = '/edit.php?post_type=clinic&page=clinic-appointment-calendar';
		}
	} else {
		//global $wpdb;
		//$post_table = $wpdb->base_prefix . "posts";
		//$meta_table = $wpdb->base_prefix . "postmeta";
		//$post_id_sql = "SELECT meta_value FROM $meta_table WHERE meta_key='_dac_user_id' AND post_id=$user_id";
		//$post_id_result = $wpdb->get_row($wpdb->prepare($post_id_sql),ARRAY_A);
		$post_id = $user_id;
		$date_url = '/edit.php?post_type=clinic&page=clinic-professionals&prid='.$post_id;
		/*$week_end_sql = "SELECT meta_value FROM $meta_table WHERE post_id = $post_id AND meta_key = '_dac_week_end_day'";
		$week_end_result = $wpdb->get_row($wpdb->prepare($week_end_sql),ARRAY_A);
		$weekend = $week_end_result['meta_value'];

		$days_full_sql = "SELECT meta_value FROM $meta_table WHERE post_id = $post_id AND meta_key = '_dac_days_full'";
		$days_full_result = $wpdb->get_row($wpdb->prepare($days_full_sql),ARRAY_A);
		$days_full = $days_full_result['meta_value'];*/
	}
	
	if($clinic_calendar) {
		$weekend = get_post_meta( $post_id, '_dac_clinic_week_end_day', true );
	} else {
		$weekend = get_post_meta( $post_id, '_dac_week_end_day', true );
	}
	$days_full = get_post_meta( $post_id, '_dac_days_full', true );
	$days_full_arr = explode(",", $days_full);

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np"> </td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day">';
			$weekday = date('l', strtotime("$year/$month/$list_day"));
			//if( $weekday == $weekend ) {
			if(in_array($weekday, $weekend)) {
				$class = 'week-end';
				$dayHtml = $list_day;
			} else {
				$class = 'open-day';
				$dayHtml = $list_day."\n";
				$dayHtml .= '<a href="'.admin_url($date_url.'&cal_day='.$list_day.'&cal_month='.$month.'&cal_year='.$year).'">Edit</a>';
			}
			
			if( count($days_full_arr) > 0 ) {
				if( in_array("$year-$month-$list_day", $days_full_arr) ) {
					$full_class = "day_full";
				} else {
					$full_class = "";
				}
			}
			
			/* add in the day number */
			$calendar.= '<div class="day-number '.$class.' '.$full_class.'">'.$dayHtml.'</div>';

			/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
			$calendar.= str_repeat('<p> </p>',2);
			
		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np"> </td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';
	
	/* all done, return result */
	return $calendar;
}


// Pluggable wp mail function write here
if ( !function_exists('wp_new_user_notification') ) :
function wp_new_user_notification($user_id, $random_password, $first_name, $last_name, $email) {

 	$email_subject = 'Welcome to TrustedTreatment';

	$message = 'Hi ' . $first_name . ", <br>";
	$message .= "Welcome to TrustedTreatment! <br>";
	$message .= "<br>";
	$message .= "Please login to access your dashboard. Here you will be able to add contact details, treatments, photos, updates and much more. <br>";
	$message .= "<br>";
	$message .= "If you have any questions please <a href='".home_url()."/contact-us/'>contact us</a> <br>";
	$message .= "<br>";
	$message .= "Regards, <br>";
	$message .= "TrustedTreatment Support Team";
	
	$headers = array('Content-Type: text/html; charset=UTF-8');
	wp_mail( $email, $email_subject, $message, $headers );
}
endif;

// show only the current user images in the media uploader  
add_filter( 'posts_where', 'dac_wpquery_where' );
function dac_wpquery_where( $where ) {
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {
    /*if( is_user_logged_in() ){*/
         // logged in user, but are we viewing the library?
         if( isset( $_POST['action'] ) && ( $_POST['action'] == 'query-attachments' ) ){
            // here you can add some extra logic if you'd want to.
            $where .= ' AND post_author='.$current_user->data->ID;
        }
    /*}*/
	}

    return $where;
}

add_image_size( 'profile-size', 200, 200, true );

function get_search_profiles($search_result_posts, $location) {
	$output = '';
	$output .= '<div class="et_pb_column et_pb_column_1_2 et_pb_column_1">
		<div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_1">';
                    
	foreach($search_result_posts as $post_id) {
        $output .= '<div class="Search_calint clearfix">';
		if ( has_post_thumbnail($post_id) ) {
			 $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'profile-size');
			$output .= '<img src="' . $image_url[0] . '" alt="" />';
			//$output .= get_the_post_thumbnail($post_id, array(200, 200));
		} else {
			$output .= '<img src="' . DAC_URL . 'images/unknown_user.png" width="200" height="200" />';
		}
		/*<img src="http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im.png" alt="serch_calint_im" class="alignnone size-full wp-image-271" srcset="http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im-150x150.png 150w, http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im-157x157.png 157w, http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im.png 200w" sizes="(max-width: 200px) 100vw, 200px" height="200" width="200">*/
		//get_post_meta($post_id, '', true)
		$user_type = get_post_type($post_id);
		if($user_type == 'professional') {
			$title = get_post_meta($post_id, '_dac_title', true).' ';
			$profession = ' – '.get_post_meta($post_id, '_dac_profession', true);
		} else {
			$title = '';
			$profession = '';
		}
        $output .= '<h3>'.$title.get_the_title($post_id).$profession.'</h3>';
		if( get_post_meta($post_id, '_dac_distance', true) ) {
			/*if( get_post_meta($post_id, '_dac_distance', true) == -1 ) {
        		$output .= '<h5>0.00 Mile</h5>';
			} else {*/
        		$output .= '<h5>'.get_post_meta($post_id, '_dac_distance', true).'</h5>';
			/*}*/
		}
        $output .= '<h4>Subspecialties:</h4>';
        $output .= '<p>'.get_post_meta($post_id, '_dac_subspecialties', true).'</p>';
        $output .= '<p><a class="view_profile" href="javascript: void(0)" data-id="'.$post_id.'"><i class="fa fa-check-circle"></i> VIEW PROFILE </a></p>';
        $output .= '</div>';
		$output .= '<form action="'.DAC_HOME.'profile/" method="post" name="profile_'.$post_id.'_form">
						<input type="hidden" value="'.$post_id.'" name="profile_id" />
					</form>';
	}
    
    $output .= '</div>
			</div>';

	$output .= '<div class="et_pb_column et_pb_column_1_2 et_pb_column_2 last">
		<div class="et_pb_text et_pb_module et_pb_bg_layout_light et_pb_text_align_left  et_pb_text_2">
			<div class="g-map">
				<iframe src="'.DAC_URL.'shortcodes/google-map.php?location='.$location.'"></iframe>
			</div>
		</div>
	</div>';
	
	return $output;
}

// function to geocode address, it will return false if unable to geocode address
function geocode($address){
 
    // url encode the address
    $address = urlencode($address);
     
    // google map geocode api url
    $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
 
    // get the json response
    /*$resp_json = file_get_contents($url);*/
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, ''.$url.'');
	curl_setopt($ch, CURLOPT_HTTPGET, 1);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	//curl_setopt ($ch, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$curl_response_res = curl_exec ($ch);
	curl_close ($ch);
     
    // decode the json
    $resp = json_decode($curl_response_res, true);
 
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }else{
        return false;
    }
}

add_action( 'wp_ajax_get_treatments', 'get_treatments' );
add_action( 'wp_ajax_nopriv_get_treatments', 'get_treatments' );
function get_treatments() {
	$term = strip_tags($_POST['treatment']);
	$term = mysql_real_escape_string($term); // Attack Prevention
	if($term != "") {

		/*global $wpdb;
		$table = $wpdb->base_prefix . 'rmdh_products';
		$sql = "SELECT * FROM $table WHERE company_id = $company_id AND product_name LIKE('$term%') ORDER BY product_name";
		$results = $wpdb->get_results($sql,ARRAY_A);*/
		
		//print_r($result_1);
		/*$suite_args = array(
					'post_type' => array( 'professional', 'clinic' ),
					'posts_per_page' => -1,
					'post_status' => array('publish', 'draft'),
					'meta_key'     => '_dac_profession',
					'meta_value'   => $term,
					'meta_compare' => 'LIKE'
					//'post__in' => $postIds
				);*/

		/*$suite_posts = new WP_Query( $suite_args );
		if($suite_posts->have_posts()) {
			global $post;
			while($suite_posts->have_posts()): $suite_posts->the_post();
			endwhile;
			wp_reset_postdata();
		}*/
		global $wpdb;
		$post_table = $wpdb->base_prefix . "posts";
		$options_table = $wpdb->base_prefix . "options";
		//$meta_table = $wpdb->base_prefix . "postmeta";
		$results = $wpdb->get_results("SELECT post_title FROM $post_table WHERE post_type='specialties' AND post_status='publish' AND post_title LIKE '%$term%'", ARRAY_A);
		/*$results = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE (option_name='treatments_procedures' AND option_value LIKE '%$term%') OR (meta_key='_dac_profession' AND meta_value LIKE '%$term%')", ARRAY_A);*/

		$string = '';
		//$string .= $wpdb->last_query;
		
		if( sizeof($results) > 0 ) {
			foreach($results as $result){
				$string .= '<span style="cursor:pointer;" data-treatment="'.$result['post_title'].'">'.$result['post_title'].'</span>';
			}
		} else {
			$string .= "No treatments found!";
		}

		echo $string;
		die(); 

	} else {
		echo $string = '';
		die();
	}
}

function insert_treatment_posts() {
	$treatments_arr = explode(',', get_option('treatments_procedures'));
	foreach($treatments_arr as $treatment) {
		$defaults = array(
					  'post_type'      => 'specialties',
					  'post_title'     => $treatment,
					  'post_author'    => get_current_user_id(),
					  'post_status'    => 'publish'
					);
									
		wp_insert_post( $defaults );
	}
}
//add_action('admin_init', 'insert_treatment_posts');

add_action( 'wp_ajax_get_locations', 'get_locations' );
add_action( 'wp_ajax_nopriv_get_locations', 'get_locations' );
function get_locations() {
	$term = strip_tags($_POST['location']);
	$term = mysql_real_escape_string($term); // Attack Prevention
	if($term != "") {
		global $wpdb;
		/*$results = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE (meta_key='_dac_location' AND meta_value LIKE '%$term%') OR (meta_key='_dac_clinic_location' AND meta_value LIKE '%$term%') OR (meta_key='_dac_additional_location' AND meta_value LIKE '%$term%')", ARRAY_A);*/
		$results = $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE (meta_key='_dac_city' AND meta_value LIKE '%$term%')", ARRAY_A);

		$string = '';
		//$string .= $wpdb->last_query;
		
		if( sizeof($results) > 0 ) {
			foreach($results as $result){
				$string .= '<span style="cursor:pointer;" data-location="'.$result['meta_value'].'">'.$result['meta_value'].'</span>';
			}
		} else {
			$string .= "No city found!";
		}

		echo $string;
		die(); 

	} else {
		echo $string = '';
		die();
	}
}


// add new dashboard widgets
function dac_add_dashboard_widgets() {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {
		wp_add_dashboard_widget( 'dac_dashboard_profile_views', 'Profile Views', 'dac_add_profile_views_widget' );
		wp_add_dashboard_widget( 'dac_dashboard_appointment_requests', 'Appointment Requests', 'dac_add_appointment_requests_widget' );
		add_meta_box('dac_dashboard_quick_links', 'Quick links', 'dac_add_quick_links_widget', 'dashboard', 'side', 'high');
	}
}

function dac_add_quick_links_widget() {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);

	$user_id = get_current_user_id();
	$post_id = get_user_meta( $user_id, '_dac_post_id', true );
	$post_type = $user_role;
	if($user_role == 'account_manager') {
		$parent_user_id = get_user_meta( $user_id, '_dac_parent_clinic_user_id', true );
		$post_id = get_user_meta( $parent_user_id, '_dac_post_id', true );
		$post_type = get_post_type( $post_id );
	}

	if($post_type == 'clinic') {
		$clinic = 'clinic-';
	} else {
		$clinic = '';
	}
?>
	<ul>
    	<li><a href="<?php echo admin_url('/edit.php?post_type='.$post_type.'&page='.$clinic.'appointment-calendar'); ?>">My Calendar</a></li>
    	<li><a href="<?php echo admin_url('/admin.php?page=appointments'); ?>">My Appointment Requests</a></li>
    	<li><a href="<?php echo admin_url('/post.php?post='.$post_id.'&action=edit'); ?>">My Profile</a></li>
    </ul>
<?php
}

function dac_add_profile_views_widget() {
	$user_id = get_current_user_id();
	$post_id = get_user_meta( $user_id, '_dac_post_id', true );

	global $wpdb;
	$table_profile_views = $wpdb->base_prefix . "dac_profile_views";
	$profile_views = $wpdb->get_row( $wpdb->prepare( "SELECT * from $table_profile_views WHERE post_id=$post_id"), ARRAY_A );
	$monthly_views = explode(",", $profile_views['monthly_views']);
	if( sizeof($monthly_views) > 0 ) {
		foreach($monthly_views as $key => $value) {
			if( stripos($value, date("Y-m")) !== false ) {
				$views = explode(":", $value);
				echo '<p>Your profile views for this month: <strong>' . $views[0] . '</strong></p>';
				break;
			}/* else {
				echo '<p>There is no profile view for this month</p>';
			}*/
		}
	}/* else {
		echo '<p>There is no profile view.</p>';
	}*/
	$weekly_views = explode(",", $profile_views['weekly_views']);
	if( sizeof($weekly_views) > 0 ) {
		foreach($weekly_views as $key => $value) {
			if( stripos($value, date("Y-W")) !== false ) {
				$views = explode(":", $value);
				echo '<p>Your profile views for this week: <strong>' . $views[0] . '</strong></p>';
				break;
			}/* else {
				echo '<p>There is no profile view for this month</p>';
			}*/
		}
	}/* else {
		echo '<p>There is no profile view.</p>';
	}*/
}
 
function dac_add_appointment_requests_widget() {

	$user_id = get_current_user_id();
	$post_id = get_user_meta( $user_id, '_dac_post_id', true );

	global $wpdb;
	$table_appointments = $wpdb->base_prefix . "dac_appointments";
	$cur_month = date("m");
	$monthly_appointments = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) from $table_appointments WHERE appt_doctor_id = $post_id AND appt_month='$cur_month'") );
	if($monthly_appointments > 0) {
		echo '<p>Appointment requests for this month: <strong>' . $monthly_appointments . '</strong></p>';
	} else {
		echo '<p>There is no appointment request for this month</p>';
	}

	$cur_week = date("W");
	$weekly_appointments = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) from $table_appointments WHERE appt_doctor_id = $post_id AND appt_week='$cur_week'") );
	if($weekly_appointments > 0) {
		echo '<p>Appointment requests for this week: <strong>' . $weekly_appointments . '</strong></p>';
	} else {
		echo '<p>There is no appointment request for this week</p>';
	}
 
}
add_action( 'wp_dashboard_setup', 'dac_add_dashboard_widgets' );

//add_action( 'show_user_profile', 'dac_show_extra_profile_fields' );
//add_action( 'edit_user_profile', 'dac_show_extra_profile_fields' );
function dac_show_extra_profile_fields( $user ) {
	$user_roles = $user->roles;
	$user_role = array_shift($user_roles);
	if ( ($user_role == 'professional') || ($user_role == 'clinic') || ($user_role == 'account_manager') ) {
?>
	<table class="form-table">
		<tr>
			<th>Disabled Parking</th>
			<td>
				<select class="text-select" name="disabled_parking" id="disabled_parking">
					<option <?php if(get_the_author_meta( 'disabled_parking', $user->ID ) == 'Yes') echo 'selected="selected"'; ?> value="Yes">Yes</option>
					<option <?php if(get_the_author_meta( 'disabled_parking', $user->ID ) == 'No') echo 'selected="selected"'; ?> value="No">No</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Parking on Site</th>
			<td>
				<select class="text-select" name="parking_on_site" id="parking_on_site">
					<option <?php if(get_the_author_meta( 'parking_on_site', $user->ID ) == 'No') echo 'selected="selected"'; ?> value="No">No</option>
					<option <?php if(get_the_author_meta( 'parking_on_site', $user->ID ) == 'Free') echo 'selected="selected"'; ?> value="Free">Free</option>
					<option <?php if(get_the_author_meta( 'parking_on_site', $user->ID ) == 'Paid') echo 'selected="selected"'; ?> value="Paid">Paid</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>Nearest Parking</th>
			<td>
            	<input type="text" name="nearest_parking" id="nearest_parking" value="<?php echo get_the_author_meta( 'nearest_parking', $user->ID ); ?>" />
			</td>
		</tr>
		<tr>
			<th>Parking Charge</th>
			<td>
            	<input type="text" name="parking_charge" id="parking_charge" value="<?php echo get_the_author_meta( 'parking_charge', $user->ID ); ?>" />
			</td>
		</tr>
	</table>
<?php
	}
}

//add_action( 'personal_options_update', 'dac_save_extra_profile_fields' );
//add_action( 'edit_user_profile_update', 'dac_save_extra_profile_fields' );
function dac_save_extra_profile_fields( $user_id ) {

	/*if ( !current_user_can( 'edit_user', $user_id ) )
		return false;*/

	update_user_meta($user_id, 'disabled_parking', $_POST['disabled_parking']);
	update_user_meta($user_id, 'parking_on_site', $_POST['parking_on_site']);
	update_user_meta($user_id, 'nearest_parking', $_POST['nearest_parking']);
	update_user_meta($user_id, 'parking_charge', $_POST['parking_charge']);
}

add_action('admin_init', 'notification_bubble_in_appointments_menu');
function notification_bubble_in_appointments_menu() {
    global $menu, $current_user;
	//print_r($menu);
	//$GLOBALS[ 'menu' ]
	$user_id = get_current_user_id();
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ( $user_role == 'account_manager' ) {
		$user_id = get_user_meta( $user_id, '_dac_parent_clinic_user_id', true );
	}
	$post_id = get_user_meta( $user_id, '_dac_post_id', true );

    $new_appointments = get_number_of_new_appointments_by_id( $post_id );
	/*foreach($menu as $menu_page) {
		if($menu_page[0] == 'Appointments') {
    		$menu_page[0] .= $new_appointments ? "<span class='update-plugins count-1'><span class='update-count'>$new_appointments </span></span>" : '';
		}
	}*/
    $menu[15][0] .= $new_appointments ? "<span class='update-plugins count-1'><span class='update-count'>$new_appointments </span></span>" : '';
}

function get_number_of_new_appointments_by_id( $id ) {
    global $wpdb;
	$appointments_table = $wpdb->base_prefix . "dac_appointments";
    return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $appointments_table WHERE appt_doctor_id=$id AND appt_status='Unread';" ) );
}

/*add_action('admin_menu', 'notification_bubble_in_clinic_menu');
function notification_bubble_in_clinic_menu() {
    global $menu;
	//print_r($menu);
	$user_id = get_current_user_id();
	global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	if ( $user_role == 'account_manager' ) {
		$user_id = get_user_meta( $user_id, '_dac_parent_clinic_user_id', true );
	}
	$post_id = get_user_meta( $user_id, '_dac_post_id', true );

    $new_appointments = get_number_of_new_appointments_by_id( $post_id );
    $menu[30][0] .= $new_appointments ? "<span class='update-plugins count-1'><span class='update-count'>$new_appointments </span></span>" : '';
}*/

function update_profile_views() {
	if ( isset($_POST['profile_id']) ) {
		$post_id = $_POST['profile_id'];
		global $wpdb;
		$table_profile_views = $wpdb->base_prefix . "dac_profile_views";
		$profile_views = $wpdb->get_row( $wpdb->prepare( "SELECT * from $table_profile_views WHERE post_id=$post_id"), ARRAY_A );
		$total_views = $profile_views['total_views'] + 1;
		$monthly_views = $profile_views['monthly_views'];
		if($monthly_views == '') {
			$monthly_views = '1:'.date("Y-m");
		} else {
			$monthly_views_arr = explode(",", $monthly_views);
			if( stripos(end($monthly_views_arr), date("Y-m")) !== false ) {
				$views_arr = explode(":", end($monthly_views_arr));
				$new_monthly_views = ($views_arr[0] + 1);
				//if( count($monthly_views_arr) > 1 ) {
					array_pop($monthly_views_arr);
				//}
				$monthly_views = $new_monthly_views.':'.date("Y-m");
				array_push($monthly_views_arr, $monthly_views);
			} else {
				// this is a new month
				$new_monthly_views = '1:'.date("Y-m");
				array_push($monthly_views_arr, $new_monthly_views);
			}
			$monthly_views = join(",", $monthly_views_arr);
		}

		$weekly_views = $profile_views['weekly_views'];
		if($weekly_views == '') {
			// week starts in Monday
			$weekly_views = '1:'.date("Y-W");
		} else {
			$weekly_views_arr = explode(",", $weekly_views);
			if( stripos(end($weekly_views_arr), date("Y-W")) !== false ) {
				$week_views_arr = explode(":", end($weekly_views_arr));
				$new_weekly_views = ($week_views_arr[0] + 1);
				//if( count($weekly_views_arr) > 1 ) {
					array_pop($weekly_views_arr);
				//}
				$weekly_views = $new_weekly_views.':'.date("Y-W");
				array_push($weekly_views_arr, $weekly_views);
			} else {
				// this is a new week
				$new_weekly_views = '1:'.date("Y-W");
				array_push($weekly_views_arr, $new_weekly_views);
			}
			$weekly_views = join(",", $weekly_views_arr);
		}
		$wpdb->update( $table_profile_views, array( 'total_views' => $total_views, 'monthly_views' => $monthly_views, 'weekly_views' => $weekly_views, 'updated_datetime' => current_time( 'mysql' ) ), array( "post_id" => $_POST['profile_id']));		
	}
}
add_action('init', 'update_profile_views');

function posts_for_current_author($query) {
	global $user_level;

	if($query->is_admin && $user_level < 5) {
		global $user_ID;
		$query->set('author',  $user_ID);
		unset($user_ID);
	}
	unset($user_level);

	return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

function dac_load_profile_scripts() {
	if ( is_page('profile') && isset($_POST['profile_id']) ) {
		function dac_enqueue_profile_scripts() {
			wp_enqueue_script('jquery');
			$full_days = get_post_meta($_POST['profile_id'], '_dac_days_full', true);
			$full_days_arr = explode(",", $full_days);
			$full_days_new = array();
			foreach($full_days_arr as $full_day) {
				if( substr($full_day, 5, 1) == 0 ) {
					$full_day = substr_replace($full_day ,'', 5, 1);
				}
				$full_days_new[] = $full_day;
			}
			$full_days = join(",", $full_days_new);

			$user_type = get_post_type($_POST['profile_id']);
			if($user_type == 'professional') {
				$weekEnds = get_post_meta($_POST['profile_id'], '_dac_week_end_day', true);
			} elseif($user_type == 'clinic') {
				$weekEnds = get_post_meta($_POST['profile_id'], '_dac_clinic_week_end_day', true);
			}
			$off_days = array();
			foreach($weekEnds as $weekEnd) {
				$off_days[] = date("w", strtotime($weekEnd));
			}
			
			wp_localize_script( 'jquery', 'profileObj', array( 'disableddates' => explode(",", $full_days), 'week_ends' => $off_days ) );
		}
		add_action('wp_enqueue_scripts', 'dac_enqueue_profile_scripts', 10);
	}
}
add_action('template_redirect', 'dac_load_profile_scripts');


add_action( 'wp_ajax_get_review_profile', 'get_review_profile' );
add_action( 'wp_ajax_nopriv_get_review_profile', 'get_review_profile' );
function get_review_profile() {
	// get posted data
	$review_code = $_POST['profile_code'];
	$email = $_POST['email'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$rating_count = $_POST['rating_count'];
	$review_message = $_POST['review_message'];

	$args = array(
		'post_type'  => array( 'clinic', 'professional' ),
		'posts_per_page' => -1,
		'post_status' => array('draft', 'publish'),
		'meta_key'     => '_dac_pin_code',
		'meta_value'   => $review_code,
		'meta_compare' => '='
	);
	$query = new WP_Query( $args );

	if( $query->have_posts() ) {
		global $post;
		while($query->have_posts()): $query->the_post();
			if( $email ) {
				global $wpdb;
				$post_table = $wpdb->base_prefix . "dac_reviews";
				$result = $wpdb->get_row( $wpdb->prepare( "SELECT * from $post_table WHERE profile_id=".$post->ID." AND email='$email'"), ARRAY_A );
				if( count($result) > 0 ) {
					$message = 'You have already left a review for this professional';
				} else {
					global $wpdb;
					$profile_id = $post->ID;
					$reviews_table = $wpdb->base_prefix . "dac_reviews";
					$wpdb->insert( $reviews_table, array("profile_id" => $profile_id, "email" => $email, "name" => $name, "phone" => $phone, "rating" => $rating_count, "message" => $review_message, "status" => 'pending', "created_datetime" => current_time( 'mysql' ) ));
					$reviewId = $wpdb->insert_id;
					
					//$to = 'saddam987020@gmail.com';
					$to = get_option( 'admin_email' );
					$subject = 'New review submitted';
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
					$message_body = 'Hello Admin,<br/>';
					$message_body .= '<br/>';
					$message_body .= 'There is a new review waiting for your approval. Please <a href="'.admin_url('/options-general.php?page=review-page&review_id='.$reviewId).'">approve/reject</a> it.<br/>';
					$message_body .= '<br/>';
					$message_body .= '~Thanks';
					wp_mail( $to, $subject, $message_body );

					$message = 'Thanks, your review is submitted.';
				}
			}
		endwhile;
	} else {
		$message = 'Your code is not valid.';
	}

	/*echo $string = '';
	die();*/
	
	echo json_encode(array("msg" => $message, "success" => $success));
	exit;
}

add_action( 'wp', 'dac_setup_schedule_scan' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function dac_setup_schedule_scan() {
	if ( ! wp_next_scheduled( 'dac_schedule_scan' ) ) {
		wp_schedule_event( time(), 'hourly', 'dac_schedule_scan');
	}
}


add_action( 'dac_schedule_scan', 'dac_do_this_hourly' );
/**
 * On the scheduled action hook, run a function.
 */
function dac_do_this_hourly() {
	// do something every hour
	$day = date('l');
	$hour = date('g A');
	$day_number = date('j');

	$users = get_registered_users();
	foreach($users as $user) {
		//$author_info = get_userdata($user->ID);
		$subscriptions = WC_Subscriptions_Manager::get_users_subscriptions( $user->ID );
		foreach($subscriptions as $subscrip_data){
			$order_id = $subscrip_data['order_id'];
			$product_id = $subscrip_data['product_id'];
			$variation_id = $subscrip_data['variation_id'];
			$period = $subscrip_data['period'];
			$interval = $subscrip_data['interval'];		
			$start_date = $subscrip_data['start_date'];
			$expiry_date = $subscrip_data['expiry_date'];
			$end_date = $subscrip_data['end_date'];
			$last_payment_date = $subscrip_data['last_payment_date'];
			
			$status = $subscrip_data['status'];
			if($status == 'active') {
				
				//$subscription_amount = WC_Subscriptions_Order::get_price_per_period( $order_id, $product_id );
				
			} else {
				/*$post_id = get_user_meta( $user->ID, '_dac_post_id', true );
				$my_post = array(
								'ID' => $post_id,
								'post_status' => 'hold'
							);
				wp_update_post( $my_post );*/

				if($hour) {
					$to = 'saddam987020@gmail.com';
					$subject = 'DAC hour '.$hour;
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
					$message = 'HTML messege from DAC<br/>';
					wp_mail( $to, $subject, $message );
				} else {
					$to = 'saddam987020@gmail.com';
					$subject = 'DAC hour number not found';
					add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
					$message = 'HTML messege from DAC<br/>';
					wp_mail( $to, $subject, $message );
				}
			}
		}
	}
	
}

function get_registered_users() { 

    $users = array();
    $roles = array('professional', 'clinic');

    foreach ($roles as $role) :
        $users_query = new WP_User_Query( array( 
            'fields' => 'all_with_meta', 
            'role' => $role, 
            'orderby' => 'display_name'
            ) );
        $results = $users_query->get_results();
        if ($results) $users = array_merge($users, $results);
    endforeach;

    return $users;
}
	
add_filter( 'wp_mail_from_name', function( $name ) {
	return 'Trusted Treatment';
});
?>