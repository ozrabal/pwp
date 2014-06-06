<?php
interface Field{
    function render();
}

abstract class Formelement_Input extends Formelement implements Field{

    public function render(){
        parent::render();

	if( isset( $this->callback ) ) {
	    $this->do_callback( $this->callback );
	}

        return  $this->get_before().$this->get_label().'<input '.$this->id().$this->type().$this->name().$this->value().$this->cssclass().'/>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();
    }
}

