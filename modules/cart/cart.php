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
//include_once 'Formelement/class-orderitem.php';
add_action( 'pwp_init_cart', array( 'Cart', 'init' ) );