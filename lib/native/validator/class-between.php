<?php

class Validator_Between extends Validator{
    /**
     *
     * @param Mixed $value
     * @return Array
     */
    public function is_valid( $value ) {
        if( !is_numeric( $value ) || $value < $this->_rule[0] || $value > $this->_rule[1] ) {
            return array( 'error', 'Musi byc wieksze od ' . $this->_rule[0] . ' i mniejsze od ' . $this->_rule[1] );
        }
    }
}