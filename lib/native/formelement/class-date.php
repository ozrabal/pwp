<?php

class Formelement_Date extends Formelement_Input {
    protected $type = 'text';

    public function __construct( $form, $name) {
	//add_action('init', array($this,'enqueue_scripts'));
        add_action( 'admin_init', array( $this, 'admin_enqueue_scripts' ) );
	parent::__construct( $form, $name );
	$this->set_class( 'datepicker' );
    }

    function admin_enqueue_scripts() {
	wp_enqueue_style( 'jquery-ui', PWP_EXTERNAL_LIBRARY . 'jquery-ui/jquery-ui.min.css' );
	wp_enqueue_script( 'jquery-ui-datepicker', plugins_url( '/' ) );
	wp_enqueue_script( 'field-date', plugins_url( '/field-date.js', __FILE__ ), array( 'jquery' ),PWP_VERSION );
    }
}