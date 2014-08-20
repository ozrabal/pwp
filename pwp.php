<?php
/**
 * The WordPress Plugin PWP
 *
 * @package   PWP
 * @author    Piotr Łepkowski <ozrabal@gmail.com>
 * @license   GPL-2.0+
 * @link      http://webkowski.com
 * @copyright 2013 Piotr Łepkowski
 *
 * @wordpress-plugin
 * Plugin Name:       PWP
 * Plugin URI:        http://webkowski.com
 * Description:       Meta plugin for Wordpress Developers contains the most frequently used functions, widgets and libraries
 * Version:           0.0.1
 * Author:            Piotr Łepkowski
 * Author URI:        http://webkowski.com
 * Text Domain:       pwp
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/ozrabal/pwp
 */

 ob_start();
 //session_start();


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'PWP_ROOT_URL',  plugin_dir_url( __FILE__));
define( 'PWP_ROOT', plugin_dir_path( __FILE__ ) );
define( 'PWP_EXTERNAL_LIBRARY',  plugin_dir_url(plugin_basename(__FILE__)).'lib/external/' );
define( 'PWP_VERSION', '1.1' );

//unset($_SESSION);
spl_autoload_register( 'pwp_class_autoloader' );


//}

function pwp_class_autoloader( $classname ) {
    
    $path = explode('_', strtolower( $classname ));
    $end = end($path);
    array_pop($path);
//dump($path);
if(isset($path[0]) && $path[0] == 'interface'){
     $end = 'interface-'.$end;
}else{
    $end = 'class-'.$end;
}
   
    $path[] = $end;
    //dump($path);
    $classname = implode('/',$path);
    $classname = str_replace( '_', '/', strtolower( $classname ) );
    $classfile = sprintf( '%slib/native/%s.php', ABSPATH.'wp-content/plugins/pwp/', str_replace( '_', '-', strtolower( $classname ) ));

   //dump($classfile);

    if ( file_exists( $classfile ) ) include_once( $classfile );
}


//add_action( 'init', 'pwp_register_autoload' );
//function pwp_register_autoload(){
//    spl_autoload_register( 'pwp_class_autoloader' );
//}
//function pwp_class_autoloader( $classname ) {
//    //dump($classname);
//    $path = explode('_', strtolower( $classname ));
//    $end = end($path);
//    array_pop($path);
//    $end = 'class-'.$end;
//    $path[] = $end;
//    $classname = implode('/',$path);
//    $classname = str_replace( '_', '/', strtolower( $classname ) );
//    $classfile = sprintf( '%slib/native/%s.php',plugin_dir_path( __FILE__ ), str_replace( '_', '-', strtolower( $classname ) ));
//    //dump($classfile);
//    if ( file_exists( $classfile ) ) include_once( $classfile );
//}
function load_custom_wp_admin_style() {
        wp_register_style( 'pwp_admin_css', plugins_url( '/lib/pwp-style.css', __FILE__ ), array('theme_style'), PWP_VERSION );
        wp_enqueue_style( 'pwp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
include_once( PWP_ROOT . 'lib/external/meta-box-class/my-meta-box-class.php' );
include_once PWP_ROOT . 'lib/native/helpers.php';
include_once PWP_ROOT . 'lib/native/class-pwp.php';

include_once PWP_ROOT . 'config/site-config.php';

//require_once PWP_ROOT . 'lib/native/class-email-observer.php';
//$plugin_instance = Pwp::init( array( '' ) );

//dump($plugin_instance);


//$obj->load_class('session');

//$plugin_instance->load_module('cookie-alert');

//register_activation_hook( __FILE__, array( 'Pwp', 'plugin_activation' ) );
//register_deactivation_hook( __FILE__, array( 'Pwp', 'plugin_deactivation' ) );
add_action( 'plugins_loaded', 'pwp_plugin_initialize' );
function pwp_plugin_initialize() {
    load_plugin_textdomain( 'pwp', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    
}
add_action( 'init', array( 'Pwp', 'init' ) );


