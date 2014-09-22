<?php

class Module {

    protected $actions;

    public function __construct(){
	$this->get_actions();
    }

    protected function get_actions() {
        $methods = preg_grep( '/_Action/', get_class_methods( $this ) );
        foreach ( $methods as $method ) {
            $this->actions[] = basename( $method, '_Action' );
        }
    }

    protected function is_action(){
        //$this->get_actions();
        if ( !in_array( $this->action, $this->actions, true ) && false === has_filter( 'login_form_' . $this->action ) ) {
            return false;
        }
        return true;
    }



    public function route(  ){

        if( get_query_var( $this->action_slug ) ) {
            $this->action = get_query_var($this->action_slug);
        } else if ( isset( $_GET[$this->action_slug] ) ) {
            $this->action = $_GET[$this->action_slug];
        } else {
            //$this->action = $this->_defaults[$this->action_slug];
	    $this->action = 'index';
        }
        if ( $this->is_action() ) {
            if ( method_exists( $this, $this->action . '_Action' ) )
                call_user_func( array( $this, $this->action . '_Action' ), array() );
        }else{
            $wp_error = new WP_Error();
            $wp_error->add('router', __( 'Action not allowed' ), 'message' );
            return $wp_error;
        }
    }


}