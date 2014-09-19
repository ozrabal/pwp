<?php
/**
   * Cart module class
   *
   * @package    PWP
   * @subpackage Cart
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

class Cart {

    static $instance = null;

    /**
     * Inicjalizacja modulu
     * singleton
     * @return Cart
     */
    static function init() {
	if( is_null( self::$instance ) ) {
            self::$instance = new Cart();
	}
        return self::$instance;
    }


    public function __construct() {
	if( !session_id() ) {
	    Pwp::load_module( 'session' );
	}
	
	$_SESSION['cart'] = $this;

	//$this->cart

	wp_enqueue_style( 'dashicons' );
	add_action( 'the_content', array( $this, 'add_cart_button' ) );

	//add_action( 'get_template_part_content', array( $this, 'add_cart_panel' ) );

	register_widget( 'Cart_Widget' );
    }

    public function add_cart_button( $content ) {
	
	$content .= '<button class="btn">Dodaj do koszyka <div class="dashicons dashicons-cart"></div></button>';
	return $content;
    }

    public function cart_panel () {
	dump($this);
	echo 'zobacz koszyk';
    }
    
    
}