<?php
/**
   * Formelement class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
abstract class Formelement{
    
    protected $message, $value;

    private $disabled;

    /**
     * __toString
     * @return string
     */
    public function __toString() {

	return $this->get_type();
    }

    /**
     * konstruktor
     * @param Form|Formelement_Repeatable $form
     * @param string $name
     */
    public function __construct( $form, $name ) {

	$this->form = $form;
        $this->set_name( $name );
    }

    /**
     * __call
     * @param string $name
     * @param array $arguments
     * @return \Formelement
     */
    public function __call( $name, $arguments = null ) {

        dbug( 'Klasa ' . __CLASS__ . ' nie posiada metody ' . $name . print_r( $arguments ) );
        return $this;
    }

    /**
     * ustawia wlasnosc disabled
     * @param string $value
     */
    public function set_disabled( $value = 'disabled' ) {

	if( isset( $value ) ) {
	    $this->disabled = 'disabled';
	}
    }

    /**
     * pobiera wartosc disabled jako atrybut html
     * @return string
     */
    public function get_disabled(){

	if ( !empty( $this->disabled ) ) {
	    return 'disabled=' . $this->disabled;
	}
    }

    /**
     * pobiera typ pola
     * @return string
     */
    public function get_type() {

        return $this->type;
    }

    /**
     * pobiera typ pola jako jako atrybut html
     * @return string
     */
    public function type() {

        return 'type="' . $this->get_type() . '" ';
    }

    /**
     * ustawia wartosc parametrow html data-
     * @param array $data
     */
    public function set_data( Array $data ) {

	if( is_array( $data ) && !empty( $data ) ) {
            $this->data = $data;
        }
    }

    /**
     * pobiera wartosci parametrow data- i zwraca jako ciag atrybutow html lub wartosc
     * @param string $data_name
     * @return string
     */
    public function get_data( $data_name = null ) {

	if( empty( $this->data ) ) {
	    return;
	}
	if( isset( $data_name ) ) {
	    return $this->data[$data_name];
	} else {
	$data = null;
	foreach( $this->data as $key => $value ) {
	    $data .= 'data-' . $key . '="' . $value . '" ';
	}
	    return $data;
	}
    }

    /**
     * ustawia wartosc pola
     * @param string|array $value
     * @return \Formelement
     */
    public function set_value( $value ) {

        $this->value = $value;
	return $this;
    }

    /**
     * zwraca wartosc pola
     * @return string|array
     */
    public function get_value() {

        return $this->value;
    }

    /**
     * zwraca wartosc pola jako atrybut html
     * @return string
     */
    public function value() {

        if( isset( $this->value ) ) {
            return 'value="' . $this->get_value() . '" ';
        }
    }

    /**
     * ustawia domyslna wartosc pola
     * @param string|array $value
     */
    public function set_default( $value = null ) {

	if( !empty( $value ) ) {
	    $this->default = $value;
	}
    }

    /**
     * zwraca domyslna wartosc pola
     * @return string|array
     */
    public function get_default() {

	if( isset( $this->default ) ) {
	    return $this->default;
	}
    }

    /**
     * ustawia tytul pola
     * @param string $title
     * @return \Formelement
     */
    public function set_title( $title ) {

        $this->title = $title;
        return $this;
    }

    /**
     * zwraca tytul pola i dekoruje tagami html
     * @param string $tag
     * @return string
     */
    public function get_title( $tag = '%s' ) {

        if( isset( $this->title ) ) {
            return sprintf( $tag, $this->title );
	}else if( isset( $this->label ) ) {
	    return sprintf( $tag, $this->get_label() );
	}else{
	    return sprintf( $tag, $this->get_name() );
	}
    }

    /**
     * ustawia nazwe pola
     * @param string $name
     * @return \Formelement
     */
    public function set_name( $name ) {

	$this->name = strval( $name );
	return $this;
    }

    /**
     * pobiera nazwe pola
     * @return string
     */
    public function get_name() {

	return $this->name;
    }

    /**
     * zwraca nazwe pola lub atrybut html "name"
     * @param boolean $tag
     * @return string
     */
    public function name( $tag = true ) {

	if ( !isset( $this->name ) ) {
	    return;
	}
	if ( $this->form instanceof Options || $this->form instanceof Taxmeta ) {
	    if ( $tag ) {
	        return 'name="' . $this->form->get_name() . '[' . $this->get_name() . ']" ';
	    } else {
	        return $this->form->get_name() . '[' . $this->get_name() . ']';
	    }
	}
	if ( $tag ) {
	    return 'name="' . $this->get_name() . '" ';
	} else {
	    return $this->get_name();
	}
    }

    /**
     * ustawia atrybut id pola
     * @param string $id
     * @return \Formelement
     */
    public function set_id( $id ) {

        $this->id = $id;
	return $this;
    }

    /**
     * 
     * @return string
     */
    public function get_id() {

        if ( isset( $this->id ) ) {
            return $this->id;
        }
    }

    /**
     *
     * @return string
     */
    public function id() {

        if ( isset( $this->id ) ) {
            return 'id="' . $this->get_id() . '" ';
        }
    }

    /**
     *
     * @param string $class
     * @return \Formelement
     */
    public function set_class( $class ) {

        $this->class[] = $class;
	return $this;
    }

    /**
     *
     * @return array
     */
    public function get_class() {

        if ( isset( $this->class ) ) {
            return $this->class;
        }
    }

    /**
     *
     * @return string
     */
    public function cssclass() {

        if ( isset( $this->class ) ) {
            return 'class="' . implode( ' ', $this->get_class() ) . '" ';
        }
    }

    /**
     *
     * @param string $before
     * @return \Formelement
     */
    public function set_before( $before ) {

	$this->before_element = $before;
	return $this;
    }

    /**
     *
     * @return string
     */
    public function get_before(){

        if ( isset( $this->before_element ) ) {
            return $this->before_element;
        }
    }

    /**
     *
     * @param string $after
     * @return \Formelement
     */
    public function set_after( $after ) {

	$this->after_element = $after;
	return $this;
    }

    /**
     *
     * @return string
     */
    public function get_after(){

        if ( isset( $this->after_element ) ) {
            return $this->after_element;
        }
    }

    /**
     *
     * @param string $comment
     * @return \Formelement
     */
    public function set_comment( $comment ) {

        $this->comment = $comment;
        return $this;
    }

    /**
     *
     * @param string $tag
     * @return string
     */
    public function get_comment( $tag = '%s' ) {

        if ( isset( $this->comment ) ) {
	    return sprintf( $tag, $this->comment );
	}
    }

    /**
     *
     * @param string $label
     * @return \Formelement
     */
    public function set_label( $label ) {

	$this->label = new Formelement_Label( $this->form, $label );
        if ( !isset( $this->id ) ) {
            $this->set_id( $this->get_name() );
            
        }
        $this->label->set_for( $this->get_id() );
	return $this;
    }

    /**
     *
     * @return string
     */
    public function get_label() {

	if( isset( $this->label ) && $this->label instanceof Formelement_Label ) {
	    return $this->label->render();
	}
    }

    /**
     *
     * @param string $callback
     * @return \Formelement
     */
    public function set_callback( $callback ) {
	if ( is_callable( $callback[0] ) ) {
	    $this->callback = $callback;
	}
	return $this;
    }

    /**
     *
     * @param array $validator
     * @return \Formelement
     */
    public function set_validator( $validator ) {

        if ( isset( $validator ) && is_array( $validator ) ) {
            foreach( $validator as $key => $val ) {
                if ( !is_numeric( $key ) ) {
                    $validator_object = $key;
                    $validator_rule = $val;
                } else {
                    $validator_object = $val;
                    $validator_rule = null;
                }
                $this->init_validator( $validator_object, $validator_rule );
            }
        }
	return $this;
    }

    /**
     *
     * @param string $validator_object
     * @param string $validator_rule
     */
    protected function init_validator( $validator_object, $validator_rule ) {
	
	$validator_object = 'Validator_' . ucfirst( $validator_object );
	if ( class_exists( $validator_object ) ) {
	    $this->validator[] = new $validator_object( $validator_rule );
        }
    }

    /**
     *
     * @param string $name
     * @return boolean
     */
    public function get_validator( $name = null ) {

	if ( !isset( $this->validator ) ) {
	    return false;
	}
	if ( !$name ) {
	    return $this->validator;
	} 
	foreach( $this->validator as  $validator ) {
	    if ( $validator instanceof $name ) {
		return $validator;
	    }
	}
    }

    /**
     *
     * @param string $value
     * @param array $validators
     * @param string $decorate
     * @return string
     */
    public function validate( $value, $validators, $decorate = null ) {

        if ( is_array( $validators ) ) {
            foreach( $validators as $validator ) {
                $invalid = $validator->is_valid( $value );
                if ( $invalid ) {
		    /* @var $invalid type Array*/
		    $this->message[$invalid[0]][] = $invalid[1];
		}
            }
        }
        if ( count( $this->message ) > 0 ) {
            $this->set_class( 'alert-danger element-error' );
            $this->set_value( '' );
            $this->form->set_errors( 1 );
            return $this->get_message( $decorate );
        }
    }

    /**
     *
     * @param array $message
     */
    public function set_message( $message ){

        $this->message[$message[0]][] = $message[1];
    }
    
    //@todo oszablonowac zeby mozna zwracac raw array albo w spanach
    /**
     *
     * @param boolean $no_decorate
     * @return string|array
     */
    public function get_message( $no_decorate = null ) {

        if ( $this->message ) {
            //@todo obsluga bledow
            if ( !$no_decorate ) {
                return '<span class="' . key( $this->message ) . '">' . implode( '<br>', $this->message['error']) . '</span>';
            } else {
                return $this->message;
            }
        }
    }

    /**
     *
     * @return boolean
     */
    public function valid() {

        if ( $this->form->get_request() ) {
	    $this->set_value( $this->form->get_request( $this->get_name() ) );
            if ( isset( $this->validator ) ) {
                return $this->validate( $this->form->get_request( $this->get_name() ), $this->validator );
            }
        }
    }

    /**
     *
     * @return \Formelement
     */
    public function render(){

	//przed renderem w dzieciach dawac parent::render
	return $this;
    }

    /**
     *
     * @param array $callback
     */
    public function do_callback( Array $callback ) {

	$this->$callback[1] = $this->callback[0]();
    }
}