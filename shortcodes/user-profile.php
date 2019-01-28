<?php
function dac_user_profile_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'profile_id' => '',
	), $atts));

	ob_start();
	if ( isset($_POST['profile_id']) ) {
		$profile_id = $_POST['profile_id'];
		$user_id = get_post_meta( $profile_id, '_dac_user_id', true );
		$user_type = get_post_type( $profile_id );
	?>
    <div class="dermatologist_tab_section">
        <div class="two_third">
            <div class="Search_calint clearfix">
            	<?php
					if ( has_post_thumbnail($profile_id) ) {
						$image_url = wp_get_attachment_image_src( get_post_thumbnail_id($profile_id), 'profile-size');
						echo '<img src="' . $image_url[0] . '" alt="" />';
					} else {
						echo '<img src="' . DAC_URL . 'images/unknown_user.png" width="200" height="200" />';
					}
					
					if($user_type == 'professional') {
						echo '<h3>'.get_post_meta($profile_id, '_dac_title', true).' '.get_the_title($profile_id).' – <span class="martin_der">'.get_post_meta($profile_id, '_dac_profession', true).'</span></h3>';
					} elseif($user_type == 'clinic') {
						echo '<h3>'.get_the_title($profile_id).'</h3>';
					}
					
					if($user_type == 'professional') {
						echo '<h5>'.get_the_title(get_post_meta($profile_id, '_dac_associated_clinic', true)).'</h5>';
					}
                ?>
                <h4>Subspecialties:</h4>
                <?php
                	echo '<p>'.get_post_meta($profile_id, '_dac_subspecialties', true).'</p>';
				?>
                <p><a class="view_profile fancybox-iframe" href="<?php echo DAC_URL . 'shortcodes/appointment-form.php?pid='.$profile_id; ?>"><i class="fa fa-check-circle"></i> REQUEST AN APPOINTMENT </a></p>
				<form action="<?php echo DAC_HOME.'request-appointment/'; ?>" method="post" name="appointment_<?php echo $profile_id; ?>_form">
                    <input type="hidden" value="<?php echo $profile_id; ?>" name="profile_id" />
                    <input type="hidden" value="<?php echo get_post_meta($profile_id, '_dac_days_full', true); ?>" name="days_full" />
                </form>
            </div>
        </div>
        
        <div class="one_third et_column_last">
            <div class="Uploaded_photo">
				<?php
                    if( get_post_meta($profile_id, '_dac_logo', true) ) {
                        echo '<img src="'.get_post_meta($profile_id, '_dac_logo', true).'" align="" />';
                    }
                ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    
    <div class="two_third">
        <div class="dermatologist_tab_section_two">
        	<?php
				/*$disabled_parking = get_the_author_meta( 'disabled_parking', $user_id );
				$parking_on_site = get_the_author_meta( 'parking_on_site', $user_id );
				$nearest_parking = get_the_author_meta( 'nearest_parking', $user_id );
				$parking_charge = get_the_author_meta( 'parking_charge', $user_id );*/
				$disabled_parking = get_post_meta( $profile_id, '_dac_disabled_parking', true );
				$parking_on_site = get_post_meta( $profile_id, '_dac_parking_on_site', true );
				$nearest_parking = get_post_meta( $profile_id, '_dac_nearest_parking', true );
				$parking_charge = get_post_meta( $profile_id, '_dac_parking_charge', true );
			?>
            <h2>Days of Week Available:</h2>
            <?php
				if($user_type == 'professional') {
					$weekEnds = get_post_meta($profile_id, '_dac_week_end_day', true);
				} elseif($user_type == 'clinic') {
					$weekEnds = get_post_meta($profile_id, '_dac_clinic_week_end_day', true);
				}
				$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
				$unavailable_days = array();
				foreach($weekEnds as $weekEnd) {
					$unavailable_days[] = array_search($weekEnd, $days);
				}
				
				for($i=0; $i<count($unavailable_days); $i++) {
					unset($days[$unavailable_days[$i]]);
				}
			?>
            <h5><?php echo join(", ", array_unique($days)); ?></h5>
            <h3>Address:</h3>
            <?php
				if($user_type == 'professional') {
					$location = get_post_meta($profile_id, '_dac_location', true);
            		echo '<h5>'.get_post_meta($profile_id, '_dac_location', true).'</h5>';
				} elseif($user_type == 'clinic') {
					$location = get_post_meta($profile_id, '_dac_clinic_location', true);
            		echo '<h5>'.get_post_meta($profile_id, '_dac_clinic_location', true).'</h5>';
				}
			?>
            <div class="g-map" style="height: 270px; overflow: hidden; width: 97%;">
				<iframe src="<?php echo DAC_URL; ?>shortcodes/google-map.php?location=<?php echo $location; ?>"></iframe>
			</div>

            <h3>Disabled Parking Available:</h3>
            <h5><?php echo $disabled_parking; ?></h5>
            <h3>On-Site Parking:</h3>
			<h5><?php echo $parking_on_site; ?></h5>
            <h3>Nearest Parking:</h3>
			<h5><?php echo $nearest_parking; ?></h5>
            <h3>Parking Charge:</h3>
			<h5><?php echo $parking_charge; ?></h5>

            <h3>Languages:</h3>
			<h5><?php echo join(", ", get_post_meta($profile_id, '_dac_languages', true)); ?></h5>
        </div>
    </div>
    <div class="one_third et_column_last">
            <?php
				if( get_post_meta($profile_id, '_dac_video_url', true) ) {
					echo '<div class="video_area">';
					if( strpos(get_post_meta($profile_id, '_dac_video_url', true), 'youtube') !== false ) {
						echo '<iframe width="420" height="315" src="'.get_post_meta($profile_id, '_dac_video_url', true).'" frameborder="0" allowfullscreen></iframe>';
					} elseif( strpos(get_post_meta($profile_id, '_dac_video_url', true), 'vimeo') !== false ) {
						echo '<iframe src="'.get_post_meta($profile_id, '_dac_video_url', true).'" width="420" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
					}
					echo '</div>';
				}
			?>
    </div>
    <div class="clear"></div>
	<?php	
	}
	
	$user_profile = ob_get_contents();
	ob_end_clean();	
	return $user_profile;	
}
add_shortcode('user_profile', 'dac_user_profile_shortcode');
?>