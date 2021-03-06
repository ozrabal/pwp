<?php


class Callback_Email implements Interface_Callback{

   private $params, $attachments = null;
   
    public function __construct() {
	//dump(__CLASS__);

    }




    public function do_callback( $params, $object ) {
        $this->set_params( $params );
        //dump(__METHOD__);
$this->object = $object;
dump($params);
dump($object);

	$this->set_attachments( $params );
         //return true;

        if ($this->send()){

            return $this;

	}
	return false;

    }

    public function set_attachments( $params ) {

	if(!empty($params['attachments'])){
	$this->attachments = $params['attachments'];
	} else {
	    if($this->get_object_param('file')){
		$this->attachments = $this->get_object_param('file');
	    }
	}
    }

    private function get_object_param( $param ){
	if(isset($this->object->$param)){
	    return $this->object->$param;
	}
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
        if( wp_mail( $this->get_recipient(), $admin_subject, $admin_body, $this->headers( array( 'from' => get_option( 'admin_email' ) ) ), $this->attachments ) ) {
            if($this->get_request( 'email' ) && $send_to_user != ''){
                wp_mail( $this->get_request( 'email' ), $user_subject, $user_body, $this->headers( array( 'from' => get_option( 'admin_email' ) ) ), $this->attachments );
            }

	    unlink($this->attachments);
            return true;
        } else {
            return false;
	}
    }


}