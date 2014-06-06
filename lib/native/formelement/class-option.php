<?php

class Formelement_Option extends Formelement{
    protected $type = 'option';
    protected $select;

    public function __construct( \Form $form, $name, Formelement_Select $select ) {
        parent::__construct( $form, $name );
        $this->select = $select;
    }

    public function selected(){
        //if($this->get_value() == $this->form->get_request( $this->select->get_name())){
        if(isset($this->selected) && $this->selected == true)
        return ' selected="selected" ';
            
        //}
    }

    public function render() {
        //dump($this->form->get_request());
        return '<option '.$this->value().$this->selected().'>'.$this->get_name().'</option>';
    }
}