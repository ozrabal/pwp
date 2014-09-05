<?php

add_action( 'pwp_init_contact', array( 'Contact', 'init' ) );

function form( $args ) {
    if( isset( $args ) ) {
	new Contact( $args );
    }
}

add_shortcode( 'form', 'form_shortcode' );
function form_shortcode( $atts ) {
    extract( shortcode_atts( array(
		'args' => '',
	    ), $atts )
    );
    form( $atts['name'] );
}