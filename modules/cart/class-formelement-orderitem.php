<?php
/**
   * Formelement_Orderitem class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Orderitem extends Formelement {
    protected $type = 'orderitem';

    /**
     * renderuje komentarz
     * @return string
     */
    public function render() {

        return $this->get_before() . $this->get_label() . '<p ' . $this->cssclass() . $this->id() . '>' . $this->items() . '</p>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }


    private function items(){
	dump($this->get_value());
    }
}