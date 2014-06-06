<?php

class Formelement_Checkbox extends Formelement{
    protected $type = 'checkbox';

    public function checked(){
        if($this->get_value() != null){
            return 'checked="checked" ';
        }
    }

    public function render() {
        parent::render();
        return $this->get_before().$this->get_label().'<input '.$this->name().$this->type().$this->checked().$this->id().$this->cssclass().'/>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();
    }
}