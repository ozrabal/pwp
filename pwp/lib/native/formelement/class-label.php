<?php

class Formelement_Label extends Formelement  {
    protected $type = 'label';

    public function set_for($for){
	$this->for = $for;
    }
    public function get_for(){
        if(isset($this->for)){
            return $this->for;
        }
    }
    public function labelfor(){
        if(isset($this->for)){
            return 'for="'.$this->get_for().'" ';
        }
    }
    public function render(){
        return $this->get_before().'<label '.$this->labelfor().'>'.$this->get_name().'</label>'.$this->get_after();
    }
}