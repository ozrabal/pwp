<?php


//add_action( 'pwp_init_option', array('Cookiealert', 'init'));



class Adminmenu{
    
    public static $instance;


    public static function get_instance(){
	if ( is_null( self::$instance ) )
	    self::$instance = new Adminmenu();
	return self::$instance;
    }

    private function __construct(){
   
    }
    
    public function render(){
        
        echo 'sss';
        
    }

    private function set_params(Array $params){
	foreach ($params as $param => $value){
	    $method = 'set_'.$param;
	    if(method_exists($this,$method)){
		$this->$method($value);
	    }
	}
    }

    public function set_page_title($title){
	$this->page_title = $title;
    }

    public function set_menu_title($title){
	$this->menu_title = $title;
    }

    public function set_capability($capability){
	$this->capability = $capability;
    }
    public function set_menu_slug($menu_slug){
	$this->menu_slug = $menu_slug;
    }

    public function set_callback($callback){
	$this->callback = $callback;
    }

    public function set_icon_url($icon_url){
	$this->icon_url = $icon_url;
    }

    public function set_position($position){
	$this->position = $position;
    }

    public function add_page(Array $page){
	if(!empty($page)){
	   $this->set_params($page);
       }
	add_action('admin_menu', array($this, 'add_menu_page'));
    }


    public function add_menu_page(){
	add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array($this, $this->callback), $this->icon_url, $this->position );
    }




   public function add_submenu_page(){


   }


   public function add_tab(){
       
       
   }
//   
//    add_action('admin_menu', 'my_menu');
//
//function my_menu() {
//    add_menu_page('My Page Title', 'My Menu Title', 'manage_options', 'my-page-slug', 'my_function');
//}
//
//function my_function() {
//    echo 'Hello world!';
//}
}

