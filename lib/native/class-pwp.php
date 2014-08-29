<?php

class Pwp{

    protected $name;
    
    public static $instance,$modules_directory,$modules;
    
    public static function init() {
        //wp_localize_script( 'ajax-request', 'ajaxurl', ( admin_url( 'admin-ajax.php' ) ) );  
	if ( is_null( self::$instance ) ) {
            self::$instance = new Pwp();
	}
	return self::$instance;
    }
    
    public function enqueue_media() {
        wp_localize_script( 'ajax-request', 'ajaxurl', ( admin_url( 'admin-ajax.php' ) ) );
    }

    public static function get_instance(){
        if ( is_null( self::$instance ) ) {
            self::$instance = new Pwp();
	}
	return self::$instance;
    }

    private function __construct() {
        $this->name = __CLASS__;
        $this->base_dir = plugin_dir_path( __FILE__ );
        $this->get_modules_list();
        self::load_modules();
    }
        
    private function get_modules_list(){
	if ( is_dir( PWP_ROOT . '/modules' ) ) {
	    $modules = array_diff( scandir( PWP_ROOT . '/modules' ), array( '..', '.', '.DS_Store', 'index.php' ) );
	    //moduly rozpoczynajace sie od _ sa nieaktywne
	    foreach ( $modules as $module ) {
		if ( substr( $module, 0, 1 ) != '_' ) {
		    self::$modules[] = $module;
		}
	    }
	}
    }
    
    public static function load_modules() {
	foreach( self::$modules as $module ) {
	    self::load_module( $module );
	}
    }
    
    public static function load_module( $module ) {
	if ( file_exists( PWP_ROOT . '/modules/' . $module . '/' . $module . '.php' ) ) {
	    require_once PWP_ROOT . '/modules/' . $module . '/' . $module . '.php';
	    do_action( 'pwp_init_' . $module, array( 'init' ) );
	} else {
	    echo 'File not found: ' . PWP_ROOT . '/modules/' . $module . '/' . $module . '.php';
	}
    }
}