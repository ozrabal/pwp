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
        add_action('media_buttons',  array('Contact','add_my_custom_button'), 12);
        //wp_enqueue_script('media_button', plugins_url( '/contact.js', __FILE__ ), array('jquery'), '1.0', true);
        //add_action( 'wp_ajax_choice', array('Contact','choice'));
        //add_action( 'wp_ajax_nopriv_choice', array('Contact','choice'));

	$base = plugin_dir_url( __FILE__ );
				wp_enqueue_script( 'iframe_modal' , $base . 'modal.js' , array( 'jquery' ), true );
				
				wp_enqueue_style( 'iframe_modal' , $base . 'modal.css' );
				wp_localize_script(
					'iframe_modal' ,
					'aut0poietic_iframe_modal_l10n',
					array(
						"close_label" => __( 'Close Dialog' , 'iframe_modal' )
					)
				);
		add_action( 'wp_ajax_modal_frame_content' , array('Contact', 'modal_frame_content' ) );


		//add_action('admin_head', array( 'Contact', 'admin_head') );
			//add_action( 'admin_enqueue_scripts', array('Contact' , 'admin_enqueue_scripts' ) );
wp_enqueue_style('bs3_panel_shortcode', plugins_url( 'css/mce-button.css' , __FILE__ ) );
add_filter( 'mce_external_plugins', array( 'Contact' ,'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array('Contact', 'mce_buttons' ) );


    }
    static function mce_buttons( $buttons ) {
		array_push( $buttons, 'bs3_panel' );
		return $buttons;
	}

static function admin_head() {
		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			
		}
	}
static function mce_external_plugins( $plugin_array ) {
		$plugin_array['bs3_panel'] = plugin_dir_url( __FILE__ ).'mce-buttons.js';
		return $plugin_array;
	}
    static function modal_frame_content() {
			wp_enqueue_style( 'iframe_modal-content' , plugin_dir_url( __FILE__ ) . 'modal-content.css' );
			wp_enqueue_script( 'iframe_modal-content' , plugin_dir_url( __FILE__ ) . 'modal-content.js' , array( 'jquery' ) );
			include( 'modal-content.php' );
			die(); // you must die from an ajax call
		}



    static function get_template_data() {
	$base = plugin_dir_url( __FILE__ );
	dump($base);
			include( $base .'template-data.php' );
			die(); // you must die from an ajax call
		}
    static function choice() {
         wp_enqueue_script('jquery');
    add_thickbox();
    $context = '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="/wp-admin/admin-ajax.php#?action=choice&width=150&height=100&TB_iframe=true">
    <span class="wp-media-buttons-icon dashicons dashicons-exerpt-view"></span>'.__('Add form element', 'pwp').'</a>';

 $map_args  = array(
	'post_type'	=> 'form',
	'post_status'	=> 'publish',
	
    );
    $map_query = new WP_Query( $map_args );
    if( $map_query->have_posts() ) {
	
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#advanced_dlg.colorpicker_title}</title>
	<script type="text/javascript" src="<?php echo '../wp-includes/js/tinymce/tiny_mce_popup.js?ver=358-2012120'; ?>"></script>
	
	<?php //wp_head() 
        
        ?>
</head>
<body id="colorpicker" style="" role="application" aria-labelledby="app_label">
	<span class="mceVoiceLabel" id="app_label" style="display:none;">{#advanced_dlg.colorpicker_title}</span>
<form onsubmit="insertAction();return false" action="#">
	    ajaxem

	    <?php echo $context; 
            wp_enqueue_script('jquery');
            while ( $map_query->have_posts() ) {
	    $map_query->the_post();
	    ?>
           
            <br><a href="">
            <?php the_title(); ?>
            </a>
            
            <?php
            }
   
            
            
            
            
            
            ?>
</form>
<p>
  <a class="button choice_button" id="Black">Black</a>
  <a class="button choice_button" id="White">White</a>
</p>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $('.choice_button').on('click',
    function () {
          tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
        var choice = $(this).attr('id');
        tb_remove();
        alert(choice);
        $('.your-choice').html(choice);
      }
    );
  });
</script>
	</body>
    </html>




<?php
            //wp_footer();
 }
  die();
}
     static function add_my_custom_button($context) {
  
  
  //the id of the container I want to show in the popup
  
  
  //if($screen->post_type == 'form'){
  $container_id = 'element-form';
    //append the icon
//  $context .= '&nbsp;&nbsp;&nbsp;&nbsp;<a class="thickbox button " title="'.__('Start form', 'pwp').'" href="#TB_inline?width=100&inlineId=form-form">
//    <span class="wp-media-buttons-icon dashicons dashicons-format-aside"></span>'.__('Start form', 'pwp').'</a>';
//
//
//  $context .= '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="#TB_inline?width=400&inlineId='.$container_id.'">
//    <span class="wp-media-buttons-icon dashicons dashicons-exerpt-view"></span>'.__('Add form element', 'pwp').'</a>';
//  
   $context .= '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="/wp-admin/admin-ajax.php?&action=choice&width=150&height=100&TB_iframe=true">
    <span class="wp-media-buttons-icon dashicons adashicons-exerpt-view"></span>'.__('Dodaj do edytora', 'pwp').'</a>';

   			print sprintf(
				'<input type="button" class="button  " id="open-iframe_modal" value="%1$s" data-content-url="%3$s%2$d">' ,
				__( 'Open IFrame Modal' , 'iframe_modal' ) ,
 get_the_ID() ,
				admin_url( 'admin-ajax.php?action=modal_frame_content&post_id=' ) );


//echo $context;
//print sprintf(
//				'<input type="button" class="button button-primary " id="open-backbone_modal" value="%1$s">' ,
//				__( 'Open Backbone Modal' , 'backbone_modal' )
//			);
  //return $context;
  //}
}
    
    
    
    
    static function add_my_media_button() {
        
    echo '<a href="#" id="insert-my-media" class="button">Add form</a>';
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
	$this->user_email_field = get_post_meta( $form_id,'user_email_field',true );
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
	    if($callback_class == 'Callback_Mail'){
		add_filter('wp_mail_from',array($this, 'filter_wp_mail_from'));
		add_filter('wp_mail_from_name',array($this, 'filter_wp_mail_from_name'));
	    }
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
	parent::submit();
	if( $this->notify() ) {
	    echo '<div class="alert alert-success">' . $this->message_form_send . '</div>';
	}
    }


function filter_wp_mail_from_name($name) {
  return 'Helen Hou-Sandi';
}

    
function filter_wp_mail_from($content_type) {
  return 'helenyhou@example.com';
}

public function file($name){
    return $name.'dupa';
}

    public function notify() {

	
	
	//add_filter('callback_pdf_filename', array($this,'file'));

	$params = new stdClass();
	//$params->start = __METHOD__;
$params->request = $this->get_request();
	$params->user_email = $this->get_request($this->user_email_field);
	    $params->user_subject = $this->user_email_subject;
	    
$params->admin_body = $this->admin_email_template;

$params->user_body = $this->user_email_template;
	if( $params->admin_body != '' ) {
            foreach( $this->get_request( ) as $k => $v ) {
		//dump($k);
                if( !is_array( $this->get_request( $k ) ) ) {
                    //$user_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $user_body );
                    $params->admin_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $params->admin_body );



		} else {
                    //@todo polapowtarzalne do szablonow email
		    $el = $this->get_request( $k );

$params->admin_body = str_ireplace( '['.$k.']',  $el['url'], $params->admin_body );

                }

		//dump($params->admin_body) .'<br>';
            }
        } else {
            foreach( $this->get_request( ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    $params->admin_body .= $k . ' : ' . $v . '<br>';
                }
            }
        }

if( $params->user_body != '' ) {
            foreach( $this->get_request( ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    //$user_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $user_body );
                    $params->user_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $params->user_body );



		} else {
                    //@todo polapowtarzalne do szablonow email

		    $el = $this->get_request( $k );

$params->user_body = str_ireplace( '['.$k.']',  $el['url'], $params->user_body );
                }
            }
        } else {
            foreach( $this->get_request( ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    $params->user_body .= $k . ' : ' . $v . '<br>';
                }
            }
        }



	    //$params->user_attachment;
	    $params->admin_email = $this->recipient;
	    $params->admin_subject = $this->admin_email_subject;
	    //$params->admin_body = $this->admin_email_template;
	    //$params->admin_attachment;







	foreach( $this->callback_array as $callback ) {
	    $params = $callback->do_callback( $params );
	}
	return $params;
    }


    /**
     * wywoluje akcje w funkcji obslugujacej po submicie formularza
     * @return boolean
     */
    public function anotify() {

	$result = null;
	foreach( $this->callback_array as $callback ) {
	    $result = $callback->do_callback( array(
		    
		    'user_email_template'   => $this->user_email_template ,
		    'user_email_field'   => $this->user_email_field ,
		    'admin_email_template'  => $this->admin_email_template,
		    'user_email_subject'    => $this->user_email_subject,
		    'admin_email_subject'   => $this->admin_email_subject,
		    'send_to_user'	    => $this->send_to_user,
		    'recipient'		    => $this->recipient,
		    'request'		    => $this->get_request()
		),
		   $result
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
                array(
                    'type' => 'text',
                    'name' => 'user_email_field',
		  
                    'params'=> array(
                        'label' => __( 'Field of user email', 'pwp' ),
                        'class' => 'large-text',
			'comment' => __( "Name of the field that contains the user's email address", 'pwp' )

		    ),

                ),
                array(
                    'type' => 'text',
                    'name' => 'user_email_subject',
                    'params'=> array(
                        'label' => __( 'Subject of user email', 'pwp' ),
                        'class' => 'large-text'
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