<?php

class Observer_Email /*implements Observer*/{
    private $params;

    public function update( $params ) {
        $this->set_params( $params );
        //dump(__METHOD__);
      
        
         //return true;
        
        if ($this->send()){
            
            return true;
            
	}
	return false;
	
    }

    public function set_params($params){
	$this->params = $params;
    }

    public function set_param($key, $value){
	$his->params[$key] = $value;
    }

    public function get_param($key){
        if(isset($this->params[$key])){
	    return $this->params[$key];
	}
    }

    public function get_request($key){
	if(isset($this->params['request'][$key])){
	    return $this->params['request'][$key];
	}
    }

    function headers($args = array()){
	$headers[] = 'From: '.$args['from'];
	$headers[] = 'Return-Path: '.$args['from'];
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	return $headers;
    }


    private function get_recipient(){
	if($this->get_param( 'recipient' )){
	    return $this->get_param('recipient');
	}else{
	    return get_option( 'admin_email' );
	}
    }

    public function send(){
 
         
        $user_body = $this->get_param( 'user_email_template' );
        $user_subject = $this->get_param( 'user_email_subject' );
        $admin_body = $this->get_param( 'admin_email_template' );
        $admin_subject = $this->get_param( 'admin_email_subject' );
        $send_to_user = $this->get_param( 'send_to_user' );


	//$recipient = $this->get_recipient();


        if( $user_body != '' ){
	    //dump($this->get_param( 'request' ));
            foreach( $this->get_param( 'request' ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    $user_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $user_body );
                    //$admin_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $admin_body );
                } else {
                    //@todo polapowtarzalne do szablonow email
                }
            }
        } else {
            foreach( $this->get_param( 'request' ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    $user_body .= $k . ' : ' . $v . '<br>';
                }
            }
        }
        
        if( $admin_body != '' ){
            foreach( $this->get_param( 'request' ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    //$user_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $user_body );
                    $admin_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $admin_body );
                } else {
                    //@todo polapowtarzalne do szablonow email
                }
            }
        } else {
            foreach( $this->get_param( 'request' ) as $k => $v ) {
                if( !is_array( $this->get_request( $k ) ) ) {
                    $admin_body .= $k . ' : ' . $v . '<br>';
                }
            }
        }
        //dump($recipient);
        if( wp_mail( $this->get_recipient(), $admin_subject, $admin_body, $this->headers( array( 'from' => get_option( 'admin_email' ) ) ) ) ) {
            if($this->get_request( 'email' ) && $send_to_user != ''){
                wp_mail( $this->get_request( 'email' ), $user_subject, $user_body, $this->headers( array( 'from' => get_option( 'admin_email' ) ) ) );
            }
            return true;
        } else {
            return false;
	}
    }
}