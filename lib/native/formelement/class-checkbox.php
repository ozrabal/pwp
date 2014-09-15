<?php
/**
   * Formelement_Checkbox class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Checkbox extends Formelement {
    protected $type = 'checkbox';
    
    /**
     * ustawia checked
     * @return string
     */
    public function checked() {
        
        if( $this->get_value() != null ) {
            return 'checked="checked" ';
        }
    }
    
    /**
     * renderuje checkbox
     * @return string
     */
    public function render() {
        
        parent::render();
        return $this->get_before() . $this->get_label() . '<input ' . $this->name() . $this->type() . $this->checked() . $this->id() . $this->cssclass() . '/>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}