<?php
add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once DAC_ROOT . '/assets/CMB/init.php';

}


add_filter( 'cmb_meta_boxes', 'dac_cmb_sample_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function dac_cmb_sample_metaboxes( array $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_dac_';

	/**
	 * Meta box for tab features
	 */

	
	/**
	 * Meta box for tab features
	 */
	$meta_boxes['professional_metabox'] = array(
		'id'         => 'professional_metabox',
		'title'      => __( 'Professional Specifications', 'cmb' ),
		'pages'      => array( 'professional' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name' => __( 'Title', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'title',
				'type'    => 'select',
				'options' => array(
					'Prof' => __( 'Prof', 'cmb' ),
					'Dr' => __( 'Dr', 'cmb' ),
					'Mr'   => __( 'Mr', 'cmb' ),
					'Mrs'   => __( 'Mrs', 'cmb' ),
					'Ms'   => __( 'Ms', 'cmb' ),
					'Miss'   => __( 'Miss', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Profession', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'profession',
				'type'    => 'select',
				'options' => array(
					'Doctor' => __( 'Doctor', 'cmb' ),
					'Surgeon' => __( 'Surgeon', 'cmb' ),
					'Dentist'   => __( 'Dentist', 'cmb' ),
					'Nurse'   => __( 'Nurse', 'cmb' ),
				),
			),
			array(
				'name' => __( 'About You', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'about_me',
				'type'    => 'textarea',
			),
			array(
				'name' => __( 'Subspecialties', 'cmb' ),
				'desc' => __( '<small>Ex: General Dermatology, Cosmetic Dermatology</small>', 'cmb' ),
				'id'   => $prefix . 'subspecialties',
				'type'    => 'textarea_small',
			),
			array(
				'name' => __( 'Days Off', 'cmb' ),
				'desc' => __( 'Please choose the days respectively if you choose more than one' ),
				'id'   => $prefix . 'week_end_day',
				'type'    => 'multicheck',
				'options' => array(
					'Sunday' => __( 'Sunday', 'cmb' ),
					'Monday' => __( 'Monday', 'cmb' ),
					'Tuesday'   => __( 'Tuesday', 'cmb' ),
					'Wednesday'   => __( 'Wednesday', 'cmb' ),
					'Thursday'   => __( 'Thursday', 'cmb' ),
					'Friday'   => __( 'Friday', 'cmb' ),
					'Saturday'   => __( 'Saturday', 'cmb' ),
				),
				'inline'  => true,
			),			
			/*array(
				'name' => __( 'Unavailability Date', 'cmb' ),
				'desc' => __( 'Enter unavailability date (if any) for this week <br /><small style="color:#db4639;">Date "YYYY-MM-DD" format</small>', 'cmb' ),
				'id'   => $prefix . 'unavailability_date',
				'type'    => 'text_small',
			),*/
			array(
				'name' => __( 'Date of Birth', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'date_of_birth',
				'type'    => 'text_date',
			),
			array(
				'name' => __( 'Phone Number', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'phone_number',
				'type' => 'text_medium',
			),
			array(
				'name' => __( 'Your Education', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'education',
				'type'    => 'textarea',
			),
			array(
				'name' => __( 'Hours of Operation', 'cmb' ),
				'desc' => __( '<small>Ex: 9:00 AM - 5:00 PM</small>', 'cmb' ),
				'id'   => $prefix . 'working_hours',
				'type'    => 'text_medium',
			),
			/*array(
				'name' => __( 'Treatment & Procedures', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'treatments',
				'type'    => 'multicheck',
				'options' => get_treatments_list(),
				'inline'  => true,
			),*/			
			array(
				'name' => __( 'City', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'city',
				'type'    => 'text_medium',
			),
			array(
				'name' => __( 'Address', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'location',
				'type'    => 'textarea_small',
			),
			/*array(
				'name' => __( 'Distance', 'cmb' ),
				'desc' => __( 'Distance from the city (Miles)<br><small>If your city is "Nottingham", and you are about 20 miles away from the main city, then set the distance to <strong>20</strong>. If you are in the main city then set the distance to \'<strong>-1</strong>\'. <span style="color:#cd41ff;">You won\'t be listed in the search result if this field is empty</span>.</small>', 'cmb' ),
				'id'   => $prefix . 'distance',
				'type'    => 'text_small',
			),*/
			array(
				'name' => __( 'Additional Location', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'additional_location',
				'type'    => 'textarea_small',
			),
			array(
				'name' => __( 'PIN Code', 'cmb' ),
				'desc' => __( 'It will be used for your reviews', 'cmb' ),
				'id'   => $prefix . 'pin_code',
				'type'    => 'text_small',
			),
			array(
				'name' => __( 'Disabled Parking', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'disabled_parking',
				'type'    => 'select',
				'options' => array(
					'Yes' => __( 'Yes', 'cmb' ),
					'No' => __( 'No', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Parking on Site', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'parking_on_site',
				'type'    => 'select',
				'options' => array(
					'No' => __( 'No', 'cmb' ),
					'Free' => __( 'Free', 'cmb' ),
					'Paid' => __( 'Paid', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Nearest Parking', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'nearest_parking',
				'type'    => 'text_medium',
			),
			array(
				'name' => __( 'Parking Charge', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'parking_charge',
				'type'    => 'text_medium',
			),
			array(
				'name' => __( 'Clinic/Practice', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'associated_clinic',
				'type'    => 'select',
				'options' => get_clinic_list()
			),			
			array(
				'name' => __( 'About Practice', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'about_practice',
				'type'    => 'textarea',
			),
			array(
				'name' => __( 'Logo', 'cmb' ),
				'desc' => __( 'Image size should be 200px * 150px', 'cmb' ),
				'id'   => $prefix . 'logo',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Clinic can access calendar?', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'clinic_permission',
				'type'    => 'checkbox'
			),			
			array(
				'name' => __( 'Regulatory Body', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'regulatory_body',
				'type'    => 'select',
				'options' => array(
					'GMC' => __( 'GMC', 'cmb' ),
					'GDC' => __( 'GDC', 'cmb' ),
					'NMC'   => __( 'NMC', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Registration Number', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'registration_number',
				'type' => 'text_medium',
			),
			array(
				'name' => __( 'Cosmetic Certificate<span style="color:#df0000;">*</span>', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'cosmetic_certificate',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Insurance/Indemnity<span style="color:#df0000;">*</span>', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'prof_insurance',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Languages', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'languages',
				'type'    => 'multicheck',
				'options' => array(
					'English' => __( 'English', 'cmb' ),
					'French' => __( 'French', 'cmb' ),
					'Spanish'   => __( 'Spanish', 'cmb' ),
					'Ukrainian' => __( 'Ukrainian', 'cmb' ),
					'Russian' => __( 'Russian', 'cmb' ),
				),
				//'inline'  => true,
			),			
			array(
				'name' => __( 'YouTube or Vimeo Video of Yourself', 'cmb' ),
				'desc' => __( 'Link to YouTube or Vimeo Video', 'cmb' ),
				'id'   => $prefix . 'video_url',
				'type' => 'text',
			),
		),
	);

	$meta_boxes['clinic_metabox'] = array(
		'id'         => 'clinic_metabox',
		'title'      => __( 'Clinic Specifications', 'cmb' ),
		'pages'      => array( 'clinic', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name' => __( 'Title', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'title',
				'type'    => 'select',
				'options' => array(
					'Clinic' => __( 'Clinic', 'cmb' ),
					'Prof' => __( 'Prof', 'cmb' ),
					'Dr' => __( 'Dr', 'cmb' ),
					'Mr'   => __( 'Mr', 'cmb' ),
					'Mrs'   => __( 'Mrs', 'cmb' ),
					'Ms'   => __( 'Ms', 'cmb' ),
					'Miss'   => __( 'Miss', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Profession', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'profession',
				'type'    => 'select',
				'options' => array(
					'Doctor' => __( 'Doctor', 'cmb' ),
					'Surgeon' => __( 'Surgeon', 'cmb' ),
					'Dentist'   => __( 'Dentist', 'cmb' ),
					'Nurse'   => __( 'Nurse', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Days Off', 'cmb' ),
				'desc' => __( 'Please choose the days respectively if you choose more than one' ),
				'id'   => $prefix . 'clinic_week_end_day',
				'type'    => 'multicheck',
				'options' => array(
					'Sunday' => __( 'Sunday', 'cmb' ),
					'Monday' => __( 'Monday', 'cmb' ),
					'Tuesday'   => __( 'Tuesday', 'cmb' ),
					'Wednesday'   => __( 'Wednesday', 'cmb' ),
					'Thursday'   => __( 'Thursday', 'cmb' ),
					'Friday'   => __( 'Friday', 'cmb' ),
					'Saturday'   => __( 'Saturday', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Working Hours', 'cmb' ),
				'desc' => __( '<small>Ex: 9:00 AM - 5:00 PM</small>', 'cmb' ),
				'id'   => $prefix . 'clinic_working_hours',
				'type'    => 'text_medium',
			),
			array(
				'name' => __( 'About You', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'about_me',
				'type'    => 'textarea',
			),
			array(
				'name' => __( 'Subspecialties', 'cmb' ),
				'desc' => __( '<small>Ex: General Dermatology, Cosmetic Dermatology</small>', 'cmb' ),
				'id'   => $prefix . 'subspecialties',
				'type'    => 'textarea_small',
			),
			array(
				'name' => __( 'Date of Birth', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'date_of_birth',
				'type'    => 'text_date',
			),
			array(
				'name' => __( 'Logo', 'cmb' ),
				'desc' => __( 'Image size should be 200px * 150px', 'cmb' ),
				'id'   => $prefix . 'logo',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Phone Number', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'phone_number',
				'type' => 'text_medium',
			),
			array(
				'name' => __( 'City', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'city',
				'type'    => 'text_medium',
			),
			array(
				'name' => __( 'Address', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'clinic_location',
				'type'    => 'textarea_small',
			),
			/*array(
				'name' => __( 'Distance', 'cmb' ),
				'desc' => __( 'Distance from the city (Miles)<br><small>If your city is "Nottingham", and you are about 20 miles away from the main city, then set the distance to <strong>20</strong>. If you are in the main city then set the distance to \'<strong>-1</strong>\'. <span style="color:#cd41ff;">You won\'t be listed in the search result if this field is empty</span>.</small>', 'cmb' ),
				'id'   => $prefix . 'distance',
				'type'    => 'text_small',
			),*/
			array(
				'name' => __( 'Additional Location', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'additional_location',
				'type'    => 'textarea_small',
			),
			array(
				'name' => __( 'PIN Code', 'cmb' ),
				'desc' => __( 'It will be used for your reviews', 'cmb' ),
				'id'   => $prefix . 'pin_code',
				'type'    => 'text_small',
			),
			array(
				'name' => __( 'Disabled Parking', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'disabled_parking',
				'type'    => 'select',
				'options' => array(
					'Yes' => __( 'Yes', 'cmb' ),
					'No' => __( 'No', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Parking on Site', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'parking_on_site',
				'type'    => 'select',
				'options' => array(
					'No' => __( 'No', 'cmb' ),
					'Free' => __( 'Free', 'cmb' ),
					'Paid' => __( 'Paid', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Nearest Parking', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'nearest_parking',
				'type'    => 'text_medium',
			),
			array(
				'name' => __( 'Parking Charge', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'parking_charge',
				'type'    => 'text_medium',
			),
			/*array(
				'name' => __( 'Treatments & Procedures', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'treatments',
				'type'    => 'multicheck',
				'options' => get_treatments_list(),
				'inline'  => true,
			),*/
			array(
				'name' => __( 'Regulatory Body', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'regulatory_body',
				'type'    => 'select',
				'options' => array(
					'GMC' => __( 'GMC', 'cmb' ),
					'GDC' => __( 'GDC', 'cmb' ),
					'NMC'   => __( 'NMC', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Registration Number', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'registration_number',
				'type' => 'text_medium',
			),
			array(
				'name' => __( 'Cosmetic Certificate<span style="color:#df0000;">*</span>', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'cosmetic_certificate',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Insurance/Indemnity<span style="color:#df0000;">*</span>', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'prof_insurance',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Languages', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'languages',
				'type'    => 'multicheck',
				'options' => array(
					'English' => __( 'English', 'cmb' ),
					'French' => __( 'French', 'cmb' ),
					'Spanish'   => __( 'Spanish', 'cmb' ),
					'Ukrainian' => __( 'Ukrainian', 'cmb' ),
					'Russian' => __( 'Russian', 'cmb' ),
				),
				//'inline'  => true,
			),			
			array(
				'name' => __( 'YouTube or Vimeo Video of Yourself', 'cmb' ),
				'desc' => __( 'Link to YouTube or Vimeo Video', 'cmb' ),
				'id'   => $prefix . 'video_url',
				'type' => 'text',
			),
		),
	);

	$meta_boxes['claim_metabox'] = array(
		'id'         => 'claim_metabox',
		'title'      => __( 'Claim Page Specifications', 'cmb' ),
		'pages'      => array( 'claims' ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'cmb_styles' => true, // Enqueue the CMB stylesheet on the frontend
		'fields'     => array(
			array(
				'name' => __( 'Title', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'title',
				'type'    => 'select',
				'options' => array(
					'Prof' => __( 'Prof', 'cmb' ),
					'Dr' => __( 'Dr', 'cmb' ),
					'Mr'   => __( 'Mr', 'cmb' ),
					'Mrs'   => __( 'Mrs', 'cmb' ),
					'Ms'   => __( 'Ms', 'cmb' ),
					'Miss'   => __( 'Miss', 'cmb' ),
				),
			),
			array(
				'name' => __( 'Profession', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'profession',
				'type'    => 'select',
				'options' => array(
					'Doctor' => __( 'Doctor', 'cmb' ),
					'Surgeon' => __( 'Surgeon', 'cmb' ),
					'Dentist'   => __( 'Dentist', 'cmb' ),
					'Nurse'   => __( 'Nurse', 'cmb' ),
				),
			),
			array(
				'name' => __( 'About You', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'about_me',
				'type'    => 'textarea',
			),
			array(
				'name' => __( 'Subspecialties', 'cmb' ),
				'desc' => __( '<small>Ex: General Dermatology, Cosmetic Dermatology</small>', 'cmb' ),
				'id'   => $prefix . 'subspecialties',
				'type'    => 'textarea_small',
			),
			array(
				'name' => __( 'Phone Number', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'phone_number',
				'type' => 'text_medium',
			),
			array(
				'name' => __( 'Education', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'education',
				'type'    => 'textarea',
			),
			array(
				'name' => __( 'Address', 'cmb' ),
				'desc' => __( '', 'cmb' ),
				'id'   => $prefix . 'location',
				'type'    => 'textarea_small',
			),
			array(
				'name' => __( 'Logo', 'cmb' ),
				'desc' => __( 'Image size should be 200px * 150px', 'cmb' ),
				'id'   => $prefix . 'logo',
				'type'    => 'file',
			),
			array(
				'name' => __( 'Languages', 'cmb' ),
				'desc' => __( '' ),
				'id'   => $prefix . 'languages',
				'type'    => 'multicheck',
				'options' => array(
					'English' => __( 'English', 'cmb' ),
					'French' => __( 'French', 'cmb' ),
					'Spanish'   => __( 'Spanish', 'cmb' ),
					'Ukrainian' => __( 'Ukrainian', 'cmb' ),
					'Russian' => __( 'Russian', 'cmb' ),
				),
				//'inline'  => true,
			),			
		),
	);

	return $meta_boxes;
}


add_action('add_meta_boxes', 'treatments_specification_metaboxes');
function treatments_specification_post_box() {
	echo '<input type="hidden" name="treatments_specification_noncename" id="treatments_specification_noncename" value="' .
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	global $post;

	?>
	<table style="width:100%;">
		<tr>
			<td><?php _e('Treatments & Procedures', 'cmb');?>:</td>
			<td>
            	<?php
					$treatments = get_post_meta($post->ID, '_dac_treatments_procedures', true);
					if($treatments) {
						$treatments_arr = explode(',', $treatments);
					}
                ?>
            	<!--<input type="text" name="treatments_procedures" value="<?php //echo get_post_meta($post->ID, '_dac_treatments_procedures', true); ?>" class="widefat" />-->
                <select name="treatments_procedures[]" id="treatments_procedures" multiple="multiple" style="min-height:200px;">
                    <option value="Acne" <?php if( in_array("Acne", $treatments_arr) ) echo 'selected="selected"'; ?>>Acne</option>
                    <option value="Acne scars" <?php if( in_array("Acne scars", $treatments_arr) ) echo 'selected="selected"'; ?>>Acne scars</option>
                    <option value="Aged hands" <?php if( in_array("Aged hands", $treatments_arr) ) echo 'selected="selected"'; ?>>Aged hands</option>
                    <option value="Anti-wrinkle Injections" <?php if( in_array("Anti-wrinkle Injections", $treatments_arr) ) echo 'selected="selected"'; ?>>Anti-wrinkle Injections</option>
                    <option value="Aqualyx" <?php if( in_array("Aqualyx", $treatments_arr) ) echo 'selected="selected"'; ?>>Aqualyx</option>
                    <option value="Asymmetrical lips" <?php if( in_array("Asymmetrical lips", $treatments_arr) ) echo 'selected="selected"'; ?>>Asymmetrical lips</option>

                    <option value="Baggy eyes" <?php if( in_array("Baggy eyes", $treatments_arr) ) echo 'selected="selected"'; ?>>Baggy eyes</option>
                    <option value="Bumpy nose" <?php if( in_array("Bumpy nose", $treatments_arr) ) echo 'selected="selected"'; ?>>Bumpy nose</option>
                    <option value="Burns scars" <?php if( in_array("Burns scars", $treatments_arr) ) echo 'selected="selected"'; ?>>Burns scars</option>

                    <option value="Carboxytherapy" <?php if( in_array("Carboxytherapy", $treatments_arr) ) echo 'selected="selected"'; ?>>Carboxytherapy</option>
                    <option value="Cellulite" <?php if( in_array("Cellulite", $treatments_arr) ) echo 'selected="selected"'; ?>>Cellulite</option>
                    <option value="Cheek Fillers" <?php if( in_array("Cheek Fillers", $treatments_arr) ) echo 'selected="selected"'; ?>>Cheek Fillers</option>
                    <option value="Chemical peels" <?php if( in_array("Chemical peels", $treatments_arr) ) echo 'selected="selected"'; ?>>Chemical peels</option>
                    <option value="Chin and jaw line contouring" <?php if( in_array("Chin and jaw line contouring", $treatments_arr) ) echo 'selected="selected"'; ?>>Chin and jaw line contouring</option>
                    <option value="Coolsculpting" <?php if( in_array("Coolsculpting", $treatments_arr) ) echo 'selected="selected"'; ?>>Coolsculpting</option>
                    <option value="Crows feet" <?php if( in_array("Crows feet", $treatments_arr) ) echo 'selected="selected"'; ?>>Crows feet</option>
                    <option value="Cryotherapy" <?php if( in_array("Cryotherapy", $treatments_arr) ) echo 'selected="selected"'; ?>>Cryotherapy</option>

                    <option value="Dark eyes circles" <?php if( in_array("Dark eyes circles", $treatments_arr) ) echo 'selected="selected"'; ?>>Dark eyes circles</option>
                    <option value="Deep lines" <?php if( in_array("Deep lines", $treatments_arr) ) echo 'selected="selected"'; ?>>Deep lines</option>
                    <option value="Dermal fillers" <?php if( in_array("Dermal fillers", $treatments_arr) ) echo 'selected="selected"'; ?>>Dermal fillers</option>
                    <option value="Double chin" <?php if( in_array("Double chin", $treatments_arr) ) echo 'selected="selected"'; ?>>Double chin</option>
                    <option value="Dry hands" <?php if( in_array("Dry hands", $treatments_arr) ) echo 'selected="selected"'; ?>>Dry hands</option>
                    <option value="Dry skin" <?php if( in_array("Dry skin", $treatments_arr) ) echo 'selected="selected"'; ?>>Dry skin</option>

                    <option value="Eyebrow lift" <?php if( in_array("Eyebrow lift", $treatments_arr) ) echo 'selected="selected"'; ?>>Eyebrow lift</option>
                    <option value="Excessive sweating" <?php if( in_array("Excessive sweating", $treatments_arr) ) echo 'selected="selected"'; ?>>Excessive sweating</option>

                    <option value="Fine lines and wrinkles" <?php if( in_array("Fine lines and wrinkles", $treatments_arr) ) echo 'selected="selected"'; ?>>Fine lines and wrinkles</option>
                    <option value="Frown lines" <?php if( in_array("Frown lines", $treatments_arr) ) echo 'selected="selected"'; ?>>Frown lines</option>

                    <option value="Genuine Dermaroller Treatments" <?php if( in_array("Genuine Dermaroller Treatments", $treatments_arr) ) echo 'selected="selected"'; ?>>Genuine Dermaroller Treatments</option>

                    <option value="Hair loss" <?php if( in_array("Hair loss", $treatments_arr) ) echo 'selected="selected"'; ?>>Hair loss</option>
                    <option value="Hands rejuvenation" <?php if( in_array("Hands rejuvenation", $treatments_arr) ) echo 'selected="selected"'; ?>>Hands rejuvenation</option>
                    <option value="Hyper pigmentation" <?php if( in_array("Hyper pigmentation", $treatments_arr) ) echo 'selected="selected"'; ?>>Hyper pigmentation</option>
                    <option value="Hyperpigmentation – obagi , recell, micro-needling , skin peels" <?php if( in_array("Hyperpigmentation – obagi , recell, micro-needling , skin peels", $treatments_arr) ) echo 'selected="selected"'; ?>>Hyperpigmentation – obagi , recell, micro-needling , skin peels</option>

                    <option value="Jaw line reduction" <?php if( in_array("Jaw line reduction", $treatments_arr) ) echo 'selected="selected"'; ?>>Jaw line reduction</option>
                    <option value="Jowls" <?php if( in_array("Jowls", $treatments_arr) ) echo 'selected="selected"'; ?>>Jowls</option>

                    <option value="Laser" <?php if( in_array("Laser", $treatments_arr) ) echo 'selected="selected"'; ?>>Laser</option>
                    <option value="Lip fillers" <?php if( in_array("Lip fillers", $treatments_arr) ) echo 'selected="selected"'; ?>>Lip fillers</option>
                    <option value="Loose skin" <?php if( in_array("Loose skin", $treatments_arr) ) echo 'selected="selected"'; ?>>Loose skin</option>

                    <option value="Marionette lines" <?php if( in_array("Marionette lines", $treatments_arr) ) echo 'selected="selected"'; ?>>Marionette lines</option>
                    <option value="Medical micro-dermabrasion" <?php if( in_array("Medical micro-dermabrasion", $treatments_arr) ) echo 'selected="selected"'; ?>>Medical micro-dermabrasion</option>
                    <option value="Medical Micro-needling" <?php if( in_array("Medical Micro-needling", $treatments_arr) ) echo 'selected="selected"'; ?>>Medical Micro-needling</option>
                    <option value="Mesotherapy" <?php if( in_array("Mesotherapy", $treatments_arr) ) echo 'selected="selected"'; ?>>Mesotherapy</option>

                    <option value="Naso-labial lines " <?php if( in_array("Naso-labial lines", $treatments_arr) ) echo 'selected="selected"'; ?>>Naso-labial lines </option>
                    <option value="Non-Surgical Face Lift" <?php if( in_array("Non-Surgical Face Lift", $treatments_arr) ) echo 'selected="selected"'; ?>>Non-Surgical Face Lift</option>
                    <option value="Non-Surgical Rhinoplasty" <?php if( in_array("Non-Surgical Rhinoplasty", $treatments_arr) ) echo 'selected="selected"'; ?>>Non-Surgical Rhinoplasty</option>

                    <option value="Oily skin" <?php if( in_array("Oily skin", $treatments_arr) ) echo 'selected="selected"'; ?>>Oily skin</option>
                    <option value="Oxygen Therapy" <?php if( in_array("Oxygen Therapy", $treatments_arr) ) echo 'selected="selected"'; ?>>Oxygen Therapy</option>

                    <option value="PDO threads" <?php if( in_array("PDO threads", $treatments_arr) ) echo 'selected="selected"'; ?>>PDO threads</option>
                    <option value="Prone to acne skin" <?php if( in_array("Prone to acne skin", $treatments_arr) ) echo 'selected="selected"'; ?>>Prone to acne skin</option>
                    <option value="PRP" <?php if( in_array("PRP", $treatments_arr) ) echo 'selected="selected"'; ?>>PRP</option>

                    <option value="Radio frequency" <?php if( in_array("Radio frequency", $treatments_arr) ) echo 'selected="selected"'; ?>>Radio frequency</option>
                    <option value="Recell" <?php if( in_array("Recell", $treatments_arr) ) echo 'selected="selected"'; ?>>Recell</option>
                    <option value="Rosea" <?php if( in_array("Rosea", $treatments_arr) ) echo 'selected="selected"'; ?>>Rosea</option>

                    <option value="Scars" <?php if( in_array("Scars", $treatments_arr) ) echo 'selected="selected"'; ?>>Scars</option>
                    <option value="Silhouette threads lift" <?php if( in_array("Silhouette threads lift", $treatments_arr) ) echo 'selected="selected"'; ?>>Silhouette threads lift</option>
                    <option value="Skin Peels" <?php if( in_array("Skin Peels", $treatments_arr) ) echo 'selected="selected"'; ?>>Skin Peels</option>
                    <option value="Small, luck of volume lip" <?php if( in_array("Small, luck of volume lip", $treatments_arr) ) echo 'selected="selected"'; ?>>Small, luck of volume lip</option>
                    <option value="Smoker lines" <?php if( in_array("Smoker lines", $treatments_arr) ) echo 'selected="selected"'; ?>>Smoker lines</option>
                    <option value="Solar keratosis" <?php if( in_array("Solar keratosis", $treatments_arr) ) echo 'selected="selected"'; ?>>Solar keratosis</option>
                    <option value="Stretch marks" <?php if( in_array("Stretch marks", $treatments_arr) ) echo 'selected="selected"'; ?>>Stretch marks</option>

                    <option value="Tear through treatments" <?php if( in_array("Tear through treatments", $treatments_arr) ) echo 'selected="selected"'; ?>>Tear through treatments</option>
                    <option value="Thread veins" <?php if( in_array("Thread veins", $treatments_arr) ) echo 'selected="selected"'; ?>>Thread veins</option>
                    <option value="Threads vein treatment" <?php if( in_array("Threads vein treatment", $treatments_arr) ) echo 'selected="selected"'; ?>>Threads vein treatment</option>
                    <option value="Treatment for hyperhidrosis" <?php if( in_array("Treatment for hyperhidrosis", $treatments_arr) ) echo 'selected="selected"'; ?>>Treatment for hyperhidrosis</option>

                    <option value="Unhappy with nose shape" <?php if( in_array("Unhappy with nose shape", $treatments_arr) ) echo 'selected="selected"'; ?>>Unhappy with nose shape</option>
                    <option value="Unwanted hair" <?php if( in_array("Unwanted hair", $treatments_arr) ) echo 'selected="selected"'; ?>>Unwanted hair</option>


                    <option value="Vitiligo" <?php if( in_array("Vitiligo", $treatments_arr) ) echo 'selected="selected"'; ?>>Vitiligo</option>
                    <option value="Volume loss cheeks" <?php if( in_array("Volume loss cheeks", $treatments_arr) ) echo 'selected="selected"'; ?>>Volume loss cheeks</option>
                    <option value="Volume loss face" <?php if( in_array("Volume loss face", $treatments_arr) ) echo 'selected="selected"'; ?>>Volume loss face</option>
                    <option value="Volume loss in lips" <?php if( in_array("Volume loss in lips", $treatments_arr) ) echo 'selected="selected"'; ?>>Volume loss in lips</option>

                    <option value="Wrinkle on hands" <?php if( in_array("Wrinkle on hands", $treatments_arr) ) echo 'selected="selected"'; ?>>Wrinkle on hands</option>
                </select>
                <br />
                <small>Hold 'ctrl' key to choose multiple treatments</small>
            </td>
		</tr>
	</table>
	<?php
}

function treatments_specification_metaboxes() {
	add_meta_box('treatments_specification', __('Treatments & Procedures', 'cmb'), 'treatments_specification_post_box', array('professional', 'clinic'), 'normal', 'high');
}

add_action( 'save_post', 'treatments_specification_add_or_save', 10, 2 );
function treatments_specification_add_or_save($post_id, $post){
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if (!isset($_POST['treatments_specification_noncename']) || !wp_verify_nonce($_POST['treatments_specification_noncename'], plugin_basename(__FILE__))) {
		return $post->ID;
	}           
	
	  // Check permissions
	  if ( ('professional' == $_POST['post_type']) || ('clinic' == $_POST['post_type']) ) {
		if ( !current_user_can( 'edit_posts', $post_id ) )
			return;
	  }
	  
	  
	if ($_POST['treatments_procedures']) {
		add_post_meta($post_id, '_dac_treatments_procedures', join(",", $_POST['treatments_procedures']), TRUE) or update_post_meta($post_id, '_dac_treatments_procedures', join(",", $_POST['treatments_procedures']));
	} else {
		delete_post_meta($post_id, '_dac_treatments_procedures');
	}	
}
?>