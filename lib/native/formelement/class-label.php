<?php
/**
   * Formelement_Label class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Label extends Formelement  {
    protected $type = 'label';

    /**
     * ustawia parametr for
     * @param string $for
     */
    public function set_for( $for ) {
        
	$this->for = $for;
    }
    
    /**
     * pobiera parametr for
     * @return string
     */
    public function get_for() {
        if( isset( $this->for ) ) {
            return $this->for;
        }
    }
    
    /**
     * zwraca atrybut html for
     * @return string
     */
    public function labelfor() {
        if( isset( $this->for ) ) {
            return 'for="' . $this->get_for() . '" ';
        }
    }
    
    /**
     * renderuje label
     * @return string
     */
    public function render() {
        
        return $this->get_before() . '<label ' . $this->labelfor() . '>' . $this->get_name() . '</label>' . $this->get_after();
    }
}