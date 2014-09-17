<?php
/**
   * Formelement_Repeatable class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Repeatable extends Formelement {
    
    protected
	$type = 'repeatable', $request, $body = null;
    
    /**
     * 
     * @param Form $form
     * @param string $name
     */
    public function __construct( $form, $name ) {
        parent::__construct( $form, $name );
    }

    /**
     * dolacza skrypty js
     */
    private function enqueue_media_repeatable() {
        
	wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ) );
        wp_enqueue_script( 'field-repeatable', plugins_url( '/field-repeatable.js', __FILE__ ), array( 'jquery', 'jquery-ui-sortable' ), PWP_VERSION, true );
    }
    
    /**
     * ustawia elementy pola
     * @param array $params
     * @return \Formelement_Repeatable
     */
    public function set_repeater( $params ) {

        $element_count = 0;
	if( ( $this->form instanceof Options )  && isset( $this->form->options[$this->get_name()] ) ) {
            $element_count = count( $this->form->options[$this->get_name()] );
        }
        if( $element_count == 0 ) {
	    $element_count = 1;
	}
        $iter = -1;
        for ( $i=1; $i <= $element_count; $i++ ) {
            $iter += 1;
            foreach ( $params as $element ) {
                $added = $this->add_element( $element['type'], $element['name'], $iter );
                if ( $added && isset( $element['params'] ) && is_array( $element['params'] ) ) {
                    $this->set_element_params( $element, $iter );
                } else {
                    dbug( '<div class="error"><p>Nieznany typ pola: <strong>' . $element['type'] . '</strong>, w polu powtarzalnym: '. $this->form->get_name() . '</p></div>' );
                }
            }
        }
        return $this;
    }
    
    /**
     * ustawia paremetry elementu pola
     * @param Formelement $element
     * @param integer $iter
     */
    private function set_element_params( $element, $iter ) {

        foreach ( $element['params'] as $param => $value ) {
            $this->elements[$iter][$element['name']]->{ 'set_'.$param }( $value );
        }
    }
    
    /**
     * inicjuje obiekt elementu pola
     * @param string $type
     * @param string $name
     * @param integer $iter
     * @return boolean
     */
    public function add_element( $type, $name, $iter ) {

        $type = 'Formelement_' . ucfirst( $type );
        if( class_exists( $type ) ) {
            $this->elements[$iter][$name] = new $type( $this, $name );
            return $this->elements[$iter][$name];
        }
        return false;
    }
    
    /**
     * renderuje pole powtarzalne
     * @return string
     */
    public function render() {
        
        $this->enqueue_media_repeatable();
	if( $this->form instanceof Options ) {
	    $this->set_name( $this->form->get_name() . '[' . $this->get_name() . ']' );
        }
        $this->body .= $this->get_before() . $this->get_label() . '<div ' . $this->cssclass() . '>';
        if( isset( $this->elements ) ) {
            if( is_admin() ) {
		$this->render_backend();
	    } else {
		$this->render_frontend();
	    }
            $this->body .= '<a href="#" class="repeatable-add button"><span class="pwp-icon dashicons dashicons-plus"></span>'. __( 'Add ', 'pwp' ) . $this->get_title() . '</a>';
	} else {
            $this->body = '<div class="pwp-error"><p class="description">' . __( 'No declaration field: ', 'pwp') . $this->get_title() . '</p></div>';
        }
        $this->body .= $this->get_comment( '<p class="description">%s</p>' ) . '</div>';
        return $this->body;
    }

    /**
     * renderuje element pola
     * @param array $value
     * @param integer $index
     */
    private function render_repeatable_element( $value, $index ) {

	foreach( $this->elements[0] as $element ) {
	    $name = $element->get_name();
            if( $this->form instanceof Options ) {
		$element->set_name( $this->get_name() . '[' . $index . '][' . $name . ']' );
            } else {
		$element->set_name( $this->get_name() . '[' . $index . '][' . $name . ']' );
            }
            $element->set_id( $name . '_' . $index );
            $element->label->set_for( $name . '_' . $index );
	    if( isset( $value[$index][$name] ) ) {
		$element->set_value( $value[$index][$name] );
            }
            $this->body .= $element->render();
            $element->set_name( $name );
        }
    }

    /**
     * renderuje zawartosc pola powtarzalnego w backendzie
     */
    private function render_backend() {

	$this->body .= '<table class="meta ds-input-table repeatable"><tbody class="ui-sortable-container">';
	$value = $this->get_value();
	for( $index = 0; $index < count( $value ); $index++ ) {
	    $this->body .= '<tr class="row sortable-item repeatable-item inline-edit-row quick-edit-row alternate"><td class="order"><div class="dashicons dashicons-menu"></div></td><td>';
	    $this->body .= $this->get_title( '<h4>%s</h4>' );
            $this->render_repeatable_element( $value, $index );
	    $this->body .= '</td><td class="remove"><a class="repeatable-remove dashicons dashicons-no" href="#"></a></td></tr>';
	    $this->body .= $this->get_after();
        }
        $this->body .= '</tbody></table>';
    }

    /**
     * renderuje zawartosc pola powtarzalnego we frontendzie
     */
    private function render_frontend(){

	$this->body .= '<div ' . $this->set_class( 'ui-sortable' )->cssclass() . '>';
        $value = $this->get_value();
        for ( $index = 0; $index < count( $value ); $index++ ) {
	    $this->body .='<div class="order sortable-item repeatable-item"><a class="order"><span class="glyphicon glyphicon-resize-vertical"></span>';
            $this->body .= $this->get_title( '<span class="repeatable-title">%s</span>' );
            $this->body .= '</a>';
	    $this->render_repeatable_element( $value, $index );
	    $this->body .= '<a class="repeatable-remove dashicons dashicons-no" href="#"><span class="glyphicon glyphicon-minus"></span></a></div>';
	    $this->body .= $this->get_after();
        }
	$this->body .= '</div>';
    }
}