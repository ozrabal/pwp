<?php
/**
   * Session module class
   *
   * @package    PWP
   * @subpackage Session
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */


add_action( 'pwp_init_session', 'pwp_init_session' );
/**
 * inicjalizacja sesji
 */
function pwp_init_session() {
    new Session();
}