<?php
/**
   * Callback Interface
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
interface Interface_Callback {
    /**
     * funkcja renderujaca pole
     */
    function do_callback( $params, $object );
}