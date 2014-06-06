<?php


class Pwp{

    protected $name
            ;
    public static $instance,$modules_directory,$modules;
    
    public static function init() {
                wp_localize_script( 'ajax-request', 'ajaxurl', ( admin_url( 'admin-ajax.php' ) ) );  
		
        if ( is_null( self::$instance ) )
            self::$instance = new Pwp();
	
        return self::$instance;
        
    }
    
    public static function get_instance(){
         if ( is_null( self::$instance ) )
            self::$instance = new Pwp();
	
        return self::$instance;
    }


    private function __construct(){
        $this->name = __CLASS__;
        $this->base_dir = plugin_dir_path( __FILE__ );
        $this->get_modules_list();
        self::load_modules();
    }
        
    
    private function get_modules_list(){
        
        if(is_dir(PWP_ROOT . '/modules')){
        $modules = array_diff(scandir(PWP_ROOT . '/modules'), array('..', '.','.DS_Store', 'index.php'));
        //moduly rozpoczynajace sie od _ sa nieaktywne
        foreach ($modules as $module){
            if(substr($module, 0, 1) != '_') {
                self::$modules[] = $module;
            }
        }   
  }   
        
        
    }
    
    

    
    
    
//    public function load_class( $class, $create_object = FALSE ) {
//        
//        $p = $this->base_dir.'../../modules/'.$class.'/class-'.$class.'.php';
//        dump($p);
//        ! class_exists( $class ) && require_once $p ;
//        //dump($base_dir);
//        return $create_object ? new $class : TRUE;
//    }
    
    public static function load_modules(){
        //z option brac
        
        
        //$modules = array('session','cookie-alert', 'contact');
        
        //$modules = $this->modules;
        
        foreach(self::$modules as $module){
            
        
//           require_once PWP_PATH.'/modules/'.$module.'/'.$module.'.php';
//           do_action( 'pwp_init_' . $module );
            
            self::load_module($module);
            
            
        //do_action( 'pwp_init' );
        }
    }
    
    public static function load_module($module){
        //z option brac
        //$modules = array('cookie-alert');
        
        //foreach($modules as $module){
           
        if(file_exists(PWP_ROOT.'/modules/'.$module.'/'.$module.'.php')){
           require_once PWP_ROOT.'/modules/'.$module.'/'.$module.'.php';
           
           do_action( 'pwp_init_' . $module ,array('init'));
            //if(  is_callable( $module))
	}else{
echo 'File not found: '.PWP_ROOT.'/modules/'.$module.'/'.$module.'.php';
	    
//	    $e = new WP_Error('broke', __("I've fallen and can't get up"));
//	    echo $e->get_error_message();
	}
            
            
        //do_action( 'pwp_init' );
        //}
    }
    
}



class Error{
    
    private static $instance;
    private $error;

    private function __construct() {
        $this->error = null;
        
    }
    
    static function get_instance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function add_error($error){
        $this->error[] = $error; 
    }
    public function get_error(){
        return $this->error;
    }
}

