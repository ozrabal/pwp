<?php
/**
   * Contact module class
   * 
   * @package    PWP
   * @subpackage Contact
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Contact extends Form {

    private $shortcode = array();
    private $callback_array = array();

    /**
     * Inicjalizacja modulu
     */
    static function init() {
	self::settings();
	self::register_post_type();
	self::register_metabox();
	if( is_admin() && current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) && 'form' == pwp_current_post_type() ) {
	    new Contact();
	}
    }
    
    /**
     * Konstruktor
     * 
     * @param type $params
     * @return boolean
     */
    public function __construct( $params = false) {
        //$this->settings();
        //shortcody dla pola edycji
        $this->add_shortcodes();

	if( !$params ) {
	    return false;
        }
        if( !is_array( $params ) ) {
            $params = $this->get_definition( $params );
        }

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
    
    /**
     * pobiera definicje formularza z meta postu form o podanym slugu
     * @param String $slug
     * @return boolean
     */
    private function get_definition( $slug = null ) {

	if( empty( $slug ) ) {
	    return false;
	}
	$arg = array(
	    'name'          => sanitize_key( $slug ),
            'post_type'     => 'form',
            'post_status'   => 'publish',
            'numberposts'   => 1
        );
        $form_definition = get_posts( $arg );

	if( !empty( $form_definition ) ) {
	    return $this->get_form_meta( $form_definition[0]->ID );
	}
	dbug( 'Invalid form name (slug) ' . $slug );
        return false;
    }
    
    /**
     * 
     * @param Int $form_id
     * @return Array
     */
    private function get_form_meta( $form_id ) {

	$this->user_email_template = get_post_meta( $form_id,'user_email_template',true );
        $this->admin_email_template = get_post_meta( $form_id,'admin_email_template',true );
        $this->user_email_subject = get_post_meta( $form_id,'user_email_subject',true );
        $this->admin_email_subject = get_post_meta( $form_id,'admin_email_subject',true );
        $this->recipient = get_post_meta( $form_id,'recipient',true );
        $this->send_to_user = get_post_meta( $form_id,'send_to_user',true );
        $this->message_form_send = get_post_meta( $form_id,'message_form_send',true );
        return get_post_meta( $form_id,'definition',true );
    }
    
    /**
     * Zapisuje definicje formularza na podstawie shortcode z edytora do pola meta
     * @global Post $post
     */
    public function onsave() {

	if( filter_input( INPUT_POST, 'content' ) ) {
	    global $post;
            do_shortcode( stripslashes( filter_input( INPUT_POST, 'content' ) ) );
	    if( isset( $this->shortcode['elements'] ) ) {
                $this->assign_repeatable();
            }
            $meta = get_post_meta( $post->ID, 'pwp_form', true );
            $meta['definition'] = $this->shortcode;
            update_post_meta( $post->ID, 'definition', $meta );
        }
    }
    
    /**
     * obsluga pola powtarzalnego w definicji
     */
    private function assign_repeatable() {

	foreach( $this->shortcode['elements'] as $key => $field ) {
	    if( isset( $this->repeatable[$field['name']] ) ) {
		unset( $this->repeatable[$field['name']]['repeatable'] );
		$this->shortcode['elements'][$key]['params']['options'] = $this->repeatable[$field['name']];
            }
        }
    }
    
    /**
     * dodaje shortcode do edytora
     */
    private function add_shortcodes() {
	
	if( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) && 'form' == pwp_current_post_type() ) {
            add_shortcode( 'form', array( $this, 'add_form' ) );
            add_shortcode( 'field', array( $this, 'add_field' ) );
            add_action( 'save_post', array( $this, 'onsave' ) ,0 );
	    add_filter( 'user_can_richedit', array( $this, 'disable_rich_editor' ) );
        }
    }
    
    /**
     * shortcode [form]
     * @param Array $params
     */    
    public function add_form( $params ) {

	$form_atts = shortcode_atts( array(
            'name'      => 'form_' . uniqid(),
            'recipient' => get_option( 'admin_email' ),
            'callback'  => ''
        ), $params );
        if( is_callable( $form_atts['callback'] ) ) {
            $form_atts['callback'] = new $form_atts['callback']();
        }
        $this->shortcode = $form_atts;
    }
    
    /**
     * shortcode [field]
     * @param Array $params
     * @param Mixed $content
     */
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
        $field = $this->create_settings( $field_atts );

        if( $field_atts['repeatable'] ) {
            unset( $field['params']['repeatable'] );
            $this->repeatable[$field_atts['repeatable']][] = $field;
            unset( $field );
        } else {
            $this->shortcode['elements'][]= $field;
        }
    }
    
    /**
     * ustawia parametry pola ze shortcode
     * @param Array $field_atts
     * @return Array
     */
    private function create_settings( $field_atts ){
        $field['type'] = $field_atts['type'];
        $field['name'] = $field_atts['name'];
        if( !empty( $field_atts['validator'] ) ) {
            $field_atts['validator'] = explode( ',', $field_atts['validator'] );
            $field['validator'] = $field_atts['validator'];
        }
        if( !empty($field_atts['callback'] ) ) {
            $field_atts['callback'] = explode( ',', $field_atts['callback'] );
            $field_atts['callback'] = $field_atts['callback'];
        }
        if( !empty($field_atts['container'] ) ) {
            $field_atts['before'] = '<div class="' . $field_atts['container'] . '">';
            $field_atts['after'] = '</div>';
            unset( $field_atts['container'] );
        }
        if( !empty($field_atts['options'] ) ) {
            $options = explode( ',', $field_atts['options'] );
            foreach( $options as $option ) {
                $option = explode( '|', $option );
                $opt[$option[0]] = $option[1];
            }
            $field_atts['options'] = $opt;
        }
        foreach( $field_atts as $key => $att ) {
            if( !in_array( $key, array( 'type', 'name', 'validator' ) ) && !empty( $att ) ) {
                $field['params'][$key] = $att;
            }
        }
        return $field;
    }
    
    /**
     * ustawia funkcje obslugi formularza, jesli nie podano to Email
     * @param string $args
     */
    private function init_callback( $args = false ) {
	if( !$args ) {
	    $args = 'Callback_Email';
	}
	$this->callback = explode( ',', $args );
	foreach( $this->callback as $callback_class ) {
	    $this->attach( new $callback_class() );
	}
    }
    
    /**
     * ustawia tablice obslugi formularza
     * @param Object $callback_object
     */
    public function attach( $callback_object ) {
        $this->callback_array[] = $callback_object;
    }
    
    /**
     * wysylka formularza i wywolanie obslugi
     */
    public function submit() {
	if( $this->notify() ) {
	    echo '<div class="alert alert-success">' . $this->message_form_send . '</div>';
	}
    }
    
    /**
     * wywoluje akcje w funkcji obslugujacej po submicie formularza
     * @return boolean
     */
    public function notify() {

	$result = null;
	foreach( $this->callback_array as $callback ) {
	    $result = $callback->do_callback( array(
		    'object'		    => $result,
		    'user_email_template'   => $this->user_email_template ,
		    'admin_email_template'  => $this->admin_email_template,
		    'user_email_subject'    => $this->user_email_subject,
		    'admin_email_subject'   => $this->admin_email_subject,
		    'send_to_user'	    => $this->send_to_user,
		    'recipient'		    => $this->recipient,
		    'request'		    => $this->get_request()
		)
	    );
	}
	return $result;
    }
    
    /**
     * rejestracja typu postu form
     */
    static function register_post_type() {

        $args = array(
            'labels'             => array(
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
	    ),
            'public'             => false,
            'show_ui'            => true,
            'query_var'          => false,
            'supports'           => array( 'title', 'custom-fields', 'editor' )
        );
	register_post_type( 'form', $args );
    }

    /**
     * rejestracja pol meta dla typu postu form
     */
    static function register_metabox(){

	$box = array(
            'name'      => 'pwp_form',
            'title'     => __( 'Form parameters', 'pwp' ),
            'post_type' => array( 'form' ),
            'elements'  => array(
                array(
                    'type' => 'text',
                    'name' => 'user_email_subject',
                    'params'=> array(
                        'label' => __( 'Subject of user email', 'pwp' ),
                        'class' => 'large-text',
                        'validator'=>array('notempty','email')
                        
                    ),
                    
                ),
		 array(
                    'type' => 'wysiwyg',
                    'name' => 'auser_email_template',
                    'params'=> array(
                        'label' => __( 'User email template', 'pwp' ),
                        'class' => 'large-text',
			'options' => array('tinymce' => true),
                        'comment' => __( 'Template of the message that is sent to administrator when a user to fill in a form on the page.', 'pwp' )
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'user_email_template',
                    'params'=> array(
                        'label' => __( 'User email template', 'pwp' ),
                        'class' => 'large-text',
                        'comment' => __( 'Template of the message that is sent to administrator when a user to fill in a form on the page.', 'pwp' )
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
                        'comment' => __( 'Template of the message that is sent to a user when he fills a form on the page.', 'pwp' )
                    ),
                ),
                array(
                    'type' => 'text',
                    'name' => 'recipient',
                    'params'=> array(
                        'label' => __( 'Form recipient email addres (comma separated)', 'pwp' ),
                        'class' => 'large-text',
                        'comment' => __( 'Email addresses of recipients submitted forms', 'pwp' )
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'name' => 'message_form_send',
                    'params'=> array(
                        'label' => __( 'Submit info', 'pwp' ),
                        'class' => 'large-text',
                        'comment' => __( 'Message displayed after submitting the form', 'pwp' )
                    ),
                ),
		array(
                    'type' => 'checkbox',
                    'name' => 'send_to_user',
                    'params'=> array(
                        'label' => __( 'Send copy of message to user', 'pwp' ),
                        'comment' => __( 'If the box is checked the user filling out a form will receive a copy of the sent data', 'pwp' )
		    ),
                ),
            )
        );
        new Metabox( $box );
    }
    
    /**
     * funkcja zwraca false 
     * @return boolean
     */
    static function disable_rich_editor() {
	return true;
    }
    
    private static function settings(){
        /*opcje*/
$admins = new Administrator();
//$admins = Administrator::init();

$page = array(
    'parent_slug' => 'edit.php?post_type=form',
    'page_title'    => __( 'Test settings page', 'pwp' ),
    'menu_title'    => __( 'Test settings', 'pwp' ),
    'capability'    => 'manage_options',
    'menu_slug'	    => 'test-options',
    'icon'	    => '',
    'position'	    => null,
);
$admins->add_page( $page );
$admins->add_tab( 'Nowy tab', 'test-options' );


$args = array(
    'name' => 'a_options',
    'action' => 'options.php',
    'title' => __( 'Pierwsze opcje', 'pwp' ) 
    
);

$options = new Options($args);
//pobiera kategorie do selecta
function get_roles(  ) {


    global $wp_roles;
    $default_roles = array();
    //$default_roles = array('administrator','editor', 'author', 'contributor', 'subscriber', 'pending');


     $roles = $wp_roles->get_names();
    // dump($roles);

     $roless['Bez ograniczeń'] = '';
foreach($roles as  $role => $name){
    if(!in_array( $role, $default_roles)){
    $roless[$name] = $role;
    }
}

     //dump($roless);
return $roless;
     /*
    $categories = get_categories( $args );
    $cat[__('Wybierz kategorię')] = '';
    foreach( $categories as $category ) {
	$cat[$category->name] = $category->term_id;
    }
    return $cat;
    */
}

//$options->set_name( 'a_options' )
//        ->set_action( 'options.php' )
//        ->set_title( __( 'Pierwsze opcje', 'pwp' ) );
 
$options->add_element( 'text', 'tekst' )
        ->set_label( __( 'User email templatec', 'pwp' ) )
        ->set_class( 'klasa' )
        ->set_validator( array( 'notempty' ) );

$options->add_element( 'wysiwyg', 'edytor' )
        ->set_label( __( 'Edytor', 'pwp' ) )
        ->set_class( 'klasa' )->set_options( array('tinymce' => true) );

$admins->add_options_group( $options, 'test-options' );
$admins->add_options_group( $options, 'nowy-tab' );
$admins->add_tab( 'Inny tab', 'test-options' );

$options_tabs = new Options();
        $options_tabs->set_name( 'tab_options' )
                ->set_action( 'options.php' )
                ->set_title( __( 'opcje w tabie', 'pwp' ) );

        $options_tabs->add_element( 'text', 'tekstt' )
                    ->set_label( __( 'pole w tab', 'pwp' ) )
                    ->set_class( 'klasa' )
                    ->set_validator( array( 'notempty' ) );

$options_tabs->add_element( 'select', 'selekt' )
                    ->set_label( __( 'wybor', 'pwp' ) )
                    ->set_class( 'klasa' )
                    ->set_options( get_roles() );


//	$options_tabs->add_element( 'folder', 'teczka' )
//                    ->set_label( __( 'Folder', 'pwp' ) )
//                    ->set_class( 'klasa' );

	$options_tabs->add_element( 'attachment', 'zalacznik' )
                    ->set_label( __( 'Załacznik', 'pwp' ) )
                    ->set_class( 'klasa' )
                    ->set_validator( array( 'notempty' ) );

        $options_tabs->add_element( 'image', 'obrazek' )
                    ->set_label( 'Obrazek' )
                    ->set_comment( 'komentarz' )
                    ->set_validator( array( 'notempty' ) );

	$elements_repeater = array(
            array(
                'type' => 'text',
		'name' => 'user_email_templates',
		'params'=> array(
                    'label' => __( 'User email templatex', 'pwp' ),
                    'class' => 'large-text',
                ),
            ),
	    array(
		'type' => 'text',
		'name' => 'zalacznik',
		'params'=> array(
                    'label' => __( 'Ue', 'pwp' ),
                    'class' => 'large-text',
                ),
	    )
        );

        $options_tabs->add_element( 'repeatable', 'powtorz' )
                    ->set_title( 'Powtarzalne' )
                    ->set_comment( 'komentarz do repeatable' )
                    ->set_repeater( $elements_repeater );
        $admins->add_options_group( $options_tabs, 'inny-tab' );
    }
}