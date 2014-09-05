<?php
/**
   * Contact module
   * 
   * Modul wyswietla oraz obsluguje formularze po stronie frontendu 
   * 
   * @package    PWP
   * @subpackage Contact
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

add_action( 'pwp_init_contact', array( 'Contact', 'init' ) );
/**
 * funkcja wywolujaca formularz
 * mozna uzywac w szablonie podajac jako argument slug postu typu form zawierajacego definicje formularza
 * 
 * @param string $args
 */
function form( $args ) {
    if( isset( $args ) ) {
	new Contact( $args );
    }
}

add_shortcode( 'form', 'form_shortcode' );
/**
 * funkcja obslugujaca shortcode [form name="foo"]
 * 
 * @param string $atts
 */
function form_shortcode( $atts ) {
    extract( shortcode_atts( array(
		'args' => '',
	    ), $atts )
    );
    form( $atts['name'] );
}