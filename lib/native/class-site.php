<?php

class Site{

	var $args =array();

	function __construct($args){
		$this->args = $args;
	}

	public function remove_head_element(){
		foreach($this->args['remove_head_element'] as $element){
			remove_action('wp_head',$element);
		}
	}

}




