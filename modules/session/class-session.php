<?php
/**
   * Session module class
   *
   * @package    PWP
   * @subpackage Session
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

class Session {

    /**
     * konstruktor
     */
    public function __construct() {

        $this->name = __CLASS__;
        $this->register_session();
    }

    /**
     * start sesji
     */
    function register_session() {

	if( !session_id() ) {
	    session_start();
	}
    }
}