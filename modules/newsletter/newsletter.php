<?php
/**
 * Widget Newsletter subscription form
 * Display form for subscribe newsletter with link to rules
 * 
 * @package PWP
 * 
 */

add_action( 'pwp_init_newsletter', 'pwp_init_newsletter' );
//add_action( 'widgets_init', 'pwp_init_newsletter' );





function pwp_init_newsletter(){
    //$n = new Newsletter_Widget();
//$n->widget(array(), array());
 
     //register_widget( 'Newsletter_Widget' );


    //dump($n);
    //die();
    //add_action( 'init', create_function( '', 'return register_widget("Newsletter_Widget");' ) );
}

class Newsletterwidget extends WP_Widget {
 
    function __construct() {
        
       
        
        
      
        
	$widget_ops = array(
            'classname'     => 'widget_newsletter',
            'description'   => __('Newsletter subscription form with link to article contains rules', 'pwp' ) );
	$control_ops = array(
            'width' => 300,
            'height' => 350
        );
	parent::__construct('newsletter', __( 'Newsletter subscription form', 'pwp' ), $widget_ops, $control_ops );
        $this->enqueue_media();
        add_action('wp_ajax_subscribe', array($this,'subscribe'));
        add_action('wp_ajax_nopriv_subscribe', array($this,'subscribe'));
        add_action('template_redirect', array($this,'enqueue_media'));
    }

    function subscribe(){
        wp_send_json($this->send_mail());
     }
    

     function send_mail(){
	$alert = '';
	$error = 0;
	if(isset($_POST['data'])){
	    parse_str(urldecode($_POST['data']),$data);
	    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
		$alert .= __( 'Email address is not valid<br>', 'pwp' );
		$error = 1;
	    }
	    if(!isset($data['rules'])){
		$alert .= __( 'You must accept rules', 'pwp' );
		$error = 1;
	    }
	    if($error == 0){
		$headers = 'From: '. get_bloginfo( 'name').' <>';
		$admin_email = get_option('admin_email');
		$admin_content = __( 'New newsletter subscribent: ','pwp' ).$data['email'];
		$user_content = __( 'Witaj, twój adres został pomyślnie dopisany do listy subskrybentów newslettera. ', 'pwp' ).$data['email'];
		/*
		$options = get_option('newsletter');
		if($options['welcome_message']){
		    $user_content = $options['welcome_message'];
		}
		*/
		if(wp_mail($admin_email, __( 'Newsletter subscription', 'pwp' ), $admin_content, $headers)){
		    wp_mail($data['email'], __('Newsletter '.get_bloginfo( 'name' ), 'pwp'), $user_content, $headers);
		    $alert .= __( 'Succesfully subscribed', 'pwp' );
		    $class='alert-success';
		}else{
		    $alert = __( 'The mail could not be sent.', 'pwp' );
		    $class= 'alert-danger';
		}
	    }else{
		$class='alert-danger';
	    }
	    return array('alert'=>$alert, 'class' => $class);
	}
    }
        
    function widget( $args, $instance ) {
        
        
        
        extract($args);
	$newsletter_rules_id = apply_filters( 'newsletter_rules', empty( $instance['newsletter_rules'] ) ? '' : $instance['newsletter_rules'], $instance );
        $newsletter_rules_link = get_permalink( $newsletter_rules_id );
	$this->display_title = apply_filters( 'display_title', $instance['display_title'] );
	$this->title = apply_filters( 'title', $instance['title'] );
        if ( intval( $newsletter_rules_id ) == 0 ) {
            $newsletter_rules_link = false;
        }
	echo $before_widget;
	if ( $this->title && $this->display_title )
        echo $before_title . $this->title . $after_title;
        include( locate_template( 'widget-newsletter-subscribe.php' ) );
        echo $after_widget;
    }

    function enqueue_media() {
        wp_enqueue_script( 'ajax-request', plugins_url( 'pwp-settings/pwp/widgets/newsletter_widget.js' ), array( 'jquery' ) );  
        wp_localize_script( 'ajax-request', 'pwpax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );  
    }

    function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	$instance['newsletter_rules'] = intval( $new_instance['newsletter_rules'] );
	$instance['title'] = strip_tags( $new_instance['title'] );
	$instance['display_title'] = strip_tags( $new_instance['display_title'] );
        return $instance;
    }

    function form( $instance ) {
        
     
	$instance = wp_parse_args( (array) $instance, array( 'newsletter_rules' => '' ) );
	$newsletter_rules = intval( $instance['newsletter_rules'] );
	
        $this->get_content_select( $instance );
	

    }

    private function get_content_select( $instance ){
        $instance = wp_parse_args( (array) $instance, array( 'newsletter_rules' => '' ) );
        //$current_language = pll_current_language('slug');
        $newsletter_rules = intval( $instance['newsletter_rules'] );
	$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : null;
	$display_title = isset( $instance['display_title'] ) ? esc_attr( $instance['display_title'] ) : null;
        $type = 'page';
        if ( post_type_exists( 'contentblock' ) ) {
            $type='contentblock';
        }
        $args = array(
            'post_type' => $type,
        );
        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
            ?>
<p>
          <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title: ', 'pwp' ); ?></label>
          <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
<p>
          <input id="<?php echo $this->get_field_id( 'display_title' ); ?>" name="<?php echo $this->get_field_name( 'display_title' ); ?>" type="checkbox" value="1" <?php checked( '1', $display_title ); ?>/>
          <label for="<?php echo $this->get_field_id( 'display_title' ); ?>"><?php _e( 'Display title?', 'pwp' ); ?></label>
        </p>
            <p><label for="<?php echo $this->get_field_id( 'newsletter_rules' ); ?>"><?php _e( 'Newsletter rules', 'pwp' ); ?>
            <select id="<?php echo $this->get_field_id( 'newsletter_rules' ); ?>" name="<?php echo $this->get_field_name( 'newsletter_rules' ); ?>">
            <option value="0"><?php _e( 'Choose content page', 'pwp' ); ?></option>
            <?php
            while ( $query->have_posts() ) {
                $query->the_post();
                ?>
                <option value="<?php the_ID(); ?>" <?php selected( $newsletter_rules, get_the_ID() ); ?>><?php the_title(); ?></option>
                <?php
            }
            ?>
            </select>
            </label></p>
            <?php
        } else {
            _e( 'Not found post in type contentblock, to add this as newsletter rule create post in this type', 'pwp' );
        }
        wp_reset_query();
    }
}


new Newsletter_Widget();
//register widget


function f(){
    
    return register_widget("Newsletter_Widget");
}

f();
function zucc_get_calendar_filter( $content ) {
    $output = ucc_get_calendar( '' , '' , false );
    return $output;
}