<?php

class Validator_Notempty extends Validator{
    /**
     *
     * @param String $value
     * @return Array
     */
    public function is_valid( $value ) {
        if( empty( $value ) || $value == '' ) {
            return array( 'error', __('The field can not be blank','pwp') );
        }
    }
}