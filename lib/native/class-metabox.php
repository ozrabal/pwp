<?php

class Metabox extends Form {
    private
	$name,
	$title,
	$callback,
	$post_type,
	$context,
	$priority
    ;

    public function __construct( $box ) {

	if ( ! is_admin() )
	    return;
        
        $this->config( $box );
	$this->set_params( $box );
	
	
        add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
        if(!defined( 'DOING_AJAX' ) ) {
            add_action( 'save_post', array( $this, 'save' ) );
	}
    }

    public function set_params( Array $params ) {
        parent::set_params( $params );
    }

    private function config( $config ) {
	$defaults = array(
	    'name'          => 'pwp_meta',
	    'title'         => __( 'Meta parameters', 'pwp' ),
	    'callback'      => 'render',
	    'post_type'     => 'post',
            'allow_posts'   => array(
                'rule'      => null,
                'params'    => null
            ),
	    'context'       => 'normal',
	    'priority'      => 'high',
	);
	
	foreach( $defaults as $param => $value ) {
	    if ( isset( $config[$param] ) ) {
		$this->{ 'set_' . $param }( $config[$param] );
	    } else {
		$this->{ 'set_' . $param }( $value );
	    }
	}
    }
    
    public function set_name( $name ) {
	$this->name = sanitize_key( $name );
    }
    
    public function set_allow_posts( $allow_posts ) {
        
	$this->allow_box = $allow_posts;
    }
    
    public function get_name() {
	return $this->name;
    }

    public function set_title( $title ) {
	$this->title = $title;
        return $this;
    }

    public function get_title( $tag = '%s' ) {
        if ( isset( $this->title ) )
        return sprintf( $tag, $this->title );
    }
    
    public function set_callback( $callback ) {
	$this->callback = sanitize_key( $callback );
    }

    public function get_callback() {
	return $this->callback;
    }

    public function set_post_type( $post_type ) {
	$this->post_type = $post_type;
    }

    public function set_context( $context ) {
	$this->context = sanitize_key( $context );
    }

    public function get_context() {
	return $this->context;
    }

    public function set_priority( $priority ) {
	$this->priority = $priority;
    }

    public function get_priority() {
	return $this->priority;
    }
        
    function save( $post_id ) {

        
        
          
        
//if(isset( $_POST[$this->get_name()])){
    
	foreach( $this->elements as $element ) {



	    if( isset( $_POST[$element->get_name()] ) ) {
                
		//$save[$element->get_name()] = $_POST[$this->get_name()][$element->get_name()];
        $save[$element->get_name()] = $_POST[$element->get_name()];
	    }else{
                $save[$element->get_name()] = '';
            }
             //$old = get_post_meta( $post_id, $this->get_name(), true );
             $old = get_post_meta( $post_id, $element->get_name(), true );
           // dump($element->get_name());
             global $current_screen;

	     //dump($element->get_name());
// dump($this->get_name());
	
//die();
            if(  isset($current_screen) && $current_screen->action != 'add' && $element->get_validator() /*&& isset( $_POST[$element->get_name()])*/ ) {
                
               
                
                $o = $element->validate( $save[$element->get_name()], $element->get_validator());
                //$this->set_message($element, $o, $current);
                
               
              
                
               
                if( $o ) { 
                    
                    //dump(implode('|', $o['error']));
                   // add_settings_error( $this->options[$current]->get_name(), 'error-settings', $element->label->get_name().' : '.$o );
                                       //add_settings_error( 'error-settings', 'error-settings', $element->label->get_name().' : '.$o );

                    //$values[$element->get_name()] = $this->option_values[$element->get_name()];
                    $element->set_class( 'pwp_error' );
		    
                    if(!isset($_SESSION['metabox-error'][$this->get_name()][$element->get_name()]['message'])){
                    $_SESSION['metabox-error'][$this->get_name()][$element->get_name()]['message'] = array( 'error', $o );
                    //dump($_SESSION['metabox-error']);
                    }
                    //die();
                    //unset($o);
//                    $_SESSION[$element->get_name()]['class'] = 'pwp-error';
//                    $_SESSION[$element->get_name()]['message'] = array( 'error', $o );
                   //$_SESSION['metabox-errors'][$this->get_name()] = true;
		    
		
                }
                   
                
            }
            if(isset($_POST[$element->get_name()])){
             $_SESSION['p_'.$post_id][$element->get_name()] = $_POST[$element->get_name()];
            }else{
                $_SESSION['p_'.$post_id][$element->get_name()] = '';
            }
//             $element->valid();
	   //dump($element);
//           
            //dump($_SESSION);
          //die();
//            $element->set_class( 'error' );
//                    $_SESSION[$element->get_name()]['class'] = 'pwp-error';
//                    $_SESSION[$element->get_name()]['message'] = array( 'error', 'ss' );
	}
       //dump($_SESSION);
        //die();
      //dump($save);

   
	if( isset( $save ) && !isset($o)) {
	   //dump($_POST);
	    
            //$old = get_post_meta( $post_id, $this->get_name(), true );
	    if(isset($old) &&  is_array($old)){
            $savee = array_merge( $old,$save);
	    //dump($savee);
	    }else{
                
		$savee = $save;
	    }
         //dump($savee);   
         

foreach($savee as $meta_name => $meta_value){
    update_post_meta( $post_id,  $meta_name, $meta_value );
}


	   // if(update_post_meta( $post_id, $this->get_name(), $savee )){
		   
	    //}

	    //die();
	//}
        
}
    }
    /*
     * add metabox to specified one or many post types
     */
    public function add_box() {
        global $post;
        if ( is_array( $this->post_type ) ) {
            foreach( $this->post_type as $post_type ) {
                $this->set_box( $post_type );
            }
        } else {
            $post_type = $this->post_type;
            $this->set_box($post_type);
        }
    }

    /*
     * 
     */
    private function set_box( $post_type ) {
        if ( isset( $_SESSION['metabox-error'][$this->get_name()] ) && !isset( $_SESSION['noticed'] ) ) {
	    add_action( 'admin_notices', array( $this, 'error_notice' ) );
            $_SESSION['noticed'] = true;
        }
        if ( $this->allow_box_add( $this->allow_box['rule'], $this->allow_box['params'] ) ) {
            add_meta_box( $this->name, $this->title, array( $this, $this->callback ), $post_type, $this->context, $this->priority, '' );
	}
    }
    
    /*
     * allow display metabox to specified post
     * $rule string
     * $params array
     */
    private function allow_box_add( $rule, $params ) {
	global $current_screen;

        if ( $rule && isset( $current_screen ) && $current_screen->action != 'add' ) {
            return $this->{ 'allow_' . $rule }( $params );
        }
        return true;
    }
    
    /*
     * check specufied rules allo metabox display in post edit screen
     * check if post id is in allowed set of ids
     */
    private function allow_id( $ids ) {
        global $post;
        if ( in_array( $post->ID, $ids ) ) {
            return true;
        }
        return false;
    }
    
    public function error_notice(){
        echo '<div class="error"><p>' . __( 'There were errors, not all parameters are saved.', 'pwp' ) . '</p></div>';
    }


    public function render(){
	global $post;
 //global $wp_meta_boxes;
       // dump( $wp_meta_boxes['page']);
        
        //dump($_SESSION);
       //dump($this->elements); 
       
    //$v = get_post_meta($post->ID,$this->get_name(),true);
    
        
        
    foreach ($this->elements as $element){
        
        if(isset($_SESSION['p_'.$post->ID][$element->name])){
             $v[$element->name] = $_SESSION['p_'.$post->ID][$element->name];
        }else{
        
        $v[$element->name] = get_post_meta($post->ID,$element->name,true);
        }        
        
    }
    
    
    
    //dump($this->get_name());
   
    
	$this->options = $v;
	//$v = $this->options ;
        
        //dump($this->options); 
//dump($v);



	$this->body = '<table id="'.$this->get_name().'" class="form-table pwp-metabox"><tbody>';
if(isset($this->elements)){
	foreach ($this->elements as $element){


           if(isset($_SESSION['metabox-error'][$this->get_name()][$element->get_name()])){
               
              //dump($_SESSION['metabox-error'][$this->get_name()][$element->get_name()]);
            
              $element->set_class('pwp-error');
            
            $element->set_message($_SESSION['metabox-error'][$this->get_name()][$element->get_name()]['message']);
            //dump($_SESSION['metabox-error'][$this->get_name()][$element->get_name()]['message']);
            //unset($_SESSION);
             
             
        }
             
	    if(isset($v[$element->get_name()])){
                
                
                
		$element->set_value($v[$element->get_name()]);
	    
                
                
            }
	    //$element->label->set_before('<div>');
	    //$element->label->set_after('</div>');

	   

	    $this->body .= '<tr ><td>'. $element->render().'</tr></td>';

	}
}else{
    echo 'Brak legalnych elementów w formularzu';
}
unset($_SESSION['metabox-error'][$this->get_name()]);
	$this->body .= '</tbody></table>';

	$this->print_form();
        unset($_SESSION['noticed']);
    }
    
}