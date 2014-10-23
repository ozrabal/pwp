<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once PWP_ROOT . 'lib/native/class-formelement.php';



add_action('pwp_init_form', array('Form', 'init'));
 
 
 
function form( $args ){
   
    new Form( $args );
 }

   

class Form {
    
    protected $attributes = array(),
              $params = array(),
            $legal_method = array('GET', 'POST'),
            $method,
            $action = '',
            $name = null,
            $ajax = false,
            $error = null;
    const POST_TYPE = "post-type-template";
    

     
static function init(){

         $labels = array(
    'name'               => __('Forms','pwp'),
    'singular_name'      => 'Form',
    'add_new'            => 'Add New',
    'add_new_item'       => 'Add New Form',
    'edit_item'          => 'Edit Form',
    'new_item'           => 'New Form',
    'all_items'          => 'All Forms',
    'view_item'          => 'View Form',
    'search_items'       => 'Search Forms',
    'not_found'          => 'No forms found',
    'not_found_in_trash' => 'No forms found in Trash',
    'parent_item_colon'  => '',
    'menu_name'          => 'Forms'
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'form' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => false,
    'menu_position'      => null,
    'supports'           => array( 'title','custom-fields' )
  );

  register_post_type( 'form', $args );

  
  
  //additional fields in post type banner
$prefix = 'pwp_form_';
$banner_meta_config = array(
    'id'             => 'form-meta_fields',
    'title'          => __( 'Banner parameters' ,'pwp' ),
    'pages'          => array( 'form' ),
    'context'        => 'normal',
    'priority'       => 'high',
    'fields'         => array(),
    'local_images'   => false,
    'use_with_theme' => false
);
$banner_meta_fields =  new AT_Meta_Box( $banner_meta_config );
$banner_meta_fields->addTextarea( $prefix . 'definition', array( 'name' => __( 'Definition', 'pwp' ) ) );
$banner_meta_fields->Finish();

}
    


private function get_definition( $slug ){
    if( !empty( $slug ) ){
        $arg = array(
            'name'          => sanitize_key( $slug ),
            'post_type'     => 'form',
            'post_status'   => 'publish',
            'numberposts'   => 1
        );
        $form_definition = get_posts( $arg );
        if( !empty( $form_definition ) ){ 
            return get_post_meta( $form_definition[0]->ID, 'pwp_form_definition', true );
        } 
    }
    die( __( 'Form definition not found, invalid form name (slug)', 'pwp' ) );
}



    public  function __construct($args) {
        //parent::__construct();
      //$args = null;
     //$this->cpt_init();
       
       //echo serialize($args);
    //add_action( 'pwp_init_form',  array($this,'cpt_init'));
       
    
         if(empty( $args )){
             return false;
         }
        if( is_array( $args ) ){
            
            $params = $args;
        }else{
            
            
         
        $params = $this->get_definition($args);



        }
        
        add_action( 'submit_form', array($this, 'submit_form'));
        
//        foreach($params['params'] as $param_name => $param_value ){
//            if($param_name == 'attributes'){
//                $this->attributes = $param_value;
//            }
//        }
        
        $this->attributes = $params['attributes'];
        
        //dump($this->attributes);
        if(isset($params['method']) &&  in_array($params['method'], $this->legal_method)){
            $this->method = $params['method'];
        }else{
            $this->method = 'POST';
        }
        
        if(isset($params['action'])){
            $this->action = $params['action'];
        }
        if(isset($params['ajax'])){
            $this->ajax = $params['ajax'];
            
            if($this->ajax){
            $this->enqueue_media();
            add_action('wp_ajax_formsubmit', array($this,'ajax_submit'));
        add_action('wp_ajax_nopriv_formsubmit', array($this,'ajax_submit'));
        //add_action('template_redirect', array($this,'enqueue_media'));
        }
            
            
            
        }
        if(!isset($params['name'])){
            $this->name = uniqid('pwp_form');
        }else{
            $this->name = 'pwp_form_'.$params['name'];
        }
        //$this->elements = $params['elements'];
        
        $filtered_params = apply_filters('form_elements','', $params['elements']);
        if(!empty($filtered_params) && is_array($filtered_params)){
            $params['elements'] = $filtered_params;
        }
        
        foreach ($params['elements'] as $element){
        $this->addElement(new $element['type']($element['params']));
    }

    
       $this->printForm(); 
        
    }
    
    
    
    public function get_method(){
        return $this->method;
    }
    
    public function get_action(){
        return $this->action;
    }
    
    public function set_params($params){
        
    }
    
    public function get_attributes(){
        $html_attributes = null;
        foreach ($this->attributes as $key => $value ){
            $html_attributes .= $key.'="'.$value.'" ';
        }
        return $html_attributes;
    }
    
    
    public function addElement( $element ){
        $this->elements[] = $element;
        //dump($element); 
        if($element->get_validator()){
$this->validator[$element->get_attrib('name')] = $element->get_validator();
    }
    }
    
    public function printForm(){
        
        
        
        
        
        
        
        
    //dump($error->get_error());
        
        
    //dump($wp_error);
        
        
        
        $output = null;
        foreach($this->elements as $element){
            
             $element->validate();
            if(isset($element->error)){
                $this->error[] = $element->error; 
            }
            
        }
        
        
        
        if(empty($this->error)){
        //if(!empty($_POST)){
        if($_POST && wp_verify_nonce( $_REQUEST[$this->name.'_nonce'], $this->name )){
            do_action('submit_form', $this);
            //tutaj akcja submit ale musi zwracac czy sie udala
            //dobry submit, czyscimy values
            foreach($this->elements as $element){
            if($_POST && $element->get_name()){
        $element->set_param('value', null);
        }
            }   
        }else{
             foreach($this->elements as $element){
            $output .= $element->render();
            if(isset($element->error)){
                //$this->error[] = $element->error; 
            }
             }
        }
        }else{
        
        $output = null;
        foreach($this->elements as $element){
            if($_POST && $element->get_name()){
		if(isset($_POST[$element->get_name()])){
		    $v = $_POST[$element->get_name()];
		}else{
		    $v = $element->get_value();
		}

        $element->set_param('value', $v);
        }
            $output .= $element->render();
            if(isset($element->error)){
                //$this->error[] = $element->error; 
            }
            
        }
       
        }
        echo $this->start_form();
        echo $output;
        echo $this->end_form();
        
        
    }
    private function enqueue_media() {
        wp_enqueue_script( 'ajax-form', plugins_url( 'pwp/modules/form/form-ajax.js' ), array( 'jquery' ) );  
         

        
    }
    
    private function start_form(){
        return '<form action="'.$this->get_action().'" name="'.$this->name.'" method="'.$this->get_method().'" '.$this->get_attributes().'>';
    }
    
    private function end_form(){
        return wp_nonce_field( $this->name, $this->name.'_nonce', true, false ).'</form>';
    }
    
    public function ajax_submit(){
        if(wp_verify_nonce( $_REQUEST[$this->name.'_nonce'], $this->name )){
        do_action('form_submit', $this);
        wp_mail('ozrabal@gmail.com', 'temat', 'wiadomosc');
        echo 'submit';
        }
    }
    
    
    public function submit_form(){
       if(wp_verify_nonce( $_REQUEST[$this->name.'_nonce'], $this->name )){ 
        //dump($_POST);
        
        //dump($this->validator);
        
        
        
        
        
        //if(wp_verify_nonce( $_REQUEST[$this->name.'_nonce'], $this->name )){
        
            
        do_action('form_submit', $this);
        if(  !has_action('form_submit' )){
        
        
        wp_mail('ozrabal@gmail.com', 'temat', 'wiadomosc');
        echo 'wyslano z domyslnej';
        
        
        }
        }
    }
}


class Text extends Formelement{
    protected $type = 'text';
    protected $defaults = array('class' =>'alert');


    public function html() {
       
        return '<input '.$this->get_attributes().'/>';
    }

    
}


class Checkbox extends Formelement{
    protected $type = 'checkbox';
    protected $defaults = array('value' => 'on');


    private function checked(){
	if(isset($_REQUEST[$this->get_name()]) && $this->get_value() === $_REQUEST[$this->get_name()]){
	   // $checked = 'checked="checked"';
	    return 'checked="checked"';
	}
	
    }


    public function html(){
	$checked = null;
	$label = $this->get_label();
	
	if($_REQUEST && !isset($_REQUEST[$this->get_name()])){
	    $this->value = 'on';
	}

//	if($label){
//
//	return '<label><input type="checkbox" '.$this->get_attributes().' '.$this->get_value().' />'.$label['params']['title'].'</label>';
//
//
//	}else{
	    return '<input type="checkbox" '.$this->get_attributes().' value="'.$this->get_value().'" '.$this->checked().' />';

//	}
	}

}

class Hidden extends Formelement{
    protected $type = 'hidden';
    protected $defaults = array('class' =>'alert');


    public function html() {
       
        return '<input type="hidden" '.$this->get_attributes().'/>';
    }

    
}
class Comment extends Formelement{
    protected $type = 'comment';
    protected $defaults = array();


    public function html() {
       
        return '<p '.$this->get_attributes().'>'.$this->get_value().'</p>';
    }

    
}

class Button extends Formelement{
    protected $type = 'button';
    protected $defaults = array('type' => 'submit');
    
    
    public function html() {
        //attributes dac fnkcje a nie zmienna robiona w parencie wtedy bedzie mozna dodawac klasy i label
        return '<button '.$this->get_attributes().'>'.$this->get_value().'</button>';
        
        
    }
}


class Label extends Formelement{
    protected $type = 'label';
    protected $defaults = array();
    
    public function html() {
        return '<label '.$this->get_attributes().' >'.$this->title.'</label>';
    }
    
}

class Textarea extends Formelement{
    protected $type = 'textarea';
    protected $defaults = array('class' => 'info');
    
    
    
    public function html() {
        return '<textarea '.$this->get_attributes().'></textarea>';
    }
}


class Select extends Formelement{
    protected $type = 'select';
    protected $defaults = array('class' => 'select');
    


    private function get_options(){
        if(isset($this->options)){
            $output = null;
            foreach($this->options as $key => $value){
                $current = null;
                if($this->get_attrib('value') == $key){
                    $current = 'selected="selected"';
                }
                $output .= '<option value="'.$key.'" '.$current.'>'.$value.'</option>';
            }
            return $output;
        }
        return false;

    }
    
    public function html() {
        
        return '<select '.$this->get_attributes().'>'.$this->get_options().'</select>';
    }
}