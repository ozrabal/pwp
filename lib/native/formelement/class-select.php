<?php
/**
   * Formelement_Select class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Select extends Formelement {
    protected $type = 'select';

    /**
     *
     * @param array $optionsinicjalizuje pola opcji wyboru
     * @param array $options
     */
    public function set_options( Array $options ) {

        foreach( $options as $name => $value ) {
	    $option = new Formelement_Option( $this->form, $name, $this );
            $option->set_value($value);
	    if( $this->get_value() == $value ) {
                $option->selected = true;
            }
	    $this->options[] = $option;
        }
    }

    /**
     * renderuje pola opcji
     * @return string 
     */
    public function get_options() {
	
        $body = null;
        $screen = false;
        if( is_callable( 'get_current_screen' ) ) {
	    $screen = get_current_screen();
	}
	foreach( $this->options as $option ) {
	    $option->selected = false;
            if( ( $screen && $screen->action != 'add' ) ) {
		if( $this->get_value() == $option->get_value() ) {
		    $option->selected = true;
		}
	    } else {
		if( $this->get_default() == $option->get_value() ) {
		    $option->selected = true;
		}
	    }
            $body .= $option->render();
        }
        return $body;
    }

    /**
     *renderuje pole select
     * @return string
     */
    public function render() {

        parent::render();
        return $this->get_before() . $this->get_label() . '<select ' . $this->name() . $this->id() . $this->cssclass() . '>' . $this->get_options() . '</select>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}