<?php

class Event extends Module {

    static $instance = null;

    /**
     * Inicjalizacja modulu
     * singleton
     * @return Cart
     */
    static function init() {
	if( is_null( self::$instance ) ) {
            self::$instance = new Event();
	}
        return self::$instance;
    }


    public function __construct() {

	parent::__construct();

        self::register_post_type();
        self::register_metabox();
    }

    private static function register_post_type() {
	
	$args = array(
            'labels'             => array(
	    'name'               => __( 'Events', 'pwp' ),
		'singular_name'      => __( 'Event', 'pwp' ),
		'add_new'            => __( 'Add New', 'pwp' ),
		'add_new_item'       => __( 'Add New event', 'pwp' ),
		'edit_item'          => __( 'Edit event', 'pwp' ),
		'new_item'           => __( 'New event', 'pwp' ),
		'all_items'          => __( 'All events', 'pwp' ),
		'view_item'          => __( 'View event', 'pwp' ),
		'search_items'       => __( 'Search events', 'pwp' ),
		'not_found'          => __( 'No events found', 'pwp' ),
		'not_found_in_trash' => __( 'No events found in Trash', 'pwp' ),
		'parent_item_colon'  => __( ':', 'pwp' ),
		'menu_name'          => __( 'Event', 'pwp' )
	    ),
            'public'             => false,
            'show_ui'            => true,
            'query_var'          => false,
            'supports'           => array( 'title', 'custom-fields' )
        );
	register_post_type( 'event', $args );
    }

    private static function register_metabox() {

    }
}

