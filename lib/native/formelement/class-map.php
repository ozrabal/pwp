<?php
/**
   * Formelement_Map class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Map extends Formelement_Input {
    protected $type = 'map';
    
    /**
     * konstruktor
     * @param Form $form
     * @param string $name
     */
    public function __construct( $form, $name ) {
	
        add_action( 'admin_init', array( $this, 'admin_enqueue_scripts' ) );
	parent::__construct( $form, $name );
    }
    
    /**
     * dolaczenie skryptow
     */
    function admin_enqueue_scripts() {

	wp_enqueue_script( 'maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false' );
	wp_enqueue_script( 'field-map', plugins_url( '/field-map.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
	wp_localize_script( 'field-map', 'geocode_notfound', __( 'No results were found for the search criteria', 'pwp' ) );
    }
    
    /**
     * renderuje pole
     * @return string
     */
    public function render(){

	parent::render();
	$type = 'hidden';
	if( WP_DEBUG ) {
	    $type = 'text';
	}
        return  $this->get_before() . $this->get_label() . '<div class="box"><input onkeydown="if (event.keyCode == 13){ codeAddress(); return false;}" id="geocode" class="controls" type="text" placeholder="' . __( 'Type location', 'pwp' ) . '"><input type="button" class="button button-small" value="' . __( 'Show on map', 'pwp' ) . '" onclick="codeAddress();return false;"></div><div id="map"></div><input ' . $this->id() . ' type="' . $type . '" ' . $this->name() . $this->value() . $this->cssclass() . '/>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
	}
}