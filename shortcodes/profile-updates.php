<?php
function dac_profile_updates_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'profile_id' => '',
	), $atts));

	ob_start();
	if ( isset($_POST['profile_id']) ) {
		$profile_id = $_POST['profile_id'];
		$user_id = get_post_meta( $profile_id, '_dac_user_id', true );
		//$user_type = get_post_type( $profile_id );
		//if($user_type == 'professional')
		$updates_args = array(
					'post_type' => 'post',
					'posts_per_page' => 10,
					'post_status' => array('draft', 'publish'),
					'order'	=> 'DESC',
					'orderby' => 'date',
					'author' => $user_id
				);
		$profile_updates = new WP_Query( $updates_args );
		if($profile_updates->have_posts()) {
			echo '<div class="reviews_name update">';
			global $post;
			$count = 1;
			while($profile_updates->have_posts()): $profile_updates->the_post();
			if( $count%2 == 0 ) $last = 'et_column_last';
			else $last = '';
		?>
            <div class="one_half <?php echo $last; ?>">
                <h3><?php echo get_the_title(); ?></h3>
                <div class="rating">
                    <h4>Posted On: <?php echo get_the_date(); ?></h4>
                </div>
                <?php echo get_the_excerpt(); ?>
                <!--<a href="<?php //echo get_permalink(); ?>">Read More....</a>-->
            </div>
            
		<?php
			if( $count%2 == 0 ) echo '<div class="clear"></div>';
			$count++;
			endwhile;
			wp_reset_postdata();

			echo '</div>';
		} else {
			echo '<h3>There are no updates available</h3>';
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
	
	$user_profile_updates = ob_get_contents();
	ob_end_clean();	
	return $user_profile_updates;
}
add_shortcode('profile_updates', 'dac_profile_updates_shortcode');
?>