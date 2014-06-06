<?php
/**
 * 
 */
interface i_Validator{
    public function __construct( $rule );
    public function is_valid( $value );
}
/**
 * 
 */
abstract class Validator implements i_Validator{
    protected $_rule;
    /**
     * 
     * @param String $rule
     */
    public function __construct( $rule = null ) {
        if( isset( $rule ) ) {
            $this->_rule = $rule;
        }
    }
}


