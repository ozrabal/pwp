<?php

class Callback_A implements Interface_Callback {

    public function do_callback($params) {

$params->a = __METHOD__;
dump($params);
	return $params;


    }

}
