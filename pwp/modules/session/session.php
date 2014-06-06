<?php
add_action( 'pwp_init_session', 'pwp_init_session' );

function pwp_init_session(){
    $session = new Session();
}

class Session{
    public function __construct() {
        $this->name = __CLASS__;
        $this->register_session();
    }

    function register_session() {
	if( !session_id() ) {
	    session_start();
	}
    }
    
    static function get_instance(){
    }
}