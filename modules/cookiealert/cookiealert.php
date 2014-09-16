<?php
/**
   * Cookie alert module
   *
   * Modul wyswietla oraz obsluguje informacje o ciasteczkach
   *
   * @package    PWP
   * @subpackage Cookiealert
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

add_action( 'pwp_init_cookiealert', array( 'Cookiealert', 'init' ) );