<?php

/**
 *
 */
class Validator_Email extends Validator{
    /**
     *
     * @param Mixed $value
     * @return Array
     */
    public function is_valid( $value ) {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
            return array('error',__('Invalid email address', 'pwp'));
        }
    }
}