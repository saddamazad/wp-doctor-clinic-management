<?php
add_action( 'init', 'register_dac_custompost_type' );
function register_dac_custompost_type() {

	$claim_labels = array(
		'name' => _x('Page Claim', 'Claim post name', 'DAC'),
		'singular_name' => _x('Claim', 'Claim singular name', 'DAC'),
		'add_new' => _x('Add New', 'Claim', 'DAC'),
		'add_new_item' => __('Add New Claim', 'DAC'),
		'edit_item' => __('Edit Claim', 'DAC'),
		'new_item' => __('New Claim', 'DAC'),
		'view_item' => __('View Claim', 'DAC'),
		'search_items' => __('Search Claims', 'DAC'),
		'not_found' => __('No Claims Found', 'DAC'),
		'not_found_in_trash' => __('No Claims Found in Trash', 'DAC'),
		'parent_item_colon' => ''
	);

	register_post_type('claims', array('labels' => $claim_labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
            'map_meta_cap' => true,			
			'hierarchical' => false,
			'publicly_queryable' => true,
			'query_var' => true,
			'exclude_from_search' => false,
			'rewrite' => array('slug' => 'claims'),
			'show_in_nav_menus' => false,
			'supports' => array('title', 'thumbnail'),
			'taxonomies' => array('category')
		)
	);

	$labels = array(
		'name' => _x('Treatments', 'Treatment name', 'DAC'),
		'singular_name' => _x('Treatment', 'Treatment singular name', 'DAC'),
		'add_new' => _x('Add New', 'Specialty', 'DAC'),
		'add_new_item' => __('Add New Treatment', 'DAC'),
		'edit_item' => __('Edit Treatment', 'DAC'),
		'new_item' => __('New Treatment', 'DAC'),
		'view_item' => __('View Treatment', 'DAC'),
		'search_items' => __('Search Treatments', 'DAC'),
		'not_found' => __('No Treatments Found', 'DAC'),
		'not_found_in_trash' => __('No Treatments Found in Trash', 'DAC'),
		'parent_item_colon' => ''
	);

	register_post_type('specialties', array('labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
            'map_meta_cap' => true,			
			'hierarchical' => false,
			'publicly_queryable' => true,
			'query_var' => true,
			'exclude_from_search' => false,
			'rewrite' => array('slug' => 'treatment'),
			'show_in_nav_menus' => false,
			'supports' => array('title',  'editor', 'page-attributes'),
			'taxonomies' => array('category')
		)
	);

	$clinic_labels = array(
		'name' => _x('Clinics', 'Clinic name', 'DAC'),
		'singular_name' => _x('Clinic', 'Clinic singular name', 'DAC'),
		'add_new' => _x('Add New', 'Clinic', 'DAC'),
		'add_new_item' => __('Add New Clinic', 'DAC'),
		'edit_item' => __('Edit Clinic', 'DAC'),
		'new_item' => __('New Clinic', 'DAC'),
		'view_item' => __('View Clinic', 'DAC'),
		'search_items' => __('Search Clinic', 'DAC'),
		'not_found' => __('No Clinic Found', 'DAC'),
		'not_found_in_trash' => __('No Clinic Found in Trash', 'DAC'),
		'parent_item_colon' => ''
	);

	register_post_type('clinic', array('labels' => $clinic_labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
            'map_meta_cap' => true,			
			'hierarchical' => false,
			'publicly_queryable' => true,
			'query_var' => true,
			'exclude_from_search' => false,
			'rewrite' => array('slug' => 'clinic'),
			'show_in_nav_menus' => false,
			'supports' => array('title',  'editor', 'thumbnail', 'revisions'),
			//'menu_icon' => DAC_URL . 'images/icon-clinic.png'
		)
	);
	
	$professional_labels = array(
		'name' => _x('Professionals', 'Professional name', 'DAC'),
		'singular_name' => _x('Professional', 'Professional singular name', 'DAC'),
		'add_new' => _x('Add New', 'Professional', 'DAC'),
		'add_new_item' => __('Add New Professional', 'DAC'),
		'edit_item' => __('Edit Professional', 'DAC'),
		'new_item' => __('New Professional', 'DAC'),
		'view_item' => __('View Professional', 'DAC'),
		'search_items' => __('Search Professional', 'DAC'),
		'not_found' => __('No Professional Found', 'DAC'),
		'not_found_in_trash' => __('No Professional Found in Trash', 'DAC'),
		'parent_item_colon' => ''
	);

	register_post_type('professional', array('labels' => $professional_labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
            'map_meta_cap' => true,			
			'hierarchical' => false,
			'publicly_queryable' => true,
			'query_var' => true,
			'exclude_from_search' => false,
			'rewrite' => array('slug' => 'professional'),
			'show_in_nav_menus' => false,
			'supports' => array('title',  'editor', 'thumbnail', 'revisions'),
			//'menu_icon' => DAC_URL . 'images/icon-professional.png'
		)
	);

	$cosmetic_labels = array(
		'name' => _x('Cosmetic News', 'Post name', 'DAC'),
		'singular_name' => _x('Cosmetic News', 'Post singular name', 'DAC'),
		'add_new' => _x('Add New', 'Professional', 'DAC'),
		'add_new_item' => __('Add New Article', 'DAC'),
		'edit_item' => __('Edit Article', 'DAC'),
		'new_item' => __('New Article', 'DAC'),
		'view_item' => __('View Article', 'DAC'),
		'search_items' => __('Search Article', 'DAC'),
		'not_found' => __('No Article Found', 'DAC'),
		'not_found_in_trash' => __('No Article Found in Trash', 'DAC'),
		'parent_item_colon' => ''
	);

	register_post_type('cosmetic_news', array('labels' => $cosmetic_labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
            'map_meta_cap' => true,			
			'hierarchical' => false,
			'publicly_queryable' => true,
			'query_var' => true,
			'exclude_from_search' => false,
			'rewrite' => array('slug' => 'cosmetic-news'),
			'show_in_nav_menus' => false,
			'supports' => array('title',  'editor', 'thumbnail', 'revisions'),
			//'menu_icon' => DAC_URL . 'images/icon-professional.png'
		)
	);
}

add_action('init', 'treatments_add_default_boxes'); 
function treatments_add_default_boxes() {
    register_taxonomy_for_object_type('category', 'specialties');
    //register_taxonomy_for_object_type('post_tag', 'specialties');
    register_taxonomy_for_object_type('category', 'claims');
}
?>