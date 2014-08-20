<?php

add_action( 'pwp_init_cookie-alert', array('Cookiealert', 'init'));

class Cookiealert {
    
    static $instance = null;

    public function __construct() {
	add_action( 'wp_ajax_okcookie', array($this,'okcookie' ));
	add_action( 'wp_ajax_nopriv_okcookie', array($this,'okcookie' ));
        
        add_action( 'template_redirect', array($this, 'enqueue_media_cookie') );
        //add_action( 'plugins_init', 'ssession_start' );

        add_action('get_footer', array($this,'pwp_cookie_alert'));
        add_shortcode('cookie_info', array($this,'pwp_cookie_alert'));
        
        $this->settings();
    }

    public static function init() {
        if ( is_null( self::$instance ) )
            self::$instance = new Cookiealert();
        return self::$instance;
    }
    
    function okcookie(){
        if ( !session_id() ) {
	//session_start();
        
        Pwp::load_module('session');
        
    }
    $_SESSION['cookies'] = true;
    wp_send_json( array( 'cookies' => true ) );
}






    private function settings(){
        
        $default['message'] = __( 'This site uses cookies. By using this website you agree the use of cookies, according to the current browser settings. You can be changed at any time.', 'pwp' );
        $default['button_label'] = __( 'I accept cookies on this site.', 'pwp' );
        
        $cookie_options = get_option('cookie_options', true);
        if(isset($cookie_options['message']) && $cookie_options['message'] != ''){
            $this->options['message'] = $cookie_options['message'];
        }else{
            $this->options['message'] = $default['message']; 
        }
        
       
        
        if(isset($cookie_options['button_label']) && $cookie_options['button_label'] != ''){
            $this->options['button_label'] = $cookie_options['button_label'];
        }else{
            $this->options['button_label'] = $default['button_label']; 
        }
       
        $admins = new Administrator();
        $page = array(
            //'parent_slug'   => 'edit.php?post_type=form',
            'page_title'    => __( 'Cookie alert settings', 'pwp' ),
            'menu_title'    => __( 'Cookie alert', 'pwp' ),
            'capability'    => 'manage_options',
            'menu_slug'	    => 'cookie-alert-options',
            'icon'	    => '',
            'position'	    => null
        );
        $admins->add_page( $page );
        //$admins->add_tab( 'Nowy tab inny', 'form-options-inny');
        $options = new Options();
        $options->set_name('cookie_options')->set_action('options.php')->set_title(__('Ustawienia alertu cookies', 'pwp'));
        $a = $options->add_element('textarea', 'message');
	    $a->set_label( __( 'Message', 'pwp' ));
            $a->set_comment(__('If you leave the field blank is used, the message', 'pwp').':<br><strong>'.$default['message'].'</strong>');
            $a->set_class('large-text');
	
        $options->add_element('text', 'button_label')
            ->set_label( __( 'Button label', 'pwp' ))
            ->set_comment(__('If you leave the field blank is used, the label','pwp').': <strong>'.$default['button_label'].'</strong>')
            ->set_class('regular-text');
	//dump($options);
        $admins->add_options($options, 'cookie-alert-options');
        $admins->add_section(array('title' => __('Automatic message about the use of cookies','pwp'),'content' => __('The module attaches to the service required by law message about the use of cookies Football. The message is automatically displayed once for each user session and hidden by clicking the accept button.<br>Options allow you to change the display message and labels the accept button.','pwp')), 'cookie-alert-options');
    }



        
function pwp_cookie_alert_content($content) {
    $this->pwp_cookie_alert();
    return $content;
}



function pwp_cookie_alert() {
    if ( !session_id() ) {
        //session_start();
        Pwp::load_module('session');
    }

    if ( !isset( $_SESSION['cookies'] ) || $_SESSION['cookies'] != true ) {
	?>
	<div class="hidden-print clearfix">
    <div id="cookieinfo" class="panel-default cookieinfo ">
	<div class="panel-body">
	    <p><?php echo $this->options['message']; ?></p>
	    <div class="text-center">
		<a id="cookies-ok" href="#" class="btn btn-primary btn-sm okcookie "><?php echo $this->options['button_label']; ?></a>
	    </div>
	</div>
    </div>
</div> 
	<?php
    }
    
}



    
    
    
function enqueue_media_cookie() {
    wp_enqueue_script( 'cookie-info', plugins_url( 'cookie-alert.js', __FILE__ ), array( 'jquery' ) );
    //wp_localize_script( 'cookie-info', 'pwpax', array( 'ajaxurl' => admin_url( 'admin-ajax.php'),
     //       'cookie_sec' => wp_create_nonce( 'ok_cookie'  ) ) ) ;
    wp_localize_script( 'cookie-info', 'ajaxurl', admin_url( 'admin-ajax.php') ) ; 
    wp_localize_script( 'cookie-info', 'cookie_sec', wp_create_nonce( 'ok_cookie'  ) ) ;
    //wp_localize_script( 'cookie-info', 'pwpax', array( 'sec' => wp_create_nonce( 'ok_cookie' ) ) );
}

}

