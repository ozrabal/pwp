<?php
class Cart_Widget extends WP_Widget {


    function __construct() {

	$widget_ops = array(
            'classname'     => 'widget_cart',
            'description'   => __( 'Cart view widget', 'pwp' ) );
	$control_ops = array(
            'width' => 300,
            'height' => 350
        );

	parent::__construct('cart', __( 'Cart panel', 'pwp' ), $widget_ops, $control_ops );

    }

    public function widget( $args, $instance ) {


	$a = Cart::init();


     	        echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		$a->cart_panel();

		echo __( 'Hello, World!', 'text_domain' );
		echo $args['after_widget'];
	}




}