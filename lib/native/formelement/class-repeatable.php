<?php
class Formelement_Repeatable extends Formelement{
    protected
	$type = 'repeatable',
	$request,
        $body = null,
        $iter = -1
    ;
    
    function enqueue_media_repeatable() {
	wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ) );
        wp_enqueue_script( 'field-repeatable', plugins_url( '/field-repeatable.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
    }
    
    public function __construct( $form, $name ) {
        parent::__construct( $form, $name );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_repeatable' ) ); 
        add_action( 'enqueue_scripts', array( $this, 'enqueue_media_repeatable' ) );
    }

    public function set_options( $params ) {
        return $this->add_elements( $params );
    }

    public function add_elements( $params ) {
        $element_count = 0;
	if ( ( $this->form instanceof Options )  && isset( $this->form->options[$this->get_name()] ) ) {
            $element_count = count( $this->form->options[$this->get_name()] );
        }
        if ( $element_count == 0 ) {
	    $element_count = 1;
	}
        for ( $i=1; $i <= $element_count; $i++ ) {
            $this->iter += 1;
            foreach ( $params as $element ) {
                $added = $this->add_element( $element['type'], $element['name'] );
                if ( $added && isset( $element['params'] ) && is_array( $element['params'] ) ) {
                    foreach ( $element['params'] as $param => $value ) {
                    // np set_class()
                    $this->elements[$this->iter][$element['name']]->{'set_'.$param}($value);
                    }
                } else {
                    echo '<div class="error"><p>' . __( 'Unknown field type <strong>', 'pwp' ) . $element['type'] . __( '</strong> in repeatable set ') . $this->form->get_title() . '</p></div>';
                }
            }
        }
        return $this;
    }
    
    public function add_element( $type, $name ) {
        $type = 'Formelement_' . ucfirst( $type );
        if ( class_exists( $type ) ) {
            $this->elements[$this->iter][$name] = new $type( $this, $name );
            return $this->elements[$this->iter][$name];
        }
        return false;
    }
    
    public function render() {
        $this->p = $this->form->get_name() . '[' . $this->get_name() . ']';
        if ( $this->form instanceof Options ) {
            $this->set_name( $this->p );
        }
        $this->body .= $this->get_before() . $this->get_label();
        $this->body .= '<div ' . $this->cssclass() . '>';
        if ( isset( $this->elements ) ) {
            if ( is_admin() ) {
                $this->body .= '<table class="meta ds-input-table repeatable"><tbody class="ui-sortable-container">'; 
            } else {
                $this->body .= '<div ' . $this->set_class( 'ui-sortable' )->cssclass() . '>';
            }
            $a = $this->get_value();
            /*
            if ( count($a) < 1 ) {
                $a = 1;
            }
            */
            for ( $i = 0; $i<count( $a ); $i++ ) {
                if ( is_admin() ) {
                    $this->body .= '<tr class="row sortable-item repeatable-item inline-edit-row quick-edit-row alternate"><td class="order "><div class="dashicons dashicons-menu"></div></td><td>';
                    $this->body .= $this->get_title( '<h4>%s</h4>' );
                } else {
                    $this->body .='<div class="order sortable-item repeatable-item"><a class="order"><span class="glyphicon glyphicon-resize-vertical"></span>';
                    $this->body .= $this->get_title( '<span class="repeatable-title">%s</span>' );
                    $this->body .= '</a>';
                }
                foreach ( $this->elements[0] as $element ) {
                    $n = $element->get_name();
                    if ( $this->form instanceof Options ) {
                        $element->set_name( $this->get_name() . '[' . $i . '][' . $n . ']' );
                    } else {
                        $element->set_name( $this->get_name() . '[' . $i . '][' . $n . ']' );
                    }
                    $element->set_id( $n . '_' . $i );
                    $element->label->set_for( $n . '_' . $i );
                    //$element->set_name('['.$i.']['.$element->get_name().']');
                    if ( isset( $a[$i][$n] ) ) {
                        $element->set_value( $a[$i][$n] );
                    }
                    $this->body .= $element->render();
                    $element->set_name( $n );
                }
                if ( is_admin() ) {
                    $this->body .= '</td><td class="remove"><a class="repeatable-remove dashicons dashicons-no" href="#"></a></td></tr>';
                } else {
                    $this->body .= '<a class="repeatable-remove dashicons dashicons-no" href="#"><span class="glyphicon glyphicon-minus"></span></a></div>';
                }
                $this->body .= $this->get_after();
            }
            if( is_admin() ) {
                $this->body .= '</tbody></table>';
            } else {
                $this->body .= '</div>';
            }
            $this->body .= '<a href="#" class="repeatable-add button"><span class="pwp-icon dashicons dashicons-plus"></span>'. __( 'Add ', 'pwp' ) . $this->get_title() . '</a>';
        } else {
            $this->body = '<div class="pwp-error"><p class="description">' . __( 'No declaration field: ', 'pwp') . $this->get_title() . '</p></div>';
        }
        $this->body .= $this->get_comment( '<p class="description">%s</p>' );
        $this->body .= '</div>';
        return $this->body;
    }
    
    public function set_request( $request ) {
        if ( isset( $request[$this->name] ) ) {
            $this->request = $request[$this->name];
        }
    }

    public function get_request( $value = null ) {
        //if podany klucz zwraca value else zwraca array
        if ( !empty( $value ) ) {
            if ( isset( $this->request[$value] ) ) {
                return $this->request[$value];
            }
            return false;
        }
        return $this->request;
    }
}