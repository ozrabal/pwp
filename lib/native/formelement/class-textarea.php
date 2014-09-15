<?php
/**
   * Formelement_Textarea class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Textarea extends Formelement {
    protected $type = 'textarea';
    
    /**
     * renderuje textarea
     * @return string
     */
    public function render() {
        
        parent::render();
        return $this->get_before() . $this->get_label() . '<textarea ' . $this->id() . $this->cssclass() . $this->name() . '>' . $this->get_value() . '</textarea>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}