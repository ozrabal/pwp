<?php
/**
   * Formelement_Input class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
abstract class Formelement_Input extends Formelement implements Interface_Field {

    /**
     * renderuje pole input
     * @return string
     */
    public function render() {
    
        parent::render();
        if( isset( $this->callback ) ) {
	    $this->do_callback( $this->callback );
	}
        return  $this->get_before() . $this->get_label() . '<input ' . $this->get_disabled() . $this->id() . $this->type() . $this->name() . $this->value() . $this->cssclass() . '/>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}