<?php

/**
   * Taxmeta class
   * 
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Taxmeta extends Form {
    
    private 
            $taxonomy = 'category',
	    $term_id = 0,
	    $defaults = array(
		'name'	=> 'category_meta',
		'tax'	=> 'category',
		'title'	=> 'Category meta',
	    );

    public
	    $body = '';
    
    /**
     * konstruktor
     * @param array $params
     */
    public function __construct( array $params ) {
        
        parent::__construct( $params );
        $params = array_merge( $this->defaults, $params );
        $this->set_title( $params['title'] );
        $this->set_taxonomy( $params['tax'] ) ;
        if ( filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE ) ) {
	    $this->set_term_id( filter_input( INPUT_GET, 'tag_ID', FILTER_SANITIZE_NUMBER_INT ) );
	}
        add_action( $this->taxonomy . '_add_form_fields', array($this, 'render'), 2, 2 );
        add_action( $this->taxonomy . '_edit_form_fields', array( $this, 'render' ), 2, 2 );
	add_action( 'edited_' . $this->taxonomy, array( $this, 'save' ) );
	add_action( 'created_' . $this->taxonomy, array( $this, 'save' ) );
        add_action( 'delete_term', array( $this, 'delete' ), 10, 2 );

	if( isset( $_SESSION['tax-errors'] ) ) {
	    add_action( 'admin_notices', array( $this, 'error_notice' ) );
	    unset( $_SESSION['tax-errors'] );
	}

	$this->body = '<h3>' . $this->get_title() . '</h3>';
	if ( $this->is_edit() ) {
            $this->body = '<tr class="form-field"><td><h3>' . $this->get_title() . '</h3></td></tr>';
        }
    }

    /**
     * ustawia taxonomie dla ktorej dodajemy pola meta
     * @param string $taxonomy
     */
    public function set_taxonomy( $taxonomy = 'category' ) {
	$this->taxonomy = $taxonomy;
    }
    
    /**
     * zwraca nazwe taxonomii dla ktorej sa ustawione metapola
     * @return string
     */
    public function get_taxonomy() {
	return $this->taxonomy;
    }
    
    /**
     * ustawia term id
     * @param int $term_id
     */
    private function set_term_id( $term_id ) {
	$this->term_id = $term_id;
    }
    
    /**
     * pobiera term id
     * @return int
     */
    private function get_term_id() {
	return $this->term_id;
    }
    
    /**
     * uswa wartosc metapol dla danego term
     * @param int $term_id
     */
    public function delete( $term_id ) {
	delete_option( 'taxonomy_' . intval( $term_id ) );
    }
    
    /**
     * zapisuje wartosc metapola do opcji taxonomy_{id}
     * @param int $term_id
     */
    public function save( $term_id ) {

	unset($_SESSION['tax-errors']);
	$this->set_term_id( $term_id );
        if ( filter_input( INPUT_POST, $this->get_name(), FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ) ) {
	    $term_meta = filter_input( INPUT_POST, $this->get_name(), FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	    foreach( $this->elements as $element ) {
		if ( isset( $term_meta[$element->get_name()] ) ) {
		    $save[$element->get_name()] = $term_meta[$element->get_name()];
		} else {
		    $save[$element->get_name()] = '';
		}
		$save = $this->validate( $element, $save );
	    }
	    if ( isset( $save ) && !isset( $_SESSION['tax-errors'] ) ) {
		update_option( 'taxonomy_' . $this->get_term_id(), $save );
	    }
	}
    }

    /**
     * przeprowadza walidacje 
     * @param Formelement $element
     * @param array $save
     * @return array
     */
    private function validate( $element, $save ) {

	if( $element->get_validator() ) {
	    $invalid = $element->validate( $save[$element->get_name()], $element->get_validator() );
	    if( $invalid ) {
		$element->set_class( 'pwp_error' );
		$_SESSION[$element->get_name()]['class'] = 'pwp-error';
		$_SESSION[$element->get_name()]['message'] = array( 'error', $invalid );
		$_SESSION['tax-errors'] = true;
	    }
	}
	return $save;
    }

    /**
     * ustawia klase dla javascriptu validacji not empty
     * @param Formelement $element
     * @return string
     */
    private function js_notempty_css( $element ) {
	
	if ( $element->get_validator( 'Validator_Notempty' ) ) {
	    return 'form-required';
	}
    }

    /**
     * renderuje formularz z polami
     */
    public function render() {

	$this->options = get_option( 'taxonomy_' . $this->get_term_id() );
	foreach ( $this->elements as $element ) {
	    if ( isset( $_SESSION[$element->get_name()] ) ) {
		$element->set_class( $_SESSION[$element->get_name()]['class'] );
		$element->set_message( $_SESSION[$element->get_name()]['message'] );
		unset( $_SESSION[$element->get_name()] );
	    }
            if ( isset( $this->options[$element->get_name()] ) ) {
		$element->set_value( $this->options[$element->get_name()] );
	    }
	    if ( $this->is_edit() ) {
		$element->label->set_after( '</th><td>' );
                $this->body .= '<tr class="' . $this->js_notempty_css( $element ) . ' form-field"><th scope="row">' . $element->render() . '</td></tr></td>';
            } else {
		$this->body .= '<div class="' . $this->js_notempty_css($element) . ' form-field">' . $element->render() . '</div>';
            }
	}
	$this->print_form();
    }
}