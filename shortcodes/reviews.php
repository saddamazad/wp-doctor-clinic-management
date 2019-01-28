<?php
function dac_profile_reviews_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'profile_id' => '',
	), $atts));

	ob_start();
	if ( isset($_POST['profile_id']) ) {
		$profile_id = $_POST['profile_id'];
		global $wpdb;
		$reviews_table = $wpdb->base_prefix . "dac_reviews";
		$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $reviews_table WHERE profile_id=$profile_id AND status='publish'"), ARRAY_A );
		//print_r($results);

		if( count($results) > 0 ) {
			echo '<div class="reviews_name update">';
			global $post;
			$count = 1;
			foreach($results as $review) {
			if( $count%2 == 0 ) $last = 'et_column_last';
			else $last = '';
		?>
            <div class="one_half <?php echo $last; ?>">
                <h3><?php echo $review['name']; ?></h3>
                <div class="rating">
                	<?php
						$star_html = '';
						for($i=0; $i<$review['rating']; $i++) {
							$star_html .= '<img src="' . DAC_URL . 'images/star.png" alt="" />';
						}
                    ?>
                    <h4>Rating Out of 5 Stars: <span class="review-star-rating <?php echo 'star-'.$review['rating']; ?>"><?php echo $star_html; ?></span></h4>
                </div>
                <p class="review_dtl" style="padding-bottom: 0;"><strong style="font-size: 16px;">Review Details:</strong></p>
                <p><?php echo nl2br($review['message']); ?></p>
                <?php //echo get_the_excerpt(); ?>
                <!--<a href="<?php //echo get_permalink(); ?>">Read More....</a>-->
            </div>
            
		<?php
			if( $count%2 == 0 ) echo '<div class="clear"></div>';
			$count++;
			}

			echo '</div>';
		} else {
			echo '<h3>There are currently no Reviews to View</h3>';
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
	
	$user_reviews = ob_get_contents();
	ob_end_clean();	
	return $user_reviews;
}
add_shortcode('profile_reviews', 'dac_profile_reviews_shortcode');
?>