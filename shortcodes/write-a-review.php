<?php
function dac_write_review_shortcode( $content = null ) {
	/*extract(shortcode_atts(array(
		'profile_id' => '',
	), $atts));*/

	ob_start();
	?>
    <div id="write_review_wrap">
    	<h1><?php the_title(); ?></h1>
        <div class="review_title_border">&nbsp;</div>
        
    	<form action="" method="post" id="review_form">
        	<p>
            	<label for="review_code">Secret Code</label>
                <input type="text" name="review_code" id="review_code" />
            </p>
        	<p>
            	<label for="name">Name</label>
                <input type="text" name="name" id="name" />
            </p>
        	<p>
            	<label for="email">Email</label>
                <input type="text" name="email" id="email" />
            </p>
        	<p>
            	<label for="phone">Phone</label>
                <input type="text" name="phone" id="phone" />
            </p>
        	<p class="rating">
            	Rating out of 5 stars: 
                <span class="rating_stars">
                    <input class="submit-star {half:true}" type="radio" name="review_rating" value="1"/>
                    <input class="submit-star {half:true}" type="radio" name="review_rating" value="2"/>
                    <input class="submit-star {half:true}" type="radio" name="review_rating" value="3"/>
                    <input class="submit-star {half:true}" type="radio" name="review_rating" value="4"/>
                    <input class="submit-star {half:true}" type="radio" name="review_rating" value="5"/>
                </span>
            </p>
            <p>
            	<label for="review_message">Tell Us About Your Experience*</label>
            	<textarea name="review_message" id="review_message"></textarea>
            </p>
            <p class="review_btn">
            	<input type="submit" id="leave_review" name="leave_review" value="LEAVE REVIEW" />
            </p>
        </form>
    </div>
    <script type="text/javascript">
		jQuery( document ).ready(function() {
			jQuery('.submit-star').rating({
				callback: function(value, link) {
							  //alert(value);
							  jQuery('#rating_count').remove();
							  jQuery('.review_btn').prepend('<input type="hidden" id="rating_count" name="rating_count" value="'+value+'" />');
						  }
			});
			
			/*jQuery("#review_form").submit(function() {
				//alert("Submitted");
			});*/
		});
	</script>
	<?php	
	$user_reviews = ob_get_contents();
	ob_end_clean();	
	return $user_reviews;
}
add_shortcode('write_review', 'dac_write_review_shortcode');
?>