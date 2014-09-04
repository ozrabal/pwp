<?php

add_action( 'pwp_init_contact', array( 'Contact','init' ) );

function form( $args ) {

    if( isset( $args ) ) {
        
	$contact = new Contact( $args );
        //dump($args);
    }
}
function form_shortcode($atts){
extract( shortcode_atts( array(
	      'args' => '',
	      
     ), $atts ) );
form( $atts['name'] );
}
add_shortcode('form', 'form_shortcode');
/*
interface Observer {
    function update( $data );
}
interface Observable {
    function attach( Observer $observer );
    function detach( Observer $observer );
    function notify();

}
*/
add_filter( 'user_can_richedit', 'disable_for_cpt' );
function disable_for_cpt( $default ) {
    global $post;
    if ( 'form' == get_post_type( $post ) )
        return false;
    return $default;
}

///implements SplSubject
class Contact extends Form /*implements Observable*/ {

    private $shortcode = array();
    private $observers = array();




    static function init(){
        
        //rejestracja typu postu 'form'
         $labels = array(
            'name'               => __( 'Forms', 'pwp' ),
            'singular_name'      => __( 'Form', 'pwp' ),
            'add_new'            => __( 'Add New', 'pwp' ),
            'add_new_item'       => __( 'Add New Form', 'pwp' ),
            'edit_item'          => __( 'Edit Form', 'pwp' ),
            'new_item'           => __( 'New Form', 'pwp' ),
            'all_items'          => __( 'All Forms', 'pwp' ),
            'view_item'          => __( 'View Form', 'pwp' ),
            'search_items'       => __( 'Search Forms', 'pwp' ),
            'not_found'          => __( 'No forms found', 'pwp' ),
            'not_found_in_trash' => __( 'No forms found in Trash', 'pwp' ),
            'parent_item_colon'  => __( ':', 'pwp' ),
            'menu_name'          => __( 'Forms', 'pwp' )
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => false,
	    'exclude_from_search' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => array( 'slug' => 'form' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title','custom-fields','editor' )
        );
        register_post_type( 'form', $args );
        
        

        
        //pola metabox w typie postu form
        $metad = array(
            array(
                'name'      => 'pwp_form',
                'title'     => __( 'Form parameters', 'pwp' ),
                //'callback'=> '',
                'post_type' => array('form'),
                'elements'  => array(
                    array(
                        'type' => 'text',
                        'name' => 'user_email_subject',
                        'params'=> array(
                            'label' => __( 'Subject of user email', 'pwp' ),
                            'class' => 'large-text',
			    'validator' => array(
				'notempty'
			    ),
                         ),
                    ),
                    array(
                        'type' => 'textarea',
                        'name' => 'user_email_template',
                        'params'=> array(
                            'label' => __( 'User email template', 'pwp' ),
                            'class' => 'large-text',
                            'comment' => __('Template of the message that is sent to administrator when a user to fill in a form on the page.','pwp')
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'admin_email_subject',
                        'params'=> array(
                            'label' => __( 'Subject of admin email', 'pwp' ),
                            'class' => 'large-text',
                         ),
                    ),
                    array(
                        'type' => 'textarea',
                        'name' => 'admin_email_template',
                        'params'=> array(
                            'label' => __( 'Admin email template', 'pwp' ),
                            'class' => 'large-text',
                            'comment' => __('Template of the message that is sent to a user when he fills a form on the page.', 'pwp')
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'recipient',
                        'params'=> array(
                            'label' => __( 'Form recipient email addres (comma separated)', 'pwp' ),
                            'class' => 'large-text',
                            'comment' => __('Email addresses of recipients submitted forms','pwp')
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'name' => 'message_form_send',
                        'params'=> array(
                            'label' => __( 'Submit info', 'pwp' ),
                            'class' => 'large-text',
                            'comment' => __('Message displayed after submitting the form','pwp')
                        ),
                    ),

                    array(
                        'type' => 'checkbox',
                        'name' => 'send_to_user',
                        'params'=> array(
                            'label' => __( 'Send copy of message to user', 'pwp' ),
                            'comment' => __('If the box is checked the user filling out a form will receive a copy of the sent data','pwp')

                        ),
                    ),
                )
            )
        );
        foreach( $metad as $box ){
            new Metabox( $box );
        }





        
    }

    public function __construct( $params = false) {
       
        $this->add_shortcodes();
        
        if( !is_array( $params ) ) {
            $params = $this->get_definition( $params );
        } 
        if( !$params ) {
            dbug( __( 'Invalid form name (slug)', 'pwp' ) );
            return false;
        }
       //Pwp::get_instance()->a('ss');
	

        parent::__construct( $params['definition'] );

	if( is_array( $params['definition'] ) ) {
	    $this->init_callback( $params['definition']['callback'] );
	    parent::render();
	    if( wp_verify_nonce( filter_input( INPUT_POST, '_' . $this->get_name() . '_nonce' ), 'form_' . $this->get_name() ) && !$this->get_errors() ) {
		$this->submit();
		$this->body = '';
            } else if( $this->get_errors() ) {
		$this->body = '<div class="alert alert-danger">' . __( 'In the form errors occurred', 'pwp' ) . '</div>' . $this->body;
	    }
	    $this->print_form();
        }
    }
    
    private function get_definition( $slug = null ){
        
	if( empty( $slug ) ) {
	    dbug( __( 'Invalid form name (slug)', 'pwp' ) );
	    return false;
	}
	$arg = array(
	    'name'          => sanitize_key( $slug ),
            'post_type'     => 'form',
            'post_status'   => 'publish',
            'numberposts'   => 1
        );
        $form_definition = get_posts( $arg );
            //dump($form_definition);
            
            if( !empty( $form_definition ) ){
                //$meta = get_post_meta( $form_definition[0]->ID,'user_email_template',true );
                
                //dump($meta);
                
                
                
                $this->user_email_template = get_post_meta( $form_definition[0]->ID,'user_email_template',true );;
                $this->admin_email_template = get_post_meta( $form_definition[0]->ID,'admin_email_template',true );;
                $this->user_email_subject = get_post_meta( $form_definition[0]->ID,'user_email_subject',true );;
                $this->admin_email_subject = get_post_meta( $form_definition[0]->ID,'admin_email_subject',true );;
                $this->recipient = get_post_meta( $form_definition[0]->ID,'recipient',true );;
                $this->send_to_user = get_post_meta( $form_definition[0]->ID,'send_to_user',true );;
                $this->message_form_send = get_post_meta( $form_definition[0]->ID,'message_form_send',true );;
                
                //dump(get_post_meta( $form_definition[0]->ID,'definition',true ));
                //$a = get_post_meta( $form_definition[0]->ID,'definition',true );
                //dump($a);
                //return $a['definition'];
                return get_post_meta( $form_definition[0]->ID,'definition',true );;
            }
            
	    return false;
       
        
    }

    public function attach(  $observer) {
        $this->observers[] = $observer;
    }
 
    public function detach(  Observer $observer) {
        unset($this->observers);
    }
 
    public function notify() {
        
	$result = false;
	$request = $this->get_request();
//	if(isset($request['event'])){
//	    $p = get_post(intval($request['event']));
//	    $request['tytul'] = $p->post_title;
//	}
        //$result = null;

	//dump($this->observers);
//die();
        foreach ($this->observers as $observer) {
            
            $result = $observer->update(array(
                'object' => $result,
		'user_email_template' => $this->user_email_template ,
		'admin_email_template' => $this->admin_email_template,
                'user_email_subject' => $this->user_email_subject,
		'admin_email_subject' => $this->admin_email_subject,
                'send_to_user' => $this->send_to_user,
		'recipient' => $this->recipient,
		'request' => $request));
        
            
        }
	
	return $result;
    }

    public function add_form(  $params ){
        $form_atts = shortcode_atts( array(
            'name'      => 'form_' .  uniqid(),
            'recipient' => get_option( 'admin_email' ),
            'callback'  => ''
        ), $params );
        if( is_callable( $form_atts['callback'] ) ) {
            $form_atts['callback'] = new $form_atts['callback']();
        }
        $this->shortcode = $form_atts;
    }

    private function create_settings( $field_atts ){
        $field['type'] = $field_atts['type'];
        $field['name'] = $field_atts['name'];
        if(!empty($field_atts['validator'])){
            $field_atts['validator'] = explode(',', $field_atts['validator']);
            $field['validator'] = $field_atts['validator'];
        }
        if(!empty($field_atts['callback'])){
            $field_atts['callback'] = explode(',', $field_atts['callback']);
            $field_atts['callback'] = $field_atts['callback'];
        }
        if(!empty($field_atts['container'])){
            $field_atts['before'] = '<div class="'.$field_atts['container'].'">';
            $field_atts['after'] = '</div>';
            unset($field_atts['container']);
        }
        if(!empty($field_atts['options'])){
            $options = explode(',', $field_atts['options']);
            foreach($options as $option){
                $option = explode('|', $option);
                $opt[$option[0]] = $option[1];
            }
            $field_atts['options'] = $opt;
        }
        foreach($field_atts as $key => $att){
            if(!in_array($key,array('type','name','validator')) && !empty($att) ){
                $field['params'][$key] = $att;
            }
        }
        return $field;
    }

    public function add_field( $params, $content = null ) {
        $field_atts = shortcode_atts( array(
            'type'          => 'input',
            'name'          => 'field_' . uniqid(),
            'validator'     => '',
            'class'         => '',
            'title'         => '',
            'label'         => '',
            'container'     => '',
            'options'       => '',
            'value'         => '',
            'callback'      => '',
            'content'       => '',
            'repeatable'    => null
        ), $params );
    
        if( $field_atts['type'] == 'repeatable' ) {
            $field_atts['options'] = array();
        }
        $field = $this->create_settings($field_atts);

        if( $field_atts['repeatable'] ) {
            unset( $field['params']['repeatable'] );
            $this->repeatable[$field_atts['repeatable']][] = $field; 
            unset( $field );
        } else {
            $this->shortcode['elements'][]= $field;
        }
    }
    
    /**
     * 
     * 
     * @global $post
     */
    public function onsave(){
        
        //dump(filter_input( INPUT_POST, 'content' ));
            //die();
            
	if( filter_input( INPUT_POST, 'content' ) ) {
            
            
            global $post;
            do_shortcode( stripslashes( filter_input( INPUT_POST, 'content' ) ) );
	    if( isset( $this->shortcode['elements'] ) ) {
                foreach( $this->shortcode['elements'] as $key => $field ) {
                    if( isset( $this->repeatable[$field['name']] ) ) {
                        unset( $this->repeatable[$field['name']]['repeatable'] );
                        $this->shortcode['elements'][$key]['params']['options'] = $this->repeatable[$field['name']];
                    }
                }
            }
            $meta = get_post_meta( $post->ID, 'pwp_form', true ); 
            $meta['definition'] = $this->shortcode;
            update_post_meta( $post->ID, 'definition', $meta );
        }
    } 

    private function add_shortcodes(){
	if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) && 'form' == pwp_current_post_type() ) {
            add_shortcode( 'form', array( $this, 'add_form' ) );
            add_shortcode( 'field', array( $this, 'add_field' ) );
            add_action( 'save_post', array( $this, 'onsave' ) ,0 );
        }
    }

    private function init_callback($args = false){
	if(  $args  ) {
                /*
                dump($params['definition']['callback']);
                foreach ($params['definition']['elements'] as $element){


		    if($element['type'] == 'file'){
                        //dump($element);
                        $n = array('Observer_Upload', $params['definition']['callback']);
                         $params['definition']['callback'] = $n;
                    }


                }

		 */

                $this->callback = explode(',',$args);
		//dump($this->callback);
                //if( is_array( $this->callback ) ) {
                    foreach( $this->callback as $callback ) {
                        $c = new $callback();
                        //$this->attach( $c );
			$this->attach(new $callback() );
		    }
                    //$this->attach( $cal );
                //} else {
                //    $n = new $this->callback();
                //    $this->attach( $n );
                //}
            }
    }




    public function submit() {
	if( $this->notify() ) {
	    echo '<div class="alert alert-success">' . $this->message_form_send . '</div>';
	}
    }
}
$contact = new Contact();