<?php

add_filter( 'show_admin_bar', '__return_false');

function pwp_remove_recent_comments_style() {  
        global $wp_widget_factory;  
        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );  
    }  
add_action( 'widgets_init', 'pwp_remove_recent_comments_style' );

//rss tylko z newsami
add_filter( 'pre_get_posts', 'pwp_feed_category' );
function pwp_feed_category( $query ) {
    if( $query->is_feed ) {
	$query->set( 'cat', '1' );
    }
    return $query;
}

//przekierowanie do startowej jesli puste wyszukiwanie
add_action( 'pre_get_posts', 'pwp_blank_search' );
function pwp_blank_search( $query ) {
    if( isset( $_GET['s'] ) && $_GET['s'] == '' ) {
	wp_redirect( home_url() );
	exit;
    }
}

//common admin template
$admin_template = new Admin(
    array(
        'theme_css' => 'pwp-style.css',
        'remove_menu' => array(
            'link-manager.php',
            'tools.php',
            'edit-comments.php',
	    //'upload.php',
	    'edit.php?post_type=form',
	    'profile.php',
	    'customize.php'
        ),
        'remove_admin_bar' => array(
            'wp-logo',
            'updates',
            'edit',
            'new-content',
            'comments'
        ),
    )
);
$admin_template->add_theme_style();
//$admin_template->remove_menu_items();
$admin_template->remove_bar_links();

//site config
$site = new Site(
    array(
        'remove_head_element' => array(
	    'rsd_link',
	    'feed_links',
	    'feed_links_extra',
	    'wlwmanifest_link',
	    'wp_generator',
	    'start_post_rel_link',
	    'index_rel_link',
	    'adjacent_posts_rel_link',
            'adjacent_posts_rel_link_wp_head'
         )
    )
);
$site->remove_head_element();


//Gets post cat slug and looks for single-[cat slug].php and applies it
add_filter('single_template', create_function(
	'$the_template',
	'foreach( (array) get_the_category() as $cat ) {
		if ( file_exists(TEMPLATEPATH . "/single-{$cat->slug}.php") )
		return TEMPLATEPATH . "/single-{$cat->slug}.php"; }
	return $the_template;' )
);

//szablony dla single custom post type
add_filter('single_template','pwp_cpt_page_template');
function pwp_cpt_page_template($the_template){
    global $post;
    if(file_exists(TEMPLATEPATH . '/single-'.$post->post_type.'.php')){
	return TEMPLATEPATH . '/single-'.$post->post_type.'.php';
    }
    return $the_template;
}
