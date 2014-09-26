<?php

abstract class Module {

    protected $actions;

    //static $module;

    public function __construct() {
	$this->get_actions();
        spl_autoload_register( array( $this, 'module_class_autoloader' ) );
    }

    protected function get_actions() {
        $methods = preg_grep( '/_Action/', get_class_methods( $this ) );
        foreach ( $methods as $method ) {
            $this->actions[] = basename( $method, '_Action' );
        }
    }

    protected function is_action(){
        if ( !in_array( $this->action, $this->actions, true ) && false === has_filter( 'login_form_' . $this->action ) ) {
            return false;
        }
        return true;
    }



    public function route(){

        if( get_query_var( $this->action_slug ) ) {
            $this->action = get_query_var( $this->action_slug );
        } else if ( filter_input( INPUT_GET, $this->action_slug ) ) {
            $this->action = filter_input( INPUT_GET, $this->action_slug );
        } else if ( filter_input( INPUT_POST, $this->action_slug ) ) {
            $this->action = filter_input( INPUT_POST, $this->action_slug );
        } else {
	    $this->action = 'index';
        }

	

        if ( $this->is_action() ) {
            if ( method_exists( $this, $this->action . '_Action' ) ){
                call_user_func( array( $this, $this->action . '_Action' ), array() );
	    }else{
		call_user_func( array( $this, 'index_Action' ), array() );
	    }
        }else{
            $wp_error = new WP_Error();
            $wp_error->add('router', __( 'Action not allowed' ), 'message' );
	    die($wp_error->get_error_message());
            //return $wp_error;
        }
    }

static function module_class_autoloader( $classname ) {
    
    $path = explode('_', strtolower( $classname ));
    $end = end($path);
    $module = explode('_', strtolower( $classname ));
    
    array_pop($path);
    if(isset($path[0]) && $path[0] == 'interface'){
        $end = 'interface-'.$end;
    }else{
        $end = 'class-'.$end;
    }

    //dump(self::$module);

    $path[] = $end;
    $classname = implode('/',$path);
    $classname = str_replace( '_', '/', strtolower( $classname ) );
    //$classfile = sprintf( '%slib/native/%s.php', ABSPATH.'wp-content/plugins/pwp/', str_replace( '_', '-', strtolower( $classname ) ));

    $dirs_excluded = array_filter(glob(PWP_ROOT.'modules/_*',GLOB_ONLYDIR), 'is_dir');
    
    $dirs_all = array_filter(glob(PWP_ROOT.'modules/*',GLOB_ONLYDIR), 'is_dir');
    $dirs = array_diff( $dirs_all, $dirs_excluded);
    //dump($dirs);
    
 
    
    
    
            
            foreach($dirs as $dir){
            $classfile = sprintf( $dir.'/%s.php', 'class-'. implode( '-',$module  ));
            //dump($module);
            //dump($classfile);
            
            
            //die();
            if ( file_exists( $classfile ) ) {
            include_once( $classfile );
            break;
            }
        
        }
    }



}