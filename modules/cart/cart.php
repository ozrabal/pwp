<?php
/**
   * Cart module
   *
   * Modul  obsluguje koszyk
   *
   * @package    PWP
   * @subpackage Cart
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

add_action( 'pwp_init_cart', array( 'Cart', 'init' ) );