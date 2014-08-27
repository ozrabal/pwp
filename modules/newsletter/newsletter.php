<?php
/**
 * Widget Newsletter subscription form
 * Display form for subscribe newsletter with link to rules
 * 
 * @package PWP
 * 
 */

add_action( 'pwp_init_newsletter', 'pwp_init_newsletter' );

function pwp_init_newsletter() {
    register_widget( 'Newsletter_Widget' );
}