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

    public function get_disabled(){
	if ( !empty($this->disabled) ) {
	    return 'disabled=' . $this->disabled;
	}
    }

    public function get_type() {
        return $this->type;
    }
    
    public function type() {
        return 'type="' . $this->get_type() . '" ';
    }

    public function set_data( Array $data ) {
	if( is_array( $data ) && !empty( $data ) ) {
            $this->data = $data;
        }
    }
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
    public function set_value( $value ) {
        $this->value = $value;
	return $this;
    }
    
    public function get_value() {
        return $this->value;
    }
    
    public function value() {
        if( isset( $this->value ) ) {
            return 'value="' . $this->get_value() . '" ';
        }
    }

    public function set_default( $value = null ) {
	if( !empty( $value ) ) {
	    $this->default = $value;
	}
    }
    public function get_default() {
	if( isset( $this->default ) ) {
	    return $this->default;
	}
    }

    public function set_title( $title ) {
        $this->title = $title;
        return $this;
    }
    
    public function get_title( $tag = '%s' ) {
        if( isset( $this->title ) ) {
            return sprintf( $tag, $this->title );
	}else if( isset( $this->label ) ) {
	    return sprintf( $tag, $this->get_label() );
	}else{
	    return sprintf( $tag, $this->get_name() );
	}
    }
    
    public function set_name( $name ) {
	$this->name = strval( $name );
	return $this;
    }
    
    public function get_name(){
	return $this->name;
    }
    
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
    
    public function set_id( $id ) {
        $this->id = $id;
	return $this;
    }
    
    public function get_id(){
        if ( isset( $this->id ) ) {
            return $this->id;
        }
    }
    
    public function id(){
        if ( isset( $this->id ) ) {
            return 'id="' . $this->get_id() . '" ';
        }
    }
    
    public function set_class( $class ) {
        $this->class[] = $class;
	return $this;
    }

    public function get_class() {
        if ( isset( $this->class ) ) {
            return $this->class;
        }
    }
    
    public function cssclass() {
        if ( isset( $this->class ) ) {
            return 'class="' . implode( ' ', $this->get_class() ) . '" ';
        }
    }

    public function set_before( $before ) {
	$this->before_element = $before;
	return $this;
    }

    public function get_before(){
        if ( isset( $this->before_element ) ) {
            return $this->before_element;
        }
    }
    
    public function set_after( $after ) {
	$this->after_element = $after;
	return $this;
    }

    public function get_after(){
        if ( isset( $this->after_element ) ) {
            return $this->after_element;
        }
    }

    public function set_comment( $comment ) {
        $this->comment = $comment;
        return $this;
    }
    public function get_comment( $tag = '%s' ) {
        if ( isset( $this->comment ) )
        return sprintf( $tag, $this->comment );
    }

    public function set_label( $label ) {
	$this->label = new Formelement_Label( $this->form, $label );
        if ( !isset( $this->id ) ) {
            $this->set_id( $this->get_name() );
            
        }
        $this->label->set_for( $this->get_id() );
	return $this;
    }

    public function get_label() {
	if( isset( $this->label ) && $this->label instanceof Formelement_Label ) {
	    return $this->label->render();
	}
    }
    
    public function set_callback( $callback ) {
	if ( is_callable( $callback[0] ) ) {
	    $this->callback = $callback;
	}
	return $this;
    }
    
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
                $validator_object = 'Validator_' . ucfirst( $validator_object );
                if ( class_exists( $validator_object ) ) {
		    $this->validator[] = new $validator_object( $validator_rule );
                }
            }
        }
	return $this;
    }
    
    public function get_validator( $name = null ) {
	if ( isset( $this->validator ) ) {
	    if ( !$name ) {
		return $this->validator;
	    } else {
		foreach( $this->validator as $index => $validator ) {
		    if ( $validator instanceof $name ) {
			return $validator;
		    }
		}
	    }
	}
	return false;
    }
    
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
    
    public function set_message( $message ){
        $this->message[$message[0]][] = $message[1];
    }
    
    //@todo oszablonowac zeby mozna zwracac raw array albo w spanach
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
    
    public function valid() {
        if ( $this->form->get_request() ) {
	    $this->set_value( $this->form->get_request( $this->get_name() ) );
            if ( isset( $this->validator ) ) {
                return $this->validate( $this->form->get_request( $this->get_name() ), $this->validator );
            }
        }
    }
    
    public function render(){
    //przed renderem w dzieciach dawac parent::render
	return $this;
    }

    public function do_callback( Array $callback ) {
	$this->$callback[1] = $this->callback[0]();
    }
}