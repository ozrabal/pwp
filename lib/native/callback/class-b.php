<?php

class Callback_B implements Interface_Callback {


    
    public function do_callback($params) {

$params->b = __METHOD__;
dump($params);
	return $params;

    }

}