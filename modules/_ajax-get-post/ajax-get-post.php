<?php

add_action( 'pwp_init_ajax-get-post', array( 'Ajaxpost', 'init' ) );

class Ajaxpost {
    static $instance = null;
    
    public static function init() {
        if ( is_null( self::$instance ) )
            self::$instance = new self();
        return self::$instance;
    }
    
    public function ax_get_post(){
        
    }
    
    
    public function __construct() {
        //add_action( 'template_redirect', array( $this, 'load' ) );
        add_action( 'wp_ajax_get_post', array($this,'ax_get_post' ));
        add_action( 'wp_ajax_nopriv_get_post', array($this,'ax_get_post' ));
        
        add_filter('previous_post_link',  array($this,'add_ajax'));
        add_filter('next_post_link',  array($this,'add_ajax'));
        //apply_filters( "{$adjacent}_post_link", $output, $format, $link, $post );
        
        
   wp_enqueue_script( 'pwp-ax-getpost', plugin_dir_url( __FILE__ ) . 'ajax-get-post.js', array( 'jquery' ), false, true );

  if( isset( $_REQUEST['ajax'] ) ) {
            add_filter( 'template_include', array( $this, 'set_template' ) );
        }      
        
        
//        add_filter('next_post_link', 'post_link_attributes');
//add_filter('previous_post_link', 'post_link_attributes');


        
        
        
    }
    
    
    function set_template( $template ) {
        
        
        return plugin_dir_path( __FILE__) . '/ajax-post.php';
    }
    
    
    public function add_ajax( $output){
        //global $format;
        $injection = 'class="ax-get"';
    return str_replace('<a href=', '<a '.$injection.' href=', $output);
    
   //return $link . '?ajax=true';
    
        //dump($output);
//dump('sssssss');


//return 'ssss';
//die();


//return $output;
    }
    
    
    
    
    
    
    

}