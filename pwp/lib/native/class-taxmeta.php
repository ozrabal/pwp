<?php

class Taxmeta extends Form{
    
    private 
            $taxonomy;

    public
	    $body = '';
    
    public function __construct( array $params ) {
	$this->set_name( $params['name'] );
        $this->set_taxonomy( $params['name']) ;
        $this->set_params( $params );
	if( isset( $_GET['tag_ID'] ) ) {
	    $this->set_term_id( intval( $_GET['tag_ID'] ) );
	}
        //add_action( 'category_add_form_fields', array($this, 'pippin_taxonomy_add_new_meta_field'));
        add_action( $this->taxonomy . '_edit_form_fields', array( $this, 'render' ), 2, 2 );
	add_action( 'edited_' . $this->taxonomy, array( $this, 'save' ) );
	add_action( 'create_' . $this->taxonomy, array( $this, 'save' ) );

	if( isset( $_SESSION['tax-errors'] ) ) {
	    add_action( 'admin_notices', array( $this, 'error_notice' ) );
	    unset( $_SESSION['tax-errors'] );
	}
    }

    public function set_taxonomy( $taxonomy ) {
	$this->taxonomy = $taxonomy;
    }

    public function get_taxonomy() {
	return $this->taxonomy;
    }
    
    private function set_term_id( $term_id ) {
	$this->term_id = $term_id;
    }

    private function get_term_id() {
	return $this->term_id;
    }

    public function save( $term_id ) {
        $this->set_term_id( $term_id );
global $current_screen;

        if( isset( $_POST[$this->get_name()] ) && $current_screen->base == 'edit-tags') {
	    //pobieramy stare wartosci
	    //$term_meta = get_option( 'taxonomy_' . $this->get_term_id() );
	    foreach( $this->elements as $element ) {
		if( isset( $_POST[$this->get_name()][$element->get_name()] ) ) {
		    $save[$element->get_name()] = $_POST[$this->get_name()][$element->get_name()];
		} else {
		    $save[$element->get_name()] = '';
		}
		//validacja
		if( $element->get_validator() ) {
		    $invalid = $element->validate( $save[$element->get_name()], $element->get_validator() );
		    if( $invalid ) {
			$element->set_class( 'pwp_error' );
			$_SESSION[$element->get_name()]['class'] = 'pwp-error';
			$_SESSION[$element->get_name()]['message'] = array( 'error', $invalid );
			$_SESSION['tax-errors'] = true;
		    }
		}
	    }
	    if( isset( $save ) && !isset( $_SESSION['tax-errors'] ) ) {
		update_option( 'taxonomy_' . $this->get_term_id(), $save );
	    }
	}
    }
    
    function error_notice() {
	echo '<div class="error"><p>' . __( 'There were mistakes, not all changes have been saved', 'pwp' ) . '</p></div>';
	
    }

    public function render() {
	$this->options = get_option( 'taxonomy_' . $this->get_term_id() );
	foreach( $this->elements as $element ) {
	    //komunikat o bledzie jesli wywolanie po submicie
	    if( isset( $_SESSION[$element->get_name()] ) ) {
		$element->set_class( $_SESSION[$element->get_name()]['class'] );
		$element->set_message( $_SESSION[$element->get_name()]['message'] );
		unset( $_SESSION[$element->get_name()] );
	    }
            if( isset( $this->options[$element->get_name()] ) ) {
		$element->set_value( $this->options[$element->get_name()] );
	    }
	    //dopasowanie do wyswietlania w komorkach tabeli
	    $element->label->set_after( '</th><td>' );
	    //klasa dla validatora wymaganych pol w JS
	    if( $element->get_validator( 'Validator_Notempty' ) ) {
		$this->body .= '<tr class="form-required"><th>' . $element->render() . '</td></tr></td>';
	    } else {
		$this->body .= '<tr><th>' . $element->render() . '</td></tr></td>';
	    }
	}
	$this->print_form();
	
    }
}