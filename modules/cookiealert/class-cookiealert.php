<?php
/**
   * Cookiealert module class
   *
   * @package    PWP
   * @subpackage Contact
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

class Cookiealert {

    static $instance = null;

    /**
     * domy
     * @return int|arrayslne wartosci parametrow
     * @return array
     */
    static function default_settings(){
	
	$default['message'] = __( 'This site uses cookies. By using this website you agree the use of cookies, according to the current browser settings. You can be changed at any time.', 'pwp' );
        $default['button_label'] = __( 'I accept cookies on this site.', 'pwp' );
	$default['automatic_attach'] = 1;
	return $default;
    }

    /**
     * konstruktor
     */
    public function __construct() {

	$this->cookie_options = get_option( 'cookie_options', true );
	$this->settings_page( self::default_settings() );

	add_action( 'wp_ajax_okcookie', array( $this, 'okcookie' ) );
	add_action( 'wp_ajax_nopriv_okcookie', array( $this, 'okcookie' ) );
        add_action( 'template_redirect', array( $this, 'enqueue_media_cookie' ) );
	add_shortcode( 'cookie_info', array( $this, 'shortcode_cookie_info' ) );
	if( $this->get_automatic_attach() ) {
	    add_action( 'get_footer', array( $this, 'pwp_cookie_alert' ) );
	}
    }

    /**
     * zalacza odpowiednie pliki i zmienne js
     */
    public function enqueue_media_cookie() {
	wp_enqueue_script( 'cookie-info', plugins_url( 'cookie-alert.js', __FILE__ ), array( 'jquery' ), true, true );
	wp_localize_script( 'cookie-info', 'cookie_sec', wp_create_nonce( 'ok_cookie'  ) ) ;
    }

    /**
     * inicjalizacja
     * @return Cookiealert
     */
    public static function init() {
        if( is_null( self::$instance ) ) {
            self::$instance = new Cookiealert();
	}
        return self::$instance;
    }

    /**
     * funkcja wywolana ajaxem po kliknieciu w button zgody na cookie
     * ustawia w sesji akceptacje
     */
    public function okcookie() {
        if( !session_id() ) {
	    Pwp::load_module( 'session' );
	}
	$_SESSION['cookies'] = true;
	wp_send_json( array( 'cookies' => true ) );
    }

    /**
     * pobiera wartosci domyslne parametrow
     * @return string|array
     */
    private function get_default( $key ) {
	
	$default = self::default_settings();
	if( $key ) {
	    return $default[$key];
	} else {
	    return $default;
	}
    }

    /**
     * zwraca komunikat
     * @return string
     */
    private function get_message() {

	if( isset( $this->cookie_options['message'] ) && $this->cookie_options['message'] != '' ) {
            return $this->cookie_options['message'];
        } else {
            return $this->get_default( 'message' );
        }
    }

    /**
     * zwrac
     * @return stringa etykiete buttona
     * @return string
     */
    private function get_label() {
	if( isset( $this->cookie_options['button_label'] ) && $this->cookie_options['button_label'] != '' ) {
            return $this->cookie_options['button_label'];
        } else {
            return $this->get_default('button_label');
        }
    }

    /**
     * ustala czy automatycznie dodawac do stopki
     * return string|boolean
     */
    private function get_automatic_attach() {
	if( $this->cookie_options ) {
	    if( isset( $this->cookie_options['automatic_attach'] ) ) {
		return $this->cookie_options['automatic_attach'];
	    } else {
		return false;
	    }
        } else {
            return $this->get_default( 'automatic_attach' );
        }
    }

    /**
     * ustawienia strony administracyjnej
     */
    private function settings_page() {

	$cookie_admin = new Administrator();
        $page = array(
	    'page_title'    => __( 'Cookie alert settings', 'pwp' ),
            'menu_title'    => __( 'Cookie alert', 'pwp' ),
            'capability'    => 'manage_options',
            'menu_slug'	    => 'cookie-alert-options',
        );
        $cookie_admin->add_page( $page );
        $options = new Options();
        $options->set_name( 'cookie_options' )
		->set_action( 'options.php' )
		->set_title( __( 'Ustawienia alertu cookies', 'pwp' ) );
        
	$options->add_element( 'textarea', 'message' )
		->set_label( __( 'Message', 'pwp' ) )
		->set_comment( __( 'If you leave the field blank is used, the message', 'pwp' ) . ':<br><strong>' . $this->get_default( 'message' ) . '</strong>' )
		->set_class( 'large-text' );

	$options->add_element( 'text', 'button_label' )
		->set_label( __( 'Button label', 'pwp' ) )
		->set_comment( __( 'If you leave the field blank is used, the label', 'pwp' ) . ': <strong>' . $this->get_default( 'button_label' ) . '</strong>')
		->set_class( 'regular-text' );

	$options->add_element( 'checkbox', 'automatic_attach' )
		->set_label( __( 'In footer', 'pwp' ) )
		->set_comment( __( 'Automatically attach a message to the footer', 'pwp' ) )
		->set_value( $this->get_automatic_attach() );

	$cookie_admin->add_options_group( $options, 'cookie-alert-options' );
        $cookie_admin->add_section( array( 'title' => __( 'Automatic message about the use of cookies', 'pwp' ), 'content' => __( 'The module attaches to the service required by law message about the use of cookies. The message is automatically displayed once for each user session and hidden by clicking the accept button.<br>Options allow you to change the display message and labels the accept button.', 'pwp' ) ), 'cookie-alert-options' );
    }

    /**
     * metoda obslugujaca shortcode
     */
    public function shortcode_cookie_info() {
	$this->pwp_cookie_alert();
    }

    /**
     * wyswietla alert
     */
    public function pwp_cookie_alert() {
	if ( !session_id() ) {
	    Pwp::load_module( 'session' );
	}
	if( !isset( $_SESSION['cookies'] ) || $_SESSION['cookies'] != true ) {
	    $body =
		'<div class="hidden-print clearfix">
		    <div id="cookieinfo" class="panel-default cookieinfo">
			<div class="panel-body">
			    <p>' . $this->get_message() . '</p>
			    <div class="text-center">
				<a id="cookies-ok" href="#" class="btn btn-primary btn-sm okcookie ">' . $this->get_label() . '</a>
			    </div>
			</div>
		    </div>
		</div>';
	    echo $body;
	}
    }
}