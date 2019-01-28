<?php
function dac_staff_or_clinic_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'profile_id' => '',
	), $atts));

	ob_start();
	if ( isset($_POST['profile_id']) ) {
		$profile_id = $_POST['profile_id'];
		$user_id = get_post_meta( $profile_id, '_dac_user_id', true );
		$user_type = get_post_type( $profile_id );

		if($user_type == 'professional') {
	?>
    <div class="reviews_name staff_clinic">
        <div class="one_half">
			<?php if(get_post_meta($profile_id, '_dac_associated_clinic', true)) { ?>
            <h3><?php echo get_the_title(get_post_meta($profile_id, '_dac_associated_clinic', true)); ?></h3>
            <?php } ?>
            <!--<div class="rating">
            <h4>Title if Available</h4>
            </div>-->
            <h5>Information:</h5>
            <?php echo get_post_meta($profile_id, '_dac_about_practice', true); ?>
        </div>
    
        <!--<div class="one_half et_column_last">
        <h3>Staff or Clinic Name if Any</h3>
        <div class="rating">
        <h4>Title if Available</h4>
        </div>
        <h5>Information:</h5>
        <p>Pulled from About Information Entered</p>
        </div>-->
    
    </div>
    <?php }
		if($user_type == 'clinic') {
		$professional_args = array(
					'post_type' => 'professional',
					'posts_per_page' => -1,
					'post_status' => array('draft', 'publish'),
					'order'	=> 'ASC',
					'orderby' => 'title',
					'meta_query' => array(
						array(
							'key'     => '_dac_associated_clinic',
							'value'   => $profile_id,
							'compare' => '=',
						),
					),

					//'post__in' => $postIds
					
				);
		$professionals = new WP_Query( $professional_args );	
		if($professionals->have_posts()) {
			echo '<div class="reviews_name staff_clinic">';
			global $post;
			$count = 1;
			while($professionals->have_posts()): $professionals->the_post();
			if( $count%2 == 0 ) $last = 'et_column_last';
			else $last = '';
		?>
            <div class="one_half <?php echo $last; ?>">
                <h3>
					<?php echo get_the_title(); ?>
                    <?php
						if(has_post_thumbnail()){	
							$prof_image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'post-thumb');
							echo '<img src="'.$prof_image[0].'" align="" width="48" height="48" />';
						}
					?>
                </h3>
                <?php if(get_post_meta($post->ID, '_dac_profession', true)) { ?>
                <div class="rating">
                <h4><?php echo get_post_meta($post->ID, '_dac_profession', true); ?></h4>
                </div>
                <?php } ?>
                <h5>Information:</h5>
                <?php echo get_post_meta($post->ID, '_dac_about_me', true); ?>
            </div>
        <?php
			$count++;
			endwhile;
			wp_reset_postdata();

			echo '</div>';
		} else {
			echo 'No Staff Listed.';
		}
	}
	?>
    <script type="text/javascript">
		jQuery( document ).ready(function() {
			<?php if($user_type == 'professional') { ?>
			jQuery(".et_pb_tab_2 a").text('Clinic');
			<?php } ?>
			<?php if($user_type == 'clinic') { ?>
			jQuery(".et_pb_tab_2 a").text('Staff');
			<?php } ?>
		});
	</script>
	<?php	
	}
	
	$user_profile = ob_get_contents();
	ob_end_clean();	
	return $user_profile;	
}
add_shortcode('staff_or_clinic', 'dac_staff_or_clinic_shortcode');
?>