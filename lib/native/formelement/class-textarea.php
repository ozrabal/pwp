<?php

class Formelement_Textarea extends Formelement {
    protected $type = 'textarea';
//    dopuszczalne=(class, style)
//	    ustawione=array();
//
//    setter(typ, wartosc){
//	if typ w dopuszczlnych
//
//    }
//    //private $params = array('id' )

    public function render() {




        parent::render();
        return $this->get_before().$this->get_label().'<textarea '.$this->id().$this->cssclass().$this->name().'>'.$this->get_value().'</textarea>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();
    }
}