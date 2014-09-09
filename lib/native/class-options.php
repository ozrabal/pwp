<?php
/**
   * Option class
   * 
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Options extends Form {

    public static $instance;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Options();
        }
        return self::$instance;
    }

    function __construct() {
        //parent::__construct(array());
        
        
//        if( $_POST ) {
//	    $_SESSION['request'] = $_POST;
//	}
    }
    
    public function add_element( $type, $name ) {
                

        $this->options = get_option($this->get_name(), true);
	//register_setting( $this->get_name(), 'form_options');
	//echo $a = $this->get_name();

	$this->elements[$name] = parent::add_element($type, $name);

	//$this->elements[$name] = parent::add_element($type, $name);
        
	$old_value = get_option($this->get_name(), true);
	if(isset($old_value[$name])){
            $this->elements[$name]->set_value($old_value[$name]);
	}
        return $this->elements[$name];
    }

//    private function set_session_request(){
//	if(isset($_SESSION['request'])){
//	    $this->set_request($_SESSION['request']);
//	    unset($_SESSION['request']);
//	}
//    }

//    public function tab($tab_name){
//        return $this;
//    }
//
    
    
    
//    public function sec(){
//        foreach ($this->elements as $element){
//            $this->body .='<tr>';
//            
//	    //$element->set_before('<td>');
//            $element->label->set_before('<th>');
//            $element->label->set_after('</th><td>');
//             $element->set_after('</td>');
//             
//	    //$this->body .= $element->render();
//            $this->body .='</tr>';
//	}
//        //echo $this->body;
//        
//        
//    }
    
//    public function render(){
//        
//        //global $wp_settings_sections;
//        //dump($wp_settings_sections);
//        wp_enqueue_media();
//        
//        
//        $this->set_session_request();
//	$this->body .= '<div class="wrap">';
//     
//        
//        
//        
//        $this->body .= '<div id="icon-themes" class="icon32"></div>';
//        $this->body .= '<h2>Sandbox Theme Options</h2>';
//         settings_errors(); 
//
//      $this->body .= '   <h2 class="nav-tab-wrapper">';
//    $this->body .= '<a href="#" class="nav-tab">Display Options</a>';
//    $this->body .= '<a href="#" class="nav-tab">Social Options</a>';
//$this->body .= '</h2>';
//         
//         
//	$this->body .= '<form method="post" name="'.$this->get_name().'" action="'.$this->get_action().'">';
//        
//        $this->body .= '<table class="form-table"><tbody>';
//         do_settings_sections( 'form_options' );
//        
//        $this->body .= '</tbody></table>';
//        
//        	$this->body .= get_submit_button();
//        
//        $this->body .= '</div>';
//       echo $this->body;
//	//$this->print_form();
//        //dump($this->get_request());
//        
//        
//    }
    
    
//    public function agrender(){
//
//
//
//
//
//
//	$this->set_session_request();
//	$this->body .= '<div class="wrap">';
//
//
//
//
//
//
//        $this->body .= '<div id="icon-themes" class="icon32"></div>';
//        $this->body .= $this->get_title( '<h2>%s</h2>' );
//
//
//
//	$this->body .= '<form method="post" name="'.$this->get_name().'" action="'.$this->get_action().'">';
//
//        $this->body .= '<table class="form-table"><tbody>';
//
//	foreach ($this->elements as $element){
//            echo $element->valid();
//            echo $element->get_message();
//            //register_setting( $element->get_name(), 'form_options',$element->valid());
//
//
//            $this->body .='<tr >';
//
//	    //$element->set_before('<td>');
//            $element->label->set_before('<th>');
//            $element->label->set_after('</th><td>');
//             $element->set_after('</td>');
//
//
//
//	    $this->body .= $element->render();
//            $this->body .='</tr>';
//	}
//        settings_errors();
//        $this->body .= '</tbody></table>';
//
//
//	$this->body .= get_submit_button();
//
//        $this->body .= '</div>';
//
//	$this->print_form();
//        //dump($this->get_request());
//
//
//    }

//    public function print_form() {
//
//	echo $this->body;
//         do_settings_sections( 'section' );
//	settings_fields( $this->get_name() );
//
//
//	echo '</form>';
//    }
}