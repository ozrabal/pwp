<?php
/**
   * Formelement_Date class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Date extends Formelement_Input {
    protected $type = 'text';
    
    /**
     * dolacza javascript
     */
    public function enqueue_scripts() {
        
	wp_enqueue_style( 'jquery-ui', PWP_EXTERNAL_LIBRARY . 'jquery-ui/jquery-ui.min.css' );
	wp_enqueue_script( 'jquery-ui-datepicker', plugins_url( '/' ) );
	wp_enqueue_script( 'field-date', plugins_url( '/field-date.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
    }
    
    /**
     * renderuje pole input date
     * @return string
     */
    public function render(){
        $this->set_class( 'datepicker' );
        $this->enqueue_scripts();
        return parent::render();
    }
}