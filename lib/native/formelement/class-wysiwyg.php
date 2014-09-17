<?php
/**
   * Formelement_Wysiwyg class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Wysiwyg extends Formelement {
    protected $type = 'wysiwyg';
    private $options = array( 'tinymce' => false );

    /**
     * ustawia opcje do tinymce
     */
    public function set_options( $options ) {

        if( is_array( $options ) ) {
            $this->options = $options;
        }
    }

    /**
     * pobiera id pola textarea dla edytora
     * @return string
     */
    public function get_id(){
	
        return str_replace( array( '[', ']' ), '_', $this->name( false ) ) . 'e';
    }

    /**
     * pobiera opcje dla tinymce
     * @return array
     */
    public function get_options() {
        
        return $this->options;
    }

    /**
     * renderuje komunikat o bledzie
     * edytor nie funkcjonuje w repeatable
     * @todo powinno renderowac wtedy textarea
     * @return string
     */
    private function render_repeatable() {

	$this->set_comment( __( 'Nie można używać edytora tinymce w polu powtarzalnym', 'pwp' ) );
	$this->set_before( '<div class="pwp-error">' );
	$this->set_after( '</div>' );
	$this->set_class( 'pwp-error' );
	return $this->get_before() . $this->get_label() . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }

    /**
     * dolacza skrypty js
     */
    public function enqueue_scripts() {

	wp_enqueue_script( 'field-wysiwyg',  plugins_url( '/field-wysiwyg.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
    }
    
    /**
     * renderuje pole z edytorem
     * @return string
     */
    public function render() {
        
        $this->enqueue_scripts();
        if( $this->form instanceof Formelement_Repeatable ) {
	    return $this->render_repeatable();
	}
	parent::render();
	ob_start();
        $this->options['textarea_name'] = $this->name( false );
	wp_editor( $this->get_value(), $this->get_id(), $this->get_options() );
        $editor = ob_get_clean();
	return $this->get_before() . $this->get_label() . $editor . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}