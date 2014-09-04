<?php

		//add_action( 'edit_form_advanced', 'my_second_editor' );
function my_second_editor() {
	// get and set $content somehow...
	wp_editor( $content, 'mysecondeditor' );

	wp_editor( $content, 'mysecondeditors' );
}




//action to add a custom button to the content editor
public function add_my_custom_button($context) {
  
  
  //the id of the container I want to show in the popup
  
  
  //if($screen->post_type == 'form'){
  $container_id = 'element-form';
    //append the icon
  $context .= '&nbsp;&nbsp;&nbsp;&nbsp;<a class="thickbox button " title="'.__('Start form', 'pwp').'" href="#TB_inline?width=100&inlineId=form-form">
    <span class="wp-media-buttons-icon dashicons dashicons-format-aside"></span>'.__('Start form', 'pwp').'</a>';
  
  
  $context .= '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="#TB_inline?width=400&inlineId='.$container_id.'">
    <span class="wp-media-buttons-icon dashicons dashicons-exerpt-view"></span>'.__('Add form element', 'pwp').'</a>';
   $context .= '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="/wp-admin/admin-ajax.php?&action=choice&width=150&height=100&TB_iframe=true">
    <span class="wp-media-buttons-icon dashicons dashicons-exerpt-view"></span>'.__('Add form element', 'pwp').'</a>';




  return $context;
  //}
}
public function register_button( $buttons ) {
   array_push( $buttons, "|",'addform' ,"formcreator" );
   return $buttons;
}
public function form_popup_content(){
    ?>
   
    <div id="form-form" style="display:none;">
        <table id="form-table" class="form-table ">
            <tr>
		<th><label for="form-name">aa<?php _e( 'Form name', 'pwp' ); ?></label></th>
		<td><input type="text" id="form-name" name="name" value="<?php  ?>" /><br />
		<small><?php _e( 'Form slug name', 'pwp' ); ?></small></td>
            </tr>
            <tr>
		<th><label for="form-recipient"><?php _e( 'Message recipient', 'pwp' ); ?></label></th>
                <td><input type="text" id="form-recipient" name="recipient" value="<?php echo get_bloginfo( 'admin_email' ); ?>" /><br />
		<small><?php _e( 'E-mail address of recipients (comma separated)', 'pwp' ); ?></small></td>
            </tr>
            <tr>
		<th><label for="form-callback"><?php _e( 'Callback class', 'pwp' ); ?></label></th>
		<td><input type="text" id="form-callback" name="callback" value="" /><br />
		<small><?php _e( 'Optional object', 'pwp' ); ?></small></td>
            </tr>
        </table>
	<p class="submit">
            <input type="button" id="form-submit" class="button-primary" value="<?php _e( 'Insert form tag', 'pwp' ); ?>" name="submit" />
	</p>
    </div>
    <div id="element-form" style="display:none;">
        <table id="element-table" class="form-table">
            <tr>
		<th><label for="element-name"><?php _e( 'Name', 'pwp' ); ?></label></th>
		<td><input type="text" id="element-name" name="name" value="" /><br />
		<small><?php _e( 'Form field name', 'pwp' ); ?></small></td>
            </tr>
            <tr>
		<th><label for="element-type">Type</label></th>\
		<td>
                    <select name="type" id="element-type">
                       
                        <option value="text"><?php _e( 'Text', 'pwp' ); ?></option>
                        <option value="textarea"><?php _e( 'Textarea', 'pwp' ); ?></option>
                        <option value="select"><?php _e( 'Select', 'pwp' ); ?></option>
                        <option value="comment"><?php _e( 'Comment', 'pwp' ); ?></option>
                        <option value="checkbox"><?php _e( 'Checkbox', 'pwp' ); ?></option>
                        <option value="hidden"><?php _e( 'Hidden', 'pwp' ); ?></option>
                        <option value="submit"><?php _e( 'Submit', 'pwp' ); ?></option>
                    </select><br />
                    <small><?php _e( 'Choose HTML field type', 'pwp' ); ?></small>
                </td>
            </tr>
            <tr>
		<th><label for="element-value"><?php _e( 'Value', 'pwp' ); ?></label></th>
		<td><input type="text" name="value" id="element-value" value="" /><br />
                <small><?php _e( 'Value (or content in comment field)', 'pwp' ); ?></small></td>
            </tr>
            <tr>
                <th><label for="element-options"><?php _e( 'Options', 'pwp' ); ?></label></th>
		<td><input type="text" name="options" id="element-options" value="" /><br />
		<small><?php _e( 'Options for select field, comma separated (name|value)', 'pwp' ); ?></small></td>
            </tr>
            <tr>
                <th><label for="element-validator"><?php _e( 'Validator', 'pwp' ); ?></label></th>
		<td><input type="text" name="validator" id="element-validator" value="" /><br />
		<small><?php _e( 'Comma separated list of validators', 'pwp' ); ?></small></td>
            </tr>
            <tr>
                <th><label for="element-callback"><?php _e( 'Callback', 'pwp' ); ?></label></th>
		<td><input type="text" name="callback" id="element-callback" value="" /><br />
		<small><?php _e( 'Callback function, format: (function,field for value returned)', 'pwp' ); ?></small></td>
            </tr>
            <tr>\
                <th><label for="element-container"><?php _e( 'CSS container class', 'pwp' ); ?></label></th>
		<td><input type="text" name="container" id="element-container" value="" /><br />
		<small><?php _e( 'CSS class of container div', 'pwp' ); ?></small></td>
            </tr>
            <tr>
                <th><label for="element-label"><?php _e( 'Label', 'pwp' ); ?></label></th>
		<td><input type="text" id="element-label" name="label" value="" /><br />
		<small><?php _e( 'Label of field', 'pwp' ); ?></small></td>
            </tr>
	</table>
	<p class="submit">
            <input type="button" id="form-submit" class="button-primary" value="<?php _e( 'Insert field tag', 'pwp' ); ?>" name="submit" />
	</p>
    </div>
    <script type="text/javascript">
    // handles the click event of the submit button
    jQuery(function(){
        
        
            var table = jQuery('#element-form table');
		jQuery('#element-form #form-submit').click(function(){
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = {
				'name':	'',
				'type': 'text',
				'value': '',
				'validator': '',
				'container': '',
				'label':'',
				'options': '',
				'callback': ''
				};
			var shortcode = '[field';

			for( var index in options) {
				var value = table.find('#element-' + index).val();

				// attaches the attribute to the shortcode only if it's different from the default value
				if ( value !== options[index] )
					shortcode += ' ' + index + '="' + value + '"';
                          
            if(index == 'label'){  
               
                               var label = value;
                           }
			if(index == 'name'){
       var labelvalue = value;
       
                        }
                        
                        
                        table.find('#element-' + index).val('');
                        
        }
 var email_shortcode = ''+label+' : ['+labelvalue+']';
			shortcode += ']';

			// inserts the shortcode into the active editor
                        if( ! tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
  //jQuery('textarea#content').val(shortcode);
  
   
  
  //pwp_form_user_email_template
  
  var win = window.dialogArguments || opener || parent || top;
  
win.send_to_editor(shortcode);



  
}
else {
  tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
}
//do emaili
var original_text = jQuery('#pwp_form_user_email_template').val(); 
  jQuery('#pwp_form_user_email_template').val(original_text + '\r\n' + email_shortcode);
    var original_text = jQuery('#pwp_form_admin_email_template').val(); 
  jQuery('#pwp_form_admin_email_template').val(original_text + '\r\n' + email_shortcode);
			//tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                        //
                        
  // jQuery('#element-form table input').val('');                     
//wyczyscic formu;arze
			// closes Thickbox
			tb_remove();
		});
            });
    </script>
                <?php
}

        add_action( 'wp_ajax_choice', array($this,'choice'));
        add_action( 'wp_ajax_nopriv_choice', array($this,'choice'));
        
        
public function add_plugin( $plugin_array ) {
   $plugin_array['formcreator'] = plugin_dir_url(__FILE__).'/shortcode.php';
   return $plugin_array;
}

function choice() {
    add_thickbox();
    $context = '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="/wp-admin/admin-ajax.php#?action=choice&width=150&height=100&TB_iframe=true">
    <span class="wp-media-buttons-icon dashicons dashicons-exerpt-view"></span>'.__('Add form element', 'pwp').'</a>';


?>
    <html>
	<head></head>
	<body>
	    ajaxem

	    <?php echo $context; ?>


	</body>
    </html>




<?php
  die();
}


public function add_menu(){
    
//    add_settings_section(
//  'section',
//  'Example settings section in reading',
//  array($this->options,'sec'),
//  'form_options'
//);
//    
    
   // add_submenu_page( 'edit.php?post_type=form', __('Form options title', 'pwp'), __('Form options', 'pwp'), 'manage_options', 'form_options', array($this->options,'render') );
    
    
    
    $this->options->set_name('form_options')->set_action('options.php')->set_title(__('Form options', 'pwp'));

    $this->options
	->add_element('text','tekst')
	->set_label( __( 'User email template', 'pwp' ))
	->set_class('klasa')
	->set_validator(array('notempty'));
    
    $this->options
            ->add_element('checkbox', 'check')
            ->set_label('czekbox');

    $this->options
	->add_element('textarea','tekstarea')
	->set_label( __( 'User email tresc', 'pwp' ))
	->set_class('klasa');

    $this->options
	->add_element('checkbox','check')
	->set_label( __( 'dasd', 'pwp' ))
	->set_class('klasa');

    $this->options
            ->add_element('select', 'ilosc')
            ->set_label('etykieta')
            ->set_options(array(
		    'Wybierz ilość biletów' => '0',
		    '1' => '1',
		    '2' => '2',
		    '3' => '3',
		    '4' => '4',
		    '5' => '5',
		    '6' => '6',
		    '7' => '7',
		    '8' => '8',
		    '9' => '9',
		    '10' => '10',
		    ));
    
      
                    $this->options
                            ->add_element('image', 'obrazek')
                            ->set_label('etykieta');
    
    $this->options
                            ->add_element('image', 'obrazek-inny')
                            ->set_label('etykieta-inna');
    
    

    $elements_repeater = array(
		array(
		    'type' => 'text',
		    'name' => 'user_email_template',
		    'params'=> array(
			'label' => __( 'User email template', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
        
        
		array(
		    'type' => 'text',
		    'name' => 'admin_email_template',
		    'params'=> array(
			'label' => __( 'Admin email template', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
		array(
		    'type' => 'text',
		    'name' => 'recipient',
		    'params'=> array(
			'label' => __( 'Form recipient email addres (comma separated)', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
      
                array(
		    'type' => 'textarea',
		    'name' => 'definition',
		    'params'=> array(
			'label' => __( 'Definition', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
	    );


    $this->options
	    ->add_element('repeatable','powtorz')
	    ->set_label('Powtarzalne')
            ->add_elements($elements_repeater)
            //->add_elements($elements_repeater)
            // ->add_elements($elements_repeater)
	    ;
    
    
    
    
    //dump($this->options);
    
    //$this->options->elements['powtorz']->repeat();
	    //->set_options($elements_repeater);
    
    //dump($this->options);
//    $this->options
//	    ->add_element('repetable2','powtorz')
//	    ->set_label('Powtarzalne2')
//	    ->set_options($elements_repeater);
    
}

//                array(
//		    'type' => 'repeatable',
//		    'name' => 'powtorz',
//		    'params'=> array(
//			'title' => __( 'Powtarzalne', 'pwp' ),
//			'class' => 'large-text',
//			'options' => $elements_repeater
//		    ),
//		),
   $elements_repeater = array(
		array(
		    'type' => 'text',
		    'name' => 'user_email_template',
		    'params'=> array(
			'label' => __( 'User email template', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
       
       array(
                  'type' => 'image',
                    'name' => 'obrazek',
                    'params' => array(
                        'label' => 'Obrazek w metaboxie'
                    )
                ),
       
		array(
		    'type' => 'text',
		    'name' => 'admin_email_template',
		    'params'=> array(
			'label' => __( 'Admin email template', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
		array(
		    'type' => 'text',
		    'name' => 'recipient',
		    'params'=> array(
			'label' => __( 'Form recipient email addres (comma separated)', 'pwp' ),
			'class' => 'large-text',
		    ),
		),


	    );


 
//$admin->set;

//$options['display_options'] = new Options();
//        $options['display_options']->set_name('form_options')->set_action('options.php')->set_title(__('Form options', 'pwp'));
//
//
//        $options['display_options']
//                ->add_element('text','tekst')
//                ->set_label( __( 'User email template', 'pwp' ))
//                ->set_class('klasa')
//                
//                ->set_validator(array('notempty'));
//                
//        $options['display_options']
//                ->add_element('text','tekst2')
//                ->set_label( __( 'User email template2', 'pwp' ))
//                ->set_class('klasa2')
//                ->set_validator(array('notempty'));
//
//        $options['display_options']
//                    ->add_element('select', 'ilosc')
//                    ->set_label('etykieta')
//                    ->set_options(array(
//                            'Wybierz ilość biletów' => '0',
//                            '1' => '1',
//                            '2' => '2',
//                            '3' => '3',
//                            '4' => '4',
//                            '5' => '5',
//                            '6' => '6',
//                            '7' => '7',
//                            '8' => '8',
//                            '9' => '9',
//                            '10' => '10',
//                            ));
//
//        $options['display_options']
//                    ->add_element('image', 'obrazek')
//                    ->set_label('Obrazek')
//                    ->set_validator(array('notempty'));
//            $elements_repeater = array(
//		array(
//		    'type' => 'text',
//		    'name' => 'user_email_template',
//		    'params'=> array(
//			'label' => __( 'User email template', 'pwp' ),
//			'class' => 'large-text',
//		    ),
//		),
//            );
//
//        $options['display_options']
//	    ->add_element('repeatable','powtorz')
//	    ->set_label('Powtarzalne')
//            ->add_elements($elements_repeater)
//            //->add_elements($elements_repeater)
//            //->add_elements($elements_repeater)
//	    ;
//
//
//
//
//
//$options['social_options'] = new Options();
//
//$options['social_options']->set_name('s_options')->set_action('options.php')->set_title(__('Social options', 'pwp'));
//
//
//        $options['social_options']
//                ->add_element('text','tekst_inny')
//                ->set_label( __( 'User2', 'pwp' ))
//                ->set_class('klasa')
//                
//                ->set_validator(array('notempty'));
//
//
//
//	$options['inne_options'] = new Options();
//
//$options['inne_options']->set_name('i_options')->set_action('options.php')->set_title(__('Inne options', 'pwp'));
//
//
//        $options['inne_options']
//                ->add_element('text','tekst_jeszczeinny')
//                ->set_label( __( 'User2inny', 'pwp' ))
//                ->set_class('klasa')
//
//                ->set_validator(array('notempty'));
//
//
//        
//$admin->add_options($options, 'form-options');
//
//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
//
//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );


//add_action('admin_menu', array($this, 'add_menu'));

   
   //add_shortcode('repeatable',array($this,'add_repeat'));
       
//add_action('admin_footer', array($this,'form_popup_content'));

//add_action('media_buttons_context', array($this,'add_my_custom_button'));
//add_action('wp_ajax_choice', array($this, 'choice'));

   
   
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body bgcolor="#FFFFFF"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">
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
</body></html>
        
        
        
        
        
    public function send(){




	$head = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body bgcolor="#FFFFFF"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$user = '<table width="600" border="0" cellpadding="7" cellspacing="0" align="center" style="border:none; font-family:Arial, Helvetica, sans-serif; font-size:14px;">';
	$user .= '<tr><td>Zgłoszenie zostało przyjęte, skontaktujemy się z Tobą w celu potwierdzenia rezerwacji.<br> Poniżej znajdują się szczegóły Twojego zgłoszenia.<br><br></td></tr>';
	$user .= '</table>';


	$user_footer_body = '<table width="600" border="0" cellpadding="7" cellspacing="0" align="center" style="border:none; font-family:Arial, Helvetica, sans-serif; font-size:14px;"><tr><td colspan=2 style="padding:10px; font-size: 12px; border-top: 2px solid #EAECF1;">Blue Note Jazz Club<br>ul. Kościuszki 79
(gmach C.K. Zamek)<br>61-891 Poznań<br>rezerwacje:<br>
tel/fax 61 851 04 08<br>
tel/fax 61 657 07 77<br>
<a href="mailto:klub@bluenote.poznan.pl">klub@bluenote.poznan.pl</a></td></tr></table>';
	$end_body = '<table width="600" border="0" cellpadding="7" cellspacing="0" align="center" style="border:none; font-family:Arial, Helvetica, sans-serif; font-size:14px;"><tr><td colspan=2 style="padding:10px;font-size: 10px;font-size: 12px; border-top: 1px solid #EAECF1;">'.__('This is an automated message from reservation system. Please do not reply directly to this email ','pwp').'</td></tr>';

	$end_body .= '</table></body></html>';




//dump($this->get_param('email_template'));

//$body  = file_get_contents(get_template_directory().'/reservation-email.php');


$user_body = $this->get_param('user_email_template');
$admin_body = $this->get_param('admin_email_template');
$recipient = $this->get_param('recipient');

$newsletter_body  = file_get_contents(get_template_directory().'/newsletter-email.php');
dump($newsletter_body);
foreach($this->get_param('request') as $k => $v ){
if(!is_array($this->get_request($k))){

$user_body = str_ireplace('['.$k.']',  $this->get_request($k), $user_body);

$admin_body = str_ireplace('['.$k.']',  $this->get_request($k), $admin_body);


$newsletter_body = str_ireplace('['.$k.']',  $this->get_request($k), $newsletter_body);
}else{
    
}
}
dump($this->get_param('request'));

die();

if(wp_mail($recipient, 'Rezerwacja: '.get_the_title(), $head.$admin_body.$end_body,$this->headers(array('from' => get_option('admin_email'))))){

	    wp_mail($this->get_request('email'), 'Twoja rezerwacja: '.get_the_title(), $head.$user.$user_body.$user_footer_body.$end_body,$this->headers(array('from' => get_option('admin_email'))));


	    if($this->get_request('newsletter')){

wp_mail($this->get_request('email'), 'Newsletter', $head.$newsletter_body.$user_footer_body.$end_body,$this->headers(array('from' => get_option('admin_email'))));

wp_mail($recipient, 'Newsletter', $head.$newsletter_body.$end_body,$this->headers(array('from' => get_option('admin_email'))));



	    }
	    return true;
                //echo'<div class="alert alert-success">'.__('Your message was successfully sent','pwp').'</div>';
	}else{
		return false;
	}




    }
    
    
    ?>
 admin   
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body bgcolor="#FFFFFF"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">
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
</body></html>
         
         
user
<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body bgcolor="#FFFFFF"  style="font-family:Arial, Helvetica, sans-serif; font-size:14px;">
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
<table width="600" border="0" cellpadding="7" cellspacing="0" align="center" style="border:none; font-family:Arial, Helvetica, sans-serif; font-size:14px;"><tr><td colspan=2 style="padding:10px; font-size: 12px; border-top: 2px solid #EAECF1;">Blue Note Jazz Club<br>ul. Kościuszki 79
(gmach C.K. Zamek)<br>61-891 Poznań<br>rezerwacje:<br>
tel/fax 61 851 04 08<br>
tel/fax 61 657 07 77<br>
<a href="mailto:klub@bluenote.poznan.pl">klub@bluenote.poznan.pl</a></td></tr></table>
</body></html>
<?php
//custom walker w edycji dla menu specjalizacji - dodany slug
//add_filter( 'wp_edit_nav_menu_walker', 'pwp_custom_nav_edit_walker', 10, 2 );
//function pwp_custom_nav_edit_walker( $walker,$menu_id ) {
//    return 'Walker_Parammenuedit';
//}
//add_action( 'wp_update_nav_menu_item', 'pwp_custom_nav_update', 10, 3 );
//function custom_nav_update( $menu_id, $menu_item_db_id, $args ) {
//    if( isset($_REQUEST['menu-item-slug']) && is_array($_REQUEST['menu-item-slug']) ) {
//        $slug_value = $_REQUEST['menu-item-slug'][$menu_item_db_id];
//        update_post_meta( $menu_item_db_id, '_menu_item_slug', $slug_value );
//    }
//}
//shortcode galerii botstrapowa karuzela
//new Shortcode_Gallery();

//$content_editor->add_editor_style();
//$content_editor->add_editor_buttons();


////taxonomia rodzaj brand
//add_action( 'after_setup_theme', 'create_type_taxonomies', 0 );
//function create_type_taxonomies() {
//    $labels = array(
//	'name'              => __( 'Types', 'pwp' ),
//	'singular_name'     => __( 'Type', 'pwp' ),
//        'search_items'      => __( 'Search types', 'pwp' ),
//	'all_items'         => __( 'All types','pwp' ),
//        'parent_item'       => __( 'Parent type', 'pwp' ),
//    	'parent_item_colon' => __( 'Parent type:', 'pwp' ),
//    	'edit_item'         => __( 'Edit type', 'pwp' ),
//    	'update_item'       => __( 'Update type','pwp' ),
//    	'add_new_item'      => __( 'Add new type','pwp' ),
//    	'new_item_name'     => __( 'New type' ,'pwp'),
//    	'menu_name'         => __( 'Types','pwp' ),
//    );
//    register_taxonomy( 'type',array(  'resource' ), array(
//        'hierarchical'      => true,
//	'labels'            => $labels,
//	'show_ui'           => true,
//	'query_var'         => true,
//	'show_admin_column' => true,
//	'show_in_nav_menus' => false,
//	'show_tagcloud'     => false,
//	'rewrite'           => array( 'slug' => 'type' ),
//    ));
//}


function wptp_add_categories_to_attachments() {
    register_taxonomy_for_object_type( 'category', 'attachment' );
}
//add_action( 'init' , 'wptp_add_categories_to_attachments' );

// apply tags to attachments
function wptp_add_tags_to_attachments() {
    register_taxonomy_for_object_type( 'post_tag', 'attachment' );
}
//add_action( 'init' , 'wptp_add_tags_to_attachments' );




/**
 * Change Upload Directory for Custom Post-Type
 *
 * This will change the upload directory for a custom post-type. Attachments will
 * now be uploaded to an &quot;uploads&quot; directory within the folder of your plugin. Make
 * sure you swap out &quot;post-type&quot; in the if-statement with the appropriate value...
 */
function custom_upload_directory( $args ) {
if(!empty($_REQUEST['post_id'])){
    $id = $_REQUEST['post_id'];
    $parent = get_post( $id )->post_parent;

    // Check the post-type of the current post
    if( 'resource' == get_post_type( $id ) || 'resource' == get_post_type( $parent ) ) {

        $args['path'] = ABSPATH . '/resources';
        $args['url']  = site_url() . '/resources';
        $args['basedir'] = ABSPATH . '/resources';
        $args['baseurl'] = site_url() . '/resources';
    }
}

    return $args;
}
//add_filter( 'upload_dir', 'custom_upload_directory' );



//add_filter('posts_where', 'limitMediaLibraryItems_56456', 10, 2 );
function slimitMediaLibraryItems_56456($where, &$wp_query) {
    global $pagenow, $wpdb;

    // Do not modify $where for non-media library requests

    //die();
if ( ('resource' == get_post_type( $id ) || 'resource' == get_post_type( $parent )) ) {
         $where .= " AND {$wpdb->posts}.guid LIKE '%resources%'";
 }


    return $where;
}

// Code originally by @t31os
//add_action('pre_get_posts','users_own_attachments');



//add_action('pre_get_posts','users_own_attachments');
function users_own_attachments( $wp_query_obj ) {

    global $current_user, $pagenow;



    //if( !current_user_can('delete_pages') )
        $wp_query_obj->set('guid', "LIKE '%resources%'");

    return;
}

//add_filter('posts_where', 'limitMediaLibraryItems_56456', 10, 2 );
function limitMediaLibraryItems_56456($where) {
    global  $wpdb;

    // Do not modify $where for non-media library requests
    //if ($pagenow !== 'post.php') {
        //return $where;
    //}


    //dump($_POST);
    $b = get_post($_POST['post_id']);
    //dump($b->post_type);
    $predefined = get_post_types(array('_builtin' => true));
     $custom = get_post_types(array('_builtin' => false));
    //dump($predefined);
    if(  !in_array($b->post_type, $predefined )){

             $where .= " AND {$wpdb->posts}.guid LIKE '%{$b->post_type}%'";
    }else{


        //$where .= " AND {$wpdb->posts}.guid ";
        foreach($custom as $type){
            $where .= "AND {$wpdb->posts}.guid NOT LIKE '%{$type}%' ";
        }


    }




    return $where;
}




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