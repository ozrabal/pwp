<?php

add_action( 'pwp_init_contact', array( 'Contact','init' ) );

function form( $args ) {

    if( isset( $args ) ) {
	$contact = new Contact( $args );
    }
}
function form_shortcode($atts){
extract( shortcode_atts( array(
	      'args' => '',
	      
     ), $atts ) );
form( $atts['name'] );
}
add_shortcode('form', 'form_shortcode');

interface Observer {
    function update( $data );
}
interface Observable {
    function attach( Observer $observer );
    function detach( Observer $observer );
    function notify();

}

add_filter( 'user_can_richedit', 'disable_for_cpt' );
function disable_for_cpt( $default ) {
    global $post;
    if ( 'form' == get_post_type( $post ) )
        return false;
    return $default;
}

///implements SplSubject
class Contact extends Form implements Observable {

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
        
        
//        function myformatTinyMCE($in)
//{
// $in['plugins']='inlinepopups,tabfocus,paste,media,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpfullscreen';
// $in['wpautop']=true;
// $in['apply_source_formatting']=false;
// $in['theme_advanced_buttons1']='formatselect,forecolor,|,bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,|,wp_fullscreen,wp_adv';
// $in['theme_advanced_buttons2']='pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo';
// $in['theme_advanced_buttons3']='';
// $in['theme_advanced_buttons4']='';
// return $in;
//}
//add_filter('tiny_mce_before_init', 'myformatTinyMCE' );
        
        //pola metabox w typie postu form
        $metad = array(
            array(
                'name'      => 'pwp_form',
                'title'     => __( 'Form parameters', 'pwp' ),
                //'callback'=> '',
                'post_type' => 'form',
                'elements'  => array(
//		    array(
//                        'type' => 'image',
//                        'name' => 'user_image',
//                        'params'=> array(
//                            'label' => __( 'User image', 'pwp' ),
//                            'class' => 'large-text',
//			    'validator' => array(
//				'notempty'
//			    ),
//                         ),
//                    ),
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


	   $elements_repeater = array(
		array(
		    'type' => 'text',
		    'name' => 'user_email_template',
		    'params'=> array(
			'label' => __( 'User email template', 'pwp' ),
			'class' => 'large-text',

		    ),
		));


        $taxmeta = array(
            
                'name'      => 'category',
            
                'title'     => __( 'Category meta', 'pwp' ),
                //'callback'=> '',
                
                'elements'  => array(
		    array(
                        'type' => 'text',
                        'name' => 'text_field',
                        'params'=> array(
                            'label' => __( 'Tekst', 'pwp' ),
                            'class' => 'large-text',
			    'validator' => array(
				'notempty'
			    ),
                         ),
                    ),
		    array(
		    'type' => 'repeatable',
		    'name' => 'powtorz',
		    'params'=> array(
			'title' => __( 'Powtarzalne', 'pwp' ),
			'label' => __( 'powtarzalne pole', 'pwp' ),
			'class' => 'large-text',
			'validator' => array(
				'notempty'
			    ),
			'options' => $elements_repeater
		    ),
		),




        ));
        
        //new Taxmeta($taxmeta);
        
    }

    private function get_definition( $slug ){
        if( !empty( $slug )  ){
            $arg = array(
                'name'          => sanitize_key( $slug ),
                'post_type'     => 'form',
                'post_status'   => 'publish',
                'numberposts'   => 1
            );
            $form_definition = get_posts( $arg );
            if( !empty( $form_definition ) ){
                $meta = get_post_meta( $form_definition[0]->ID, 'pwp_form', true );
                
                //dump($form_definition);
                
                
                
                $this->user_email_template = $meta['user_email_template'];
                $this->admin_email_template = $meta['admin_email_template'];
                $this->user_email_subject = $meta['user_email_subject'];
                $this->admin_email_subject = $meta['admin_email_subject'];
                $this->recipient = $meta['recipient'];
                $this->send_to_user = $meta['send_to_user'];
                $this->message_form_send = $meta['message_form_send'];
                return $meta['definition'];
            }
        }
        
        echo ( __( 'Form definition not found, invalid form name (slug)', 'pwp' ) );
        return false;
    }

    public function attach( Observer $observer) {
        $this->observers[] = $observer;
    }
 
    public function detach(  Observer $observer) {
        unset($this->observers);
    }
 
    public function notify() {
	$result = false;
	$request = $this->get_request();
	if(isset($request['event'])){
	    $p = get_post(intval($request['event']));
	    $request['tytul'] = $p->post_title;
	}
        $result = null;
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
        if( isset( $_REQUEST['content'] ) ){
            global $post;
            do_shortcode( stripslashes( $_REQUEST['content'] ) );
               
            if( isset( $this->shortcode['elements'] ) ) {
                foreach( $this->shortcode['elements'] as $key => $field ) {
                    if( isset( $this->repeatable[$field['name']] ) ) {
                        unset($this->repeatable[$field['name']]['repeatable']);
                        $this->shortcode['elements'][$key]['params']['options'] = $this->repeatable[$field['name']];
                    }
                }
            }
            $meta = get_post_meta( $post->ID, 'pwp_form', true ); 
            $meta['definition'] = $this->shortcode;
            update_post_meta($post->ID, 'pwp_form', $meta);
        }
    } 

    public function __construct( $args = null) {
        $this->options = Options::get_instance();
        
        //strona administracyjna z zakladkami
	//
	//$admin = Administrator::init();
        $admin = new Administrator();
        $page = array(
            'parent_slug'   => 'edit.php?post_type=form',
            'page_title'    => __( 'Form settings page', 'pwp' ),
            'menu_title'    => __( 'Form settings', 'pwp' ),
            'capability'    => 'manage_options',
            'menu_slug'	    => 'form-options',
            'icon'	    => '',
            'position'	    => null,
        );
        //$admin->add_page( $page );
        
        $admin->add_tab( 'Nowy tab', 'form-options' );
        $options = new Options();
        $options->set_name( 'a_options' )
                ->set_action( 'options.php' )
                ->set_title( __( 'Pierwsze opcje', 'pwp' ) );

        $options->add_element( 'text', 'tekst' )
                ->set_label( __( 'User email template', 'pwp' ) )
                ->set_class( 'klasa' )
                ->set_validator( array( 'notempty' ) );

        $admin->add_options( $options, 'form-options' );
        $admin->add_options( $options, 'nowy-tab' );

        $admin->add_tab( 'Inny tab', 'form-options' );

        $options_tab = new Options();
        $options_tab->set_name( 'tab_options' )
                ->set_action( 'options.php' )
                ->set_title( __( 'opcje w tabie', 'pwp' ) );

        $options_tab->add_element( 'text', 'tekstt' )
                    ->set_label( __( 'pole w tab', 'pwp' ) )
                    ->set_class( 'klasa' )
                    ->set_validator( array( 'notempty' ) );

        $options_tab->add_element( 'image', 'obrazek' )
                    ->set_label( 'Obrazek' )
                    ->set_comment( 'komentarz' )
                    ->set_validator( array( 'notempty' ) );
        $elements_repeater = array(
            array(
                'type' => 'text',
		'name' => 'user_email_template',
		'params'=> array(
                    'label' => __( 'User email templatez', 'pwp' ),
                    'class' => 'large-text',
                ),
            ),
        );

        $options_tab->add_element( 'repeatable', 'powtorz' )
                    ->set_label( 'Powtarzalne' )
                    ->set_comment( 'komentarz do repeatable' )
                    ->add_elements( $elements_repeater );
        $admin->add_options( $options_tab, 'inny-tab' );
        
        if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) && 'form' == pwp_current_post_type() ) {  
            add_shortcode( 'form', array( $this, 'add_form' ) );
            add_shortcode( 'field', array( $this, 'add_field' ) );
            add_action( 'save_post', array( $this, 'onsave' ) ,0 );
        }

        if( isset( $args ) ) {
            if( !is_array( $args ) ) {
                $params = $this->get_definition( $args );
            } else {
                $params = $args;
            }
            if(  is_array( $params )){
            if( isset( $params['callback'] ) ) {
                
                //dump($params['callback']);
                foreach ($params['elements'] as $element){
                    if($element['type'] == 'file'){
                        //dump($element);
                        $n = array('Observer_Upload', $params['callback']);
                         $params['callback'] = $n;
                    }
                }
                
                $this->callback = $params['callback'];
                if( is_array( $this->callback ) ) {
                    foreach( $this->callback as $callback ) {
                        $c = new $callback();
                        $this->attach( $c );
		    }
                    //$this->attach( $cal );
                } else {
                    $n = new $this->callback();
                    $this->attach( $n );
                }
            }
            parent::__construct( $params );
        }
        }
    }

    public function render(){
        parent::render();
    }

    function save(){        
    }
    
    public function submit(){
        
        
	if( $this->notify() ) {
	    echo '<div class="alert alert-success">'.$this->message_form_send.'</div>';
	}
    }
}
$contact = new Contact();



/*
 [form name="rezerwacja" callback="Observer_Email"]
[field type="file" name="plik" label="zalacznik"]

[field type="comment" name="info" value="<small>Prosimy o podanie imienia i nazwiska oraz adresu e-mail lub numeru telefonu. W polu uwagi prosimy podać rodzaj biletu (np. przedsprzedaż), cenę itp.</small>" container="form-group"]
[field type="text" name="imieinazwisko" validator="notempty" container="form-group" class="form-control" label="Imię i nazwisko"]
[field type="email" name="email" validator="notempty,email" container="form-group" class="form-control" label="Adres email"]
[field type="text" name="telefon" container="form-group" class="form-control" label="Numer telefonu"] [field type="select" name="iloscbiletow" container="form-group" class="form-control input-sm" label="Liczba rezerwowanych biletów" options="1|1,2|2,3|3,4|4,5|5,6|6,7|7,8|8,9|9,10|10"] [field type="textarea" name="uwagi" container="form-group" class="form-control" label="Uwagi"] [field type="comment" name="komentarz-newsletter" container="form-group" value="<small>Aby otrzymywać bieżące informacje o wydarzeniach w klubie Blue Note zapisz się do naszego newslettera</small>"] [field type="checkbox" name="newsletter" container="form-group" label="Chcę otrzymywać newsletter"] [field type="hidden" name="event" callback="get_the_ID,value" ] [field type="submit" name="tekst-submit" container="form-group" class="btn btn-primary btn-sm" value="Wyślij rezerwację"]


<table style="border: solid 1px #666666; font-family: Arial, Helvetica, sans-serif; font-size: 14px;" width="600" border="0" cellspacing="0" cellpadding="7" align="center">
<tbody>
<tr>
<td style="font-size: 14px; font-weight: bold; color: #666666; border-bottom: 4px solid #EAECF1;" colspan="2">[tytul]</td>
</tr>
<tr>
<td style="padding: 10px;" width="20%">Imię i nazwisko</td>
<td style="padding: 10px;" width="80%">[imieinazwisko]</td>
</tr>
<tr>
<td style="padding: 10px;">E-mail</td>
<td style="padding: 10px;">[email]</td>
</tr>
<tr>
<td style="padding: 10px;">Telefon</td>
<td style="padding: 10px;">[telefon]</td>
</tr>
<tr>
<td style="padding: 10px;">Ilość biletów</td>
<td style="padding: 10px;">[iloscbiletow]</td>
</tr>
<tr>
<td style="padding: 10px;">Uwagi</td>
<td style="padding: 10px;">[uwagi]</td>
</tr>
</tbody>
</table>
 *
 */