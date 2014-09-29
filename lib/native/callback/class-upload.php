<?php

class Callback_Upload implements Interface_Callback {
    private $params;

    public function update( $params ) {
        //$this->set_params($params);
        //dump($params['object']);
        //dump($params);
        
        //dump($_FILES);
        // dump($_POST);
         //die();
        
	return true;
    }

    public function do_callback( $params ) {
        dump(__METHOD__);
        dump($params);
        return true;
    }

}