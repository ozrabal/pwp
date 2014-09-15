<?php
/**
   * Formelement_Select class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Select extends Formelement{
    protected $type = 'select';

    public function set_options(Array $options){
        foreach($options as $name => $value ){
	    $opt = new Formelement_Option($this->form, $name, $this);
            $opt->set_value($value);
	 

            if($this->get_value() == $value){
                $opt->selected = true;
            }


            $this->options[] = $opt;
        }
    }

    public function get_options(){
        $r = null;
        $screen =false;
        if(  is_callable( 'get_current_screen' ))
	$screen = get_current_screen();
	
	foreach($this->options as $option){
	   
$option->selected = false;
            if(($screen && $screen->action != 'add') ){

	    if($this->get_value() == $option->get_value()){
                $option->selected = true;
            }
	    }else{
		if($this->get_default() == $option->get_value()){
                $option->selected = true;
		 //dump($option->get_value());
            }
	    }
            
	 
	    $r .= $option->render();
        }
        return $r;
    }

    public function render() {
        parent::render();

        return $this->get_before().$this->get_label().'<select '.$this->name().$this->id().$this->cssclass().'>'.$this->get_options().'</select>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();
    }
}