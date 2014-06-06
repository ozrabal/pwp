<?php

add_action( 'pwp_init_test', array('Test', 'init'));

class Test{
    static $instance = null;
    
    public function __construct() {
        $this->settings();
    }
    
    public static function init() {
        if ( is_null( self::$instance ) )
            self::$instance = new Test();
        return self::$instance;
    }
    
    private function settings(){
        $admin = new Administrator();

    $page = array(
    //'parent_slug'   => 'pwp_setup',
    'page_title'    => __( 'Test settings page', 'pwp' ),
    'menu_title'    => __( 'Test settings', 'pwp' ),
    'capability'    => 'manage_options',
    'menu_slug'	    => 'test-options',
    'icon'	    => '',
    'position'	    => null
);

$admin->add_page( $page );
$options['test_options'] = new Options();
        $options['test_options']->set_name('test_options')->set_action('options.php')->set_title(__('Test options', 'pwp'));


        $options['test_options']
                ->add_element('text','tekst')
                ->set_label( __( 'User email template', 'pwp' ))
                ->set_class('klasa')
                
                ->set_validator(array('notempty'));
                
        $admin->add_options($options, 'test-options');

    }
    
}