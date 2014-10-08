<?php

class Callback_Mail implements Interface_Callback {

    private
	    $user_email,
	    $user_subject,
	    $user_body,
	    $user_attachment,
	    $admin_email,
	    $admin_subject = null,
	    $admin_body = null,
	    $admin_attachment = null,
	    $header;

    private function set_params( stdClass $params ) {

	foreach ($params as $param => $value ) {
	    if( method_exists( $this, 'set_' . $param ) ) {
		$this->{ 'set_' . $param }( $value );
	    }
	}
	$this->set_header();
    }

    private function set_admin_email( $admin_email ) {

	$this->admin_email = $admin_email;
    }

    private function get_admin_email() {

	if( !isset( $this->admin_email ) ) {
	    return get_option( 'admin_email' );
	}
	return $this->admin_email;
    }

    private function set_admin_subject( $admin_subject ) {

	$this->admin_subject = $admin_subject;
    }

    private function get_admin_subject() {

	return $this->admin_subject;
    }

    private function set_admin_body( $admin_body ) {

	$this->admin_body = $admin_body;
    }

    private function get_admin_body(){

	return $this->admin_body;
    }

    private function set_admin_attachment( $admin_attachment ) {

	if( is_array( $admin_attachment ) ) {
	    foreach( $admin_attachment as $file ) {
		if( file_exists( $file ) ) {
		   $this->admin_attachment[] = $file;
		}
	    }
	} else {
	    if( file_exists( $admin_attachment ) ) {
		$this->admin_attachment = $admin_attachment;
	    }
	}
    }

    private function get_admin_attachment() {

	return $this->admin_attachment;
    }

    private function set_user_email( $user_email ) {

	$this->user_email = $user_email;
    }

    private function get_user_email() {

	if( !isset( $this->user_email ) ) {
	    return false;
	}
	return $this->user_email;
    }

    private function set_user_subject( $user_subject ) {

	$this->user_subject = $user_subject;
    }

    private function get_user_subject() {

	if( !isset( $this->user_subject ) ) {
	    return $this->admin_subject;
	}
	return $this->user_subject;
    }

    private function set_user_body( $user_body ) {

	$this->user_body = $user_body;
    }

    private function get_user_body() {

	if( !isset( $this->user_body ) ) {
	    return $this->admin_body;
	}
	return $this->user_body;
    }

    private function set_user_attachment( $user_attachment ) {

	if( is_array( $user_attachment ) ) {
	    foreach( $user_attachment as $file ) {
		if( file_exists( $file ) ) {
		   $this->user_attachment[] = $file;
		}
	    }
	} else {
	    if( file_exists( $user_attachment ) ) {
		$this->user_attachment = $user_attachment;
	    }
	}
    }

    private function get_user_attachment() {

	if( !isset( $this->user_attachment ) ) {
	    return $this->admin_attachment;
	}
	return $this->user_attachment;
    }

    private function set_header() {

	$this->header[] = 'From: ';
	$this->header[] = 'Return-Path: ';
	$this->header[] = 'MIME-Version: 1.0';
	$this->header[] = 'Content-Type: text/html; charset=UTF-8';
	
    }

    private function get_header() {

	return $this->header;
    }

    public function __call( $name, $arguments = null ) {

        dbug( 'Klasa ' . __CLASS__ . ' nie posiada metody ' . $name . print_r( $arguments ) );
        return $this;
    }
    
    public function do_callback( $params ) {

	$this->set_params( $params );
	if( $this->send() ) {
	    return $params;
	}
	return false;
    }

    private function send() {
	//do admina
	if( wp_mail( $this->get_admin_email(), $this->get_admin_subject(), $this->get_admin_body(), $this->get_header(), $this->get_admin_attachment() ) ) {
	    if($this->get_user_email()){
                wp_mail( $this->get_user_email(), $this->get_user_subject(), $this->get_user_body(), $this->get_header(), $this->get_user_attachment() );
            }
            return true;
        } else {
            return false;
	}
    }
}

