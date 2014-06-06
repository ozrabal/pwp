<?php

add_action( 'pwp_init_ajax-pagination', array( 'Ajaxpagination', 'init' ) );

class Ajaxpagination {
    static $instance = null;
    
    public static function init() {
        if ( is_null( self::$instance ) )
            self::$instance = new self();
        return self::$instance;
    }
    
    public function __construct() {
        add_action( 'template_redirect', array( $this, 'load' ) );
    }
    
    public function load(){
        global $wp_query;
        
      
        
        if( !is_singular() ) {
            $paged = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;
            $params = array(
                'start'     => $paged,
                'max'       => $wp_query->max_num_pages,
                'next_url'  => next_posts( $wp_query->max_num_pages, false ),
                'label'     => array(
                    'load_more'     => __( 'Load more works', 'pwp' ),
                    'all_loaded'    => __( 'No more works to load.', 'pwp' ),
                    'loading'       => __( 'Loading works', 'pwp' )
                )
            );
            wp_enqueue_script( 'pwp-ax-pagination', plugin_dir_url( __FILE__ ) . 'ajax-pagination.js', array( 'jquery','jquery.application' ), false, true );
            //wp_enqueue_style( 'pwp-ax-pagination', plugin_dir_url( __FILE__ ) . 'ajax-pagination.css', false, false, 'all' );
            wp_localize_script( 'pwp-ax-pagination', 'axp', $params );
 	}
        if( isset( $_REQUEST['ajax'] ) ) {
            add_filter( 'template_include', array( $this, 'set_template' ) );
        }
    }
 
    function set_template( $template ) {
        
        
        return plugin_dir_path( __FILE__) . '/ajax-post-list.php';
    }
}