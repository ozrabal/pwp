<?php

class Formelement_Map extends Formelement_Input {
    protected $type = 'map';

    public function __construct( $form, $name) {
	//add_action('init', array($this,'enqueue_scripts'));
        add_action( 'admin_init', array( $this, 'admin_enqueue_scripts' ) );
	//$this->set_disabled('disabled');
	parent::__construct( $form, $name );
	
    }




    function admin_enqueue_scripts() {
	wp_enqueue_script( 'maps', 'http://maps.google.com/maps/api/js?sensor=false' );
	//wp_enqueue_script( 'jquery-ui-datepicker', plugins_url( '/' ) );
	wp_enqueue_script( 'field-map', plugins_url( '/field-map.js', __FILE__ ), array( 'jquery' ),PWP_VERSION );
    }


    public function render(){
        parent::render();
$this->set_disabled('disabled');
	if( isset( $this->callback ) ) {
	    $this->do_callback( $this->callback );
	}



        return  $this->get_before().$this->get_label().'<div id="map"></div><input '.$this->id().' type="text" '.$this->name().$this->value().$this->cssclass().'/>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();



	}

}
