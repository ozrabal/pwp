<?php
/**
   * Formelement_Comment class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Comment extends Formelement {
    protected $type = 'comment';
    
    /**
     * renderuje komentarz
     * @return string
     */
    public function render() {
    
        return $this->get_before() . $this->get_label() . '<p ' . $this->cssclass() . $this->id() . '>' . $this->get_value() . '</p>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}