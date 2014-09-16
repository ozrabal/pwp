<?php
/**
   * Option class
   * 
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Options extends Form {

    public static $instance;
    
    /**
     * singleton
     * @return Options
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Options();
        }
        return self::$instance;
    }

    /**
     * konstruktor
     * @param array $args
     * @return boolean
     */
    function __construct( $args = false ) {
        if( !$args ) {
            return false;
        }
        
        parent::__construct( $args );
        $this->options = get_option( $this->get_name(), true );

    }
    
    /**
     * dodaje element do formularza opcji
     * wypelnia go wartoscia jesli taka jest w bazie
     * @param string $type
     * @param string $name
     * @return Formelement
     */
    public function add_element( $type = 'text', $name = false ) {
               
        if( !filter_var( $name, FILTER_SANITIZE_STRING ) ) {
            $name = $this->get_name() . '_' . $type . '_' . count( $this->elements ); 
            dbug( 'Niepoprawna nazwa pola w formularzu opcji: ' . $this->get_name() . ' wygenerowano nazwe tymczasową ' . $name );
        }
	$this->elements[$name] = parent::add_element( $type, $name );
        
        $old_value = get_option( $this->get_name(), true );
	if( isset( $old_value[$name] ) ) {
            $this->elements[$name]->set_value( $old_value[$name] );
	}
        return $this->elements[$name];
    }
}