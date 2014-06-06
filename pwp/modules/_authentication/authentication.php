<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
add_action( 'pwp_init_authentication', 'addMyRules' );
/**
 * Description of Auth
 *
 * @author piotr
 */
if ( ! class_exists( 'Frontend_Login' ) ) :

class Frontend_Login {
	
    private $params;
    private $user;
    
    private $_defaults = array('action' => 'login');

    

    private function get_actions() {
        $methods = preg_grep( '/_Action/', get_class_methods( $this ) );
        foreach ( $methods as $method ) {
            $this->actions[] = basename( $method, '_Action' );
        }
    }
    
    private function is_action(){
        //$this->get_actions();
        if ( !in_array( $this->action, $this->actions, true ) && false === has_filter( 'login_form_' . $this->action ) ) {
            return false;
        }
        return true;
    }
    public function route(){
        
        if( get_query_var( 'action' ) ) {
            $this->action = get_query_var('action');
        } else if ( isset( $_GET['action'] ) ) {
            $this->action = $_GET['action'];
        } else {
            $this->action = $this->_defaults['action'];
        }
        if ( $this->is_action() ) {
            if ( method_exists( $this, $this->action . '_Action' ) ) 
                call_user_func( array( $this, $this->action . '_Action' ), array() );
        }else{
            $wp_error = new WP_Error();
            $wp_error->add('router', __( 'Action not allowed' ), 'message' );
            return $wp_error;
        }
    }
    
    public function __construct( $params = array() ) {
        global $current_user;
        //parent::__construct();
        $this->get_actions();
	if ( is_user_logged_in() ) {
	    $this->user = $current_user;
	}
	$this->g = $_GET;
        
        $defaults = array(
            'login_page'        => '?action=login',
            'logout_page'       => '?action=logout',
            'lost_password_page'=> '?action=lostpassword',
            'register_page'     => '?action=register',
            'user_page'         => 'dla-architekta/'
        );
        $this->params = array_merge( $defaults, $params );
        add_action( 'wp', array( $this, 'setup' ) );
    }
    public function user_page(){
        if ( !empty( $this->params['user_page'] ) ) {
            return ( $this->params['user_page'] );
        } else {
            return '';
        }
    }
    public function setup() {
        add_filter( 'login_url', array( $this, 'frontend_login_url' ), 10, 2 );
        add_filter( 'logout_url', array( $this, 'frontend_logout_url' ), 10, 2 );
        add_filter( 'lostpassword_url', array( $this, 'frontend_password_url' ), 10, 2 );
        add_filter( 'register_url', array( $this, 'frontend_register_url' ), 10, 2 );
	add_filter( 'resetpassword_url', array( $this, 'frontend_resetpassword_url' ), 10, 2 );
    }

   
    public function frontend_login_url( $login_url = null, $redirect = '' ) {
        $login_url = get_site_url( 'site_url' ) . $this->params['login_page']; // Link to login URL

        if ( !empty( $redirect ) ) {
            $login_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $login_url );
        }

        return $login_url;
    }
    
    public function frontend_logout_url( $logout_url = null, $redirect = '' ) {
        $logout_url = get_site_url( 'site_url' ) . $this->params['logout_page']; // Link to logout URL

        if ( !empty( $redirect ) ) {
            $logout_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $logout_url );
        }
        
        $logout_url = wp_nonce_url( $logout_url, 'log-out' );
        
        return $logout_url;
    }

    public function frontend_password_url( $lost_password_url = null, $redirect = '' ) {
        $lost_password_url = get_site_url( 'site_url' ) . $this->params['lost_password_page']; // Link to lostpassword URL

        if ( !empty( $redirect ) ) {
            $lost_password_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $lost_password_url );
        }

        return $lost_password_url;
    }

    public function frontend_register_url( $register_url ) {
        if ( !is_user_logged_in() ) {
            if ( get_option( 'users_can_register' ) ) {
                $register_url = get_site_url( 'site_url' ) . $this->params['register_page'] ; // Link to register URL
            } else {
                $register_url = '';
            }
        }

        return $register_url;
    }
 // @todo   
    public function frontend_resetpassword_url( $register_url ) {
        if ( !is_user_logged_in() ) {
            if ( get_option( 'users_can_register' ) ) {
                $register_url = get_site_url( 'site_url' ) . $this->params['register_page'] ; // Link to register URL
            } else {
                $register_url = '';
            }
        }

        return $register_url;
    }
    
    public function get_login_form($params = array()) {
        
         $defaults = array(
                'id'                => 'frontend-login-form',
                'class'             => 'login-form',
                'form_legend'       => __( 'Log in', 'pwp' ),
                'login_label'       => __( 'Login', 'pwp' ),
                'password_label'    => __( 'Password', 'pwp' ),
                'submit_class'      => 'btn',
                'submit_label'      => __( 'Login', 'pwp' ),
                'remember_label'    => __( 'Remember Me', 'pwp' ),
                'forgot_password'   => __( 'Forgot password', 'pwp' )
        );
        $params = array_merge( $defaults, $params );
        
        $form = '<form id="' . $params['id'] . '" class="' . $params['class']. '" action="' . $this->frontend_login_url() . '" method="post">
                <fieldset><legend>' . $params['form_legend'] . '</legend>
                <label for="login">' . $params['login_label'] . '</label>
                <input class="input" type="text" tabindex="10" name="log" id="login"/>
                <label for="password">' . $params['password_label'] . '</label>
                <input value="" class="input" type="password" size="20" tabindex="20" name="pwd" id="password" />';
        if ( $params['remember_label'] ) {
            $form .= '<label class="checkbox"><input name="rememberme" id="rememberme" value="forever" tabindex="90" type="checkbox">' . $params['remember_label'] . '</label>';
        }
        if ( $params['forgot_password'] ) {
            $form .= '<span class="help-block"><a href="' . $this->frontend_password_url() . '">' . $params['forgot_password'] . ' &raquo;</a></span>';
        }
        $form .= '<input name="wp-submit" class="' . $params['submit_class'] . '" id="wp-submit" value="' . $params['submit_label'] . '" tabindex="100" type="submit">
                    <input name="redirect_to" value="' . get_option('siteurl') . '" type="hidden">
                    <input name="testcookie" value="1" type="hidden">            
                    </fieldset></form>';
        return $form;
    }

    function login_header( $title = null, $message = '', $wp_error = '' ) {
        global $error, $interim_login, $current_site, $action;
        
        if(!empty($title)){
        $this->title = $title;
        }else{
            $this->title = __('Log in', 'pwp');
        }
        
        add_action( 'head', 'wp_no_robots' );

        if ( empty($wp_error) )
            $wp_error = new WP_Error();

	/*
        // Shake it!
	$shake_error_codes = array( 'empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'incorrect_password' );
	$shake_error_codes = apply_filters( 'shake_error_codes', $shake_error_codes );

	if ( $shake_error_codes && $wp_error->get_error_code() && in_array( $wp_error->get_error_code(), $shake_error_codes ) )
		add_action( 'head', 'wp_shake_js');
        */
	//$wp_error = new WP_Error();
        // Remove all stored post data on logging out.
	// This could be added by add_action('login_head'...) like wp_shake_js()
	// but maybe better if it's not removable by plugins
	if ( 'loggedout' == $wp_error->get_error_code() ) {
            ?>
            <script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("wp-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
            <?php
	}

	do_action( 'login_enqueue_scripts' );
	do_action( 'login_head' );

	if ( is_multisite() ) {
		$login_header_url   = network_home_url();
		$login_header_title = $current_site->site_name;
	} else {
		$login_header_url   = esc_url( home_url( '/' ) );
		$login_header_title = get_bloginfo( 'name' );
	}

	$login_header_url   = apply_filters( 'login_headerurl',   $login_header_url   );
	$login_header_title = apply_filters( 'login_headertitle', $login_header_title );

	$classes = array( 'login-action-' . $action, 'wp-core-ui' );
	if ( wp_is_mobile() )
		$classes[] = 'mobile';
	if ( is_rtl() )
		$classes[] = 'rtl';
	if ( $interim_login ) {
		$classes[] = 'interim-login';
		?>
		<style type="text/css">html{background-color: transparent;}</style>
		<?php

		if ( 'success' ===  $interim_login )
			$classes[] = 'interim-login-success';
	}

	$classes = apply_filters( 'login_body_class', $classes, $action );

	?>
        
        <h2><?php echo $this->title; ?></h2>
            
        <?php
        unset( $login_header_url, $login_header_title );
        $message = apply_filters( 'login_message', $message );
	if ( !empty( $message ) )
		echo $message . "\n";

	// In case a plugin uses $error rather than the $wp_errors object
	if ( !empty( $error ) ) {
		$wp_error->add( 'error', $error );
		unset( $error );
	}

	if ( $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data( $code );
			foreach ( $wp_error->get_error_messages($code  ) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
                if ( !empty($errors) )
			echo '<div id="login_error" class="alert alert-danger">' . apply_filters( 'login_errors', $errors ) . "</div>\n";
		if ( !empty($messages) )
			echo '<div class="alert alert-info">' . apply_filters( 'login_messages', $messages ) . "</div>\n";
	}
    } // End of login_header()


    function login_footer( $input_id = '' ) {
	global $interim_login;

	
	// Don't allow interim logins to navigate away from the page.
	/*
	if ( ! $interim_login ): ?>
            <div class="pager"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="previous pull-left" title="<?php esc_attr_e( 'Are you lost?' ); ?>"><?php _e( '&larr; Back to homepage' ); ?></a></div>
	<?php endif;
	 *
	 */
	?>


        <?php if ( !empty($input_id) ) : ?>
	<script type="text/javascript">
            try{ document.getElementById( '<?php echo $input_id; ?>' ).focus(); }catch( e ){}
            if ( typeof wpOnload == 'function' ) wpOnload();
	</script>
	<?php endif; ?>
        <?php do_action('footer'); ?>
        <?php
    }

    function retrieve_password() {
	global $wpdb, $current_site;

	$errors = new WP_Error();

	if ( empty( $_POST['user_login'] ) ) {
            $errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.', 'pwp'));
	} else if ( strpos( $_POST['user_login'], '@' ) ) {
            $user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
            if ( empty( $user_data ) )
                $errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.','pwp'));
	} else {
            $login = trim( $_POST['user_login'] );
            $user_data = get_user_by( 'login', $login );
	}

	do_action('lostpassword_post');

	if ( $errors->get_error_code() )
		return $errors;

	if ( !$user_data ) {
		$errors->add( 'invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.','pwp') );
		return $errors;
	}

	// redefining user_login ensures we return the right case in the email
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;

	do_action( 'retreive_password', $user_login );  // Misspelled and deprecated
	do_action( 'retrieve_password', $user_login );

	$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

	if ( ! $allow )
		return new WP_Error( 'no_password_reset', __( 'Password reset is not allowed for this user','pwp' ) );
	else if ( is_wp_error( $allow ) )
		return $allow;

	$key = $wpdb->get_var( $wpdb->prepare( "SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login ) );
	if ( empty( $key ) ) {
		// Generate something random for a key...
		$key = wp_generate_password( 20, false );
		do_action( 'retrieve_password_key', $user_login, $key );
		// Now insert the new md5 key into the db
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $key ), array( 'user_login' => $user_login ) );
	}
	$message = __( 'Someone requested that the password be reset for the following account:','pwp' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ,'pwp') . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:','pwp' ) . "\r\n\r\n";
	$message .= '<' . network_site_url( $this->user_page() . "?action=rp&key=$key&login=" . rawurlencode($user_login), 'login' ) . ">\r\n";

	if ( is_multisite() )
		$blogname = $GLOBALS['current_site']->site_name;
	else
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	$title = sprintf( __( '[%s] Password Reset' ), $blogname );

	$title = apply_filters( 'retrieve_password_title', $title );
	$message = apply_filters( 'retrieve_password_message', $message, $key );
	if ( $message && !wp_mail( $user_email, $title, $message ) )
		wp_die(  __( 'The e-mail could not be sent.','pwp' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.','pwp' ) );
	return true;
    }
    
    function check_password_reset_key( $key, $login ) {
	global $wpdb;

	$key = preg_replace( '/[^a-z0-9]/i', '', $key );

	if ( empty( $key ) || !is_string( $key ) )
		return new WP_Error( 'invalid_key', __( 'Invalid key','pwp' ) );

	if ( empty($login) || !is_string( $login ) )
		return new WP_Error( 'invalid_key', __( 'Invalid key','pwp' ) );

	$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE user_activation_key = %s AND user_login = %s", $key, $login ) );

	if ( empty( $user ) )
		return new WP_Error( 'invalid_key', __( 'Invalid key','pwp' ) );

	return $user;
    }

    function reset_password( $user, $new_pass ) {
	do_action( 'password_reset', $user, $new_pass );
	wp_set_password( $new_pass, $user->ID );
	wp_password_change_notification( $user );
    }
    public function resetpass_Action(){
	$this->rp_Action();
    }
    public function rp_Action(){

	$user = $this->check_password_reset_key($_GET['key'], $_GET['login']);
	if ( is_wp_error($user) ) {
            wp_safe_redirect( site_url( $this->user_page() . '?action=lostpassword&error=invalidkey') );
            exit;
	}

	$errors = new WP_Error();

	if ( isset($_POST['pass1']) && $_POST['pass1'] != $_POST['pass2'] )
            $errors->add( 'password_reset_mismatch', __( 'The passwords do not match.' ,'pwp') );

        do_action( 'validate_password_reset', $errors, $user );

	if ( ( ! $errors->get_error_code() ) && isset( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
            $this->reset_password($user, $_POST['pass1']);
            $this->login_header( __( 'Password Reset','pwp' ), '<div class="alert alert-success">' . __( 'Your password has been reset.','pwp' ) . ' <a href="' . esc_url( wp_login_url() ) . '">' . __( 'Log in','pwp' ) . '</a></div>' );
            $this->login_footer();
            exit;
	}

	wp_enqueue_script('utils');
	wp_enqueue_script('user-profile');

	$this->login_header(__('Reset Password','pwp'), false, $errors );
        ?>
    
        <form class="form-horizontal" name="resetpassform" id="resetpassform" action="<?php echo esc_url( site_url( $this->user_page() . '?action=resetpass&key=' . urlencode( $_GET['key'] ) . '&login=' . urlencode( $_GET['login'] ), 'login_post' ) ); ?>" method="post" autocomplete="off">
            <p class="message reset-pass"> <?php _e('Enter your new password below.','pwp') ?> </p>
	    <div class="control-group">

                <label class="control-label" for="pass1"><?php _e('New password','pwp') ?></label>
		<div class="controls">
                    <input type="password" name="pass1" id="pass1" class="form-control input-sm"  value="" autocomplete="off" />
                </div>
            </div>
            <div class="control-group">
		<label class="control-label" for="pass2"><?php _e('Confirm new password','pwp') ?></label>
                <div class="controls">
                    <input type="password" name="pass2" id="pass2" class="form-control input-sm" value="" autocomplete="off" />
                </div>
            </div>
            <div class="control-group clearfix">
                <div class="controls">
                    <input type="hidden" id="user_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" autocomplete="off" />
                    <div id="pass-strength-result" class="hide-if-no-js"><?php _e('Strength indicator','pwp'); ?></div>
                    <?php _e('Hint: The password should be at least seven characters long. To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ &amp; ).','pwp'); ?>
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn submit button btn-default btn-sm" value="<?php esc_attr_e('Reset Password','pwp'); ?>" />
                </div>
            </div>
        
       
            <a  href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log in','pwp' ); ?></a>
            <?php if ( get_option( 'users_can_register' ) ) : ?>
            <?php echo apply_filters( 'register', sprintf( '<a href="%s" class="label">%s</a>', esc_url( wp_registration_url() ), __( 'Register','pwp' ) ) ); ?>
            <?php endif; ?>
      
   </form>
        <?php
        $this->login_footer('user_pass');
    }




    function register_new_user( $user_login, $user_email ) {
	$errors = new WP_Error();

	$sanitized_user_login = sanitize_user( $user_login );
	$user_email = apply_filters( 'user_registration_email', $user_email );

	// Check the username
	if ( $sanitized_user_login == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ,'pwp') );
	} elseif ( ! validate_username( $user_login ) ) {
		$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ,'pwp') );
		$sanitized_user_login = '';
	} elseif ( username_exists( $sanitized_user_login ) ) {
		$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ,'pwp') );
	}

	// Check the e-mail address
	if ( $user_email == '' ) {
		$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ,'pwp') );
	} elseif ( ! is_email( $user_email ) ) {
		$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ,'pwp') );
		$user_email = '';
	} elseif ( email_exists( $user_email ) ) {
		$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ,'pwp') );
	}

	do_action( 'register_post', $sanitized_user_login, $user_email, $errors );

	$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );

	if ( $errors->get_error_code() )
		return $errors;

	$user_pass = wp_generate_password( 12, false );
	$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
	if ( ! $user_id ) {
		$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you&hellip; please contact the <a href="mailto:%s">webmaster</a> !','pwp' ), get_option( 'admin_email' ) ) );
		return $errors;
	}

	update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.

	wp_new_user_notification( $user_id, $user_pass );

	return $user_id;
    }


    public function profile_edit_form( $user = array(), $errors = null ) {
        ?>
        <form method="post" id="adduser" class="form-horizontal" action="<?php echo home_url( '/' . $this->user_page() . '/?action=editprofile' ); ?>">
            <div class="control-group <?php echo isset( $errors->errors['first_name'] ) ? 'error' : ''; ?>">
                <label class="control-label" for="first-name"><?php _e( 'First Name', 'pwp' ); ?></label>
                <div class="controls">
                    <input class="form-control input-sm" name="user[first_name]" type="text" id="first-name" value="<?php echo isset( $user['first_name'] ) ? $user['first_name'] : get_the_author_meta( 'first_name', $this->user->ID ); ?>" />
                    <?php if ( isset( $errors->errors['first_name'] ) ) { ?><span class="alert-danger"><?php echo implode( '<br>', $errors->errors['first_name'] ); ?></span><?php } ?>
                </div>
            </div>
            <div class="control-group <?php echo isset($errors->errors['last_name']) ? 'error' : ''; ?>">
                <label class="control-label" for="last-name"><?php _e('Last Name', 'pwp'); ?></label>
                <div class="controls">
                    <input class="form-control input-sm" name="user[last_name]" type="text" id="last-name" value="<?php echo isset($user['last_name']) ? $user['last_name'] : get_the_author_meta( 'last_name', $this->user->ID ); ?>" />
                    <?php if(isset($errors->errors['last_name'])){ ?><span class="alert-danger"><?php echo implode('<br>', $errors->errors['last_name']); ?></span><?php } ?>
                </div>
            </div>
            <div class="control-group <?php echo isset($errors->errors['email']) ? 'error' : ''; ?>">
                <label class="control-label" for="email"><?php _e('E-mail *', 'pwp'); ?></label>
                <div class="controls">
                    <input class="form-control input-sm" name="user[email]" type="text" id="email" value="<?php echo isset($user['email']) ? $user['email'] : get_the_author_meta( 'email', $this->user->ID ); ?>" />
                    <?php if(isset($errors->errors['email'])){ ?><span class="alert-danger"><?php echo implode('<br>', $errors->errors['email']); ?></span><?php } ?>
                </div>
            </div>
            <div class="control-group <?php echo isset($errors->errors['url']) ? 'error' : ''; ?>">
                <label class="control-label" for="url"><?php _e('Website', 'pwp'); ?></label>
                <div class="controls">
                    <input class="form-control input-sm" name="user[url]" type="text" id="url" value="<?php echo isset($user['url']) ? $user['url'] : get_the_author_meta( 'url', $this->user->ID ); ?>" />
                    <?php if(isset($errors->errors['url'])){ ?><span class="alert-danger"><?php echo implode('<br>', $errors->errors['url']); ?></span><?php } ?>
                </div>
            </div>
            <div class="control-group <?php echo isset($errors->errors['pass']) ? 'error' : ''; ?>">
                <label class="control-label" for="pass1"><?php _e('Password *', 'pwp'); ?> </label>
                <div class="controls">
                    <input class="form-control input-sm" name="user[pass1]" type="password" id="pass1" />
                </div>
            </div>
            <div class="control-group <?php echo isset($errors->errors['pass']) ? 'error' : ''; ?>">
                <label class="control-label" for="pass2"><?php _e('Repeat Password *', 'pwp'); ?></label>
                <div class="controls">
                    <input class="form-control input-sm" name="user[pass2]" type="password" id="pass2" />
                    <?php if(isset($errors->errors['pass'])){ ?><span class="alert-danger"><?php echo implode('<br>', $errors->errors['pass']); ?></span><?php } ?>
                </div>
            </div>
            <?php //do_action('edit_user_profile',$this->user); ?>
            <div class="control-group">
                <div class="controls clearfix">
                    <input name="updateuser" type="submit" id="updateuser" class="btn submit button" value="<?php _e('Update profile', 'pwp'); ?>" />
                    <?php wp_nonce_field( 'updateuser' ) ?>
                    <input name="action" type="hidden" id="action" value="updateuser" />
                </div>
            </div>
        </form>
        <?php
    }

    public function profile($errors) {
	 //$errors = new WP_Error();
	//dump($errors);
        ?>

	<h2><?php _e('User profile', 'pwp'); ?></h2>
	<ul class="list-group">
	    <li class="list-group-item"> 
		<h4 class="list-group-item-heading"><?php _e( 'First Name and Last Name', 'pwp' ); ?></h4>
		<p class="list-group-item-text"><?php the_author_meta( 'first_name', $this->user->ID ). ' ' . the_author_meta( 'last_name', $this->user->ID ); ?></p></li>
        <li class="list-group-item">
	    <h4 class="list-group-item-heading"><?php _e('E-mail', 'pwp'); ?></h4>
	    <p class="list-group-item-text"><a href="mailto:<?php $email = get_the_author_meta( 'user_email', $this->user->ID ); echo $email; ?>"><?php echo $email ?></a></p></li>
        <li class="list-group-item">
	    <h4 class="list-group-item-heading"><?php _e('Website', 'pwp'); ?></h4>
	    <p class="list-group-item-text"><a href="<?php $url = get_the_author_meta( 'user_url', $this->user->ID ); echo $url; ?>" target="_blank" rel="nofollow"><?php echo $url; ?></a></p></li>
        
	</ul>
	

        <a href="?action=editprofile" class="btn btn-primary pull-right"><?php _e( 'Edit profile', 'pwp' ) ?></a>
        <?php
    }

    public function editprofile_Action(){

	$this->profile_edit_form();

	
	

    }

    public function updateuser_Action(){
	$http_post = ( 'POST' == $_SERVER['REQUEST_METHOD'] );
	
	//exit;
	$user = array();
	
        if ( $http_post && isset( $_POST['user'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'updateuser' )   ) {
            $user = $_POST['user'];
	    $errors = $this->update_profile( $user );
            if ( !is_wp_error( $errors ) ) {
                $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '' . $this->user_page() . '/profile';
		
		//exit;
                wp_safe_redirect( $redirect_to );
                exit();
            }
	    $this->profile_edit_form( $user, $errors );
	}
    }

    public function update_profile( $user = array() ) {
        $errors = new WP_Error();
        $user['ID'] = $this->user->ID;
        if ( !empty( $user['first_name'] ) ) {
            $user['first_name'] = esc_attr( $user['first_name'] );
        }

        if ( !empty( $user['last-name'] ) ) {
            $user['last_name'] = esc_attr( $user['last-name'] );
        }

        if ( !empty( $user['email'] ) ) {
            //pusty
            if ( !is_email( esc_attr( $user['email'] ) ) ) {
                $errors->add( 'email', __( 'The Email you entered is not valid.  please try again.', 'pwp' ) );
            } else {
                //juz istnieje
                if ( ( email_exists( esc_attr( $user['email'] ) ) == $this->user->ID ) || !email_exists( esc_attr( $user['email'] ) ) ) {
                    $user['user_email'] = esc_attr( $user['email'] );
                } else {
                    $errors->add( 'email', __( 'This email is already used by another user.  try a different one.', 'pwp' ) );
                }
            }
        }else{
	     $errors->add( 'email', __( 'The Email you entered is not valid.  please try again.', 'pwp' ) );
	}


        if ( !empty( $user['url'] ) ) {

            $user['user_url'] = esc_attr( $user['url'] );
        }

        if ( !empty($user['pass1'] ) && !empty( $user['pass2'] ) ) {
            if ( $user['pass1'] == $user['pass2'] ) {
                $user['user_pass'] = esc_attr( $user['pass1'] );
            } else {
                $errors->add( 'pass', __( 'The passwords you entered do not match.  Your profile was not updated.', 'pwp' ) );
            }
        }


        //jesli sa bledy koniec
        if ( $errors->get_error_code() )
            return $errors;
            //jesli nie ma bledow update
            //do_action( 'edit_user_profile_update', $this->user->ID );
            wp_update_user( $user );
	   $errors->add( 'pass', __( 'The passwords you entered do not match.  Your profile was not updated.', 'pwp' ) );
    }

    
    public function login($action){
        
        if(is_user_logged_in()){
	    if( isset( $frontend_login->g['error'] ) ) {
		$_GET['error'] = $frontend_login->g['error'];
		$errors->add('loggedin', __( 'Option only for not logged in users' ), 'message' );
		$frontend_login->login_header(__('Info', 'pwp'), '', $errors);
		break;
	    }
	    $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '/' . $frontend_login->user_page() . '?error=loggedin';
	    //wp_safe_redirect( $redirect_to );
            //
	    //exit();
	}
        //Set a cookie now to see if they are supported by the browser.
        setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
        if ( SITECOOKIEPATH != COOKIEPATH )
            setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);
        $secure_cookie = '';
	$customize_login = isset( $_REQUEST['customize-login'] );
	if ( $customize_login )
            wp_enqueue_script( 'customize-base' );

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
            $user_name = sanitize_user($_POST['log']);
            if ( $user = get_user_by('login', $user_name) ) {
		if ( get_user_option('use_ssl', $user->ID) ) {
                    $secure_cookie = true;
                    force_ssl_admin(true);
		}
            }
	}

	if ( isset( $_REQUEST['redirect_to'] ) ) {
            $redirect_to = $_REQUEST['redirect_to'];
            // Redirect to https if user wants ssl
            if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
                $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
            $redirect_to = admin_url();
	}

	$reauth = empty($_REQUEST['reauth']) ? false : true;

	// If the user was redirected to a secure login form from a non-secure admin page, and secure login is required but secure admin is not, then don't use a secure
	// cookie and redirect back to the referring non-secure admin page. This allows logins to always be POSTed over SSL while allowing the user to choose visiting
	// the admin via http or https.
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
            $secure_cookie = false;

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

	if ( !is_wp_error($user) && !$reauth ) {
            if ( $interim_login ) {
                $message = '<div class="alert alert-success">' . __('You have logged in successfully.') . '</div>';
		$interim_login = 'success';
		login_header( '', $message ); ?>
			
		<?php do_action( 'login_footer' ); ?>
		<?php if ( $customize_login ) : ?>
                    <script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
                <?php endif; ?>
                <?php exit;
            }

            if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
                // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
                if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
                    $redirect_to = user_admin_url();
                elseif ( is_multisite() && !$user->has_cap('read') )
                    $redirect_to = get_dashboard_url( $user->ID );
                elseif ( !$user->has_cap('edit_posts') )
                    $redirect_to = admin_url('profile.php');
            }
            //redirect if superadmin to zaplecze         
            if ( is_super_admin( $user->ID ) ){
                $redirect_to = user_admin_url();
            }
            wp_safe_redirect($redirect_to);
            exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) || $reauth )
            $errors = new WP_Error();

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
            $errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));

	if ( $interim_login ) {
            if ( ! $errors->get_error_code() )
                $errors->add('expired', __('Session expired. Please log in again. You will not move away from this page.'), 'message');
	} else {
            // Some parts of this script use the main login form to display a message
            if	( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
                $errors->add('loggedout', __('You are now logged out.'), 'message');
            elseif ( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
                $errors->add('registerdisabled', __('User registration is currently not allowed.'));
            elseif ( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
		$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
            elseif ( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
                $errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
            elseif ( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
		$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
            elseif ( strpos( $redirect_to, 'about.php?updated' ) )
		$errors->add('updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to experience the awesomeness.' ), 'message' );
	}
	$errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

	// Clear any stale cookies.
	if ( $reauth )
            wp_clear_auth_cookie();
        $this->login_header(__('Log In','pwp'), '', $errors);
        
	if ( isset($_POST['log']) )
            $user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(wp_unslash($_POST['log'])) : '';
            $rememberme = ! empty( $_POST['rememberme'] );
        ?>
        
        <form name="loginform" id="loginform" class="form-horizontal login-form" action="" method="post">
            <div class="control-group">
                <label class="control-label" for="user_login"><?php _e('Username') ?></label>
                <div class="controls">
                    <input type="text" name="log" id="user_login" class="input" value="<?php echo esc_attr($user_login); ?>" />
                </div>
            </div>
            <div class="control-group">
		<label class="control-label" for="user_pass"><?php _e('Password') ?></label>
                <div class="controls">
                    <input type="password" name="pwd" id="user_pass" class="input" value="" />
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <?php do_action('login_form'); ?>
                    <label for="remember" class="checkbox"><input name="rememberme" type="checkbox" id="remember" value="forever" <?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me'); ?></label>
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn submit button button-primary button-large" value="<?php esc_attr_e('Log In'); ?>" />
                    <?php if ( $interim_login ) { ?>
                    <input type="hidden" name="interim-login" value="1" />
                    <?php } else { ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
                    <?php } ?>
                    <?php if ( $customize_login ) : ?>
                    <input type="hidden" name="customize-login" value="1" />
                    <?php endif; ?>
                    <input type="hidden" name="testcookie" value="1" />
                </div>
            </div>
        </form>
	<?php if ( ! $interim_login ) { ?>
            <?php if ( ! isset( $_GET['checkemail'] ) || ! in_array( $_GET['checkemail'], array( 'confirm', 'newpass' ) ) ) : ?>
            <?php if ( get_option( 'users_can_register' ) ) : ?>
		<?php echo (  sprintf( '<a href="%s" class="label">%s</a>', esc_url( wp_registration_url() ), __( 'Register' ) ) ); ?>
            <?php endif; ?>
            <a class="label" href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>
            <?php endif; ?>
        <?php } ?>

        <script type="text/javascript">
            function wp_attempt_focus(){
                setTimeout( function(){ try{
                <?php if ( $user_login || $interim_login ) { ?>
                    d = document.getElementById('user_pass');
                    d.value = '';
                <?php } else { ?>
                    d = document.getElementById('user_login');
                    <?php if ( 'invalid_username' == $errors->get_error_code() ) { ?>
                    if( d.value != '' )
                        d.value = '';
                        <?php 
                    }
                }?>
                d.focus();
                d.select();
                } catch(e){}
                }, 200);
            }
            <?php if ( !$error ) { ?>
            wp_attempt_focus();
            <?php } ?>
            if(typeof wpOnload=='function')wpOnload();
            <?php if ( $interim_login ) { ?>
            (function(){
            try {
                var i, links = document.getElementsByTagName('a');
                for ( i in links ) {
                    if ( links[i].href )
			links[i].target = '_blank';
                }
            } catch(e){}
            }());
            <?php } ?>
        </script>
    <?php
    $frontend_login->login_footer();
	
        
        
    }
    
    private function ssl_check(){
        // Redirect to https login if forced to use SSL
if ( force_ssl_admin() && ! is_ssl() ) {
    if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
	wp_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
	exit();
    } else {
	wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	exit();
    }
} 
    }

    private function http_header(){
        nocache_headers();
        header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));
    }

    private function is_relocated(){
        
        if ( defined( 'RELOCATE' ) && RELOCATE ) { // Move flag is set
    if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
        $_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );
        $url = dirname( set_url_scheme( 'http://' .  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ) );
        if ( $url != get_option( 'siteurl' ) )
            update_option( 'siteurl', $url );
}
        
    }
    
    
    public function login_Action($param){
        
        require_once( ABSPATH . '/wp-load.php' );
        $this->ssl_check();
        $this->http_header();
        $this->is_relocated();
        do_action( 'login_init' );
        do_action( 'login_form_' . $this->action );

$http_post = ( 'POST' == $_SERVER['REQUEST_METHOD'] );
$interim_login = isset( $_REQUEST['interim-login'] );

$errors = new WP_Error();

	if(is_user_logged_in()){
	    
		$errors->add('loggedin', __( 'You already logged in', 'pwp' ), 'message' );
		$this->login_header(__('Info', 'pwp'), '', $errors);
		return;
	    
	    $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '/' . $this->user_page() . '?error=loggedin';
	    //wp_safe_redirect( $redirect_to );
            //
	    //exit();
	}
        //Set a cookie now to see if they are supported by the browser.
        setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
        if ( SITECOOKIEPATH != COOKIEPATH )
            setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);
        $secure_cookie = '';
	$customize_login = isset( $_REQUEST['customize-login'] );
	if ( $customize_login )
            wp_enqueue_script( 'customize-base' );

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
            $user_name = sanitize_user($_POST['log']);
            if ( $user = get_user_by('login', $user_name) ) {
		if ( get_user_option('use_ssl', $user->ID) ) {
                    $secure_cookie = true;
                    force_ssl_admin(true);
		}
            }
	}

	if ( isset( $_REQUEST['redirect_to'] ) ) {
            $redirect_to = $_REQUEST['redirect_to'];
            // Redirect to https if user wants ssl
            if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
                $redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
            $redirect_to = admin_url();
	}

	$reauth = empty($_REQUEST['reauth']) ? false : true;

	// If the user was redirected to a secure login form from a non-secure admin page, and secure login is required but secure admin is not, then don't use a secure
	// cookie and redirect back to the referring non-secure admin page. This allows logins to always be POSTed over SSL while allowing the user to choose visiting
	// the admin via http or https.
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
            $secure_cookie = false;

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

	if ( !is_wp_error($user) && !$reauth ) {
            if ( $interim_login ) {
                $message = '<div class="alert alert-success">' . __('You have logged in successfully.') . '</div>';
		$interim_login = 'success';
		login_header( '', $message ); ?>
			
		<?php do_action( 'login_footer' ); ?>
		<?php if ( $customize_login ) : ?>
                    <script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
                <?php endif; ?>
                <?php exit;
            }

            if ( ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) ) {
                // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
                if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) )
                    $redirect_to = user_admin_url();
                elseif ( is_multisite() && !$user->has_cap('read') )
                    $redirect_to = get_dashboard_url( $user->ID );
                elseif ( !$user->has_cap('edit_posts') )
                    //$redirect_to = admin_url('profile.php');
                $redirect_to = $this->user_page();
            }
            //redirect if superadmin to zaplecze         
            if ( is_super_admin( $user->ID ) ){
                $redirect_to = user_admin_url();
            }
            wp_safe_redirect($redirect_to);
            exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) || $reauth )
            $errors = new WP_Error();

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
            $errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));

	if ( $interim_login ) {
            if ( ! $errors->get_error_code() )
                $errors->add('expired', __('Session expired. Please log in again. You will not move away from this page.'), 'message');
	} else {
            // Some parts of this script use the main login form to display a message
            if	( isset($_GET['loggedout']) && true == $_GET['loggedout'] )
                $errors->add('loggedout', __('You are now logged out.'), 'message');
            elseif ( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
                $errors->add('registerdisabled', __('User registration is currently not allowed.'));
            elseif ( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
		$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
            elseif ( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
                $errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
            elseif ( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
		$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
            elseif ( strpos( $redirect_to, 'about.php?updated' ) )
		$errors->add('updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to experience the awesomeness.' ), 'message' );
	}
	$errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

	// Clear any stale cookies.
	if ( $reauth )
            wp_clear_auth_cookie();
        $this->login_header(__('Log In','pwp'), '', $errors);
        
	if ( isset($_POST['log']) )
            $user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(wp_unslash($_POST['log'])) : '';
            $rememberme = ! empty( $_POST['rememberme'] );
        ?>
        
                    
        <?php $this->login_form(array('interim' => $this->is_interim(), 'customize' => $this->is_customize(), 'redirect_to' => $redirect_to)); ?>            
                    
        
	<?php if ( ! $interim_login ) { ?>
            <?php if ( ! isset( $_GET['checkemail'] ) || ! in_array( $_GET['checkemail'], array( 'confirm', 'newpass' ) ) ) : ?>
            

            <!--<a class="label" href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ); ?>"><?php _e( 'Lost your password?' ); ?></a>-->

 <?php endif; ?>
        <?php } ?>

        <script type="text/javascript">
            function wp_attempt_focus(){
                setTimeout( function(){ try{
                <?php if ( !empty($user_login) || !empty($interim_login) ) { ?>
                    d = document.getElementById('user_pass');
                    d.value = '';
                <?php } else { ?>
                    d = document.getElementById('user_login');
                    <?php if ( 'invalid_username' == $errors->get_error_code() ) { ?>
                    if( d.value != '' )
                        d.value = '';
                        <?php 
                    }
                }?>
                d.focus();
                d.select();
                } catch(e){}
                }, 200);
            }
            <?php if ( !isset($error )) { ?>
            wp_attempt_focus();
            <?php } ?>
            if(typeof wpOnload=='function')wpOnload();
            <?php if ( $interim_login ) { ?>
            (function(){
            try {
                var i, links = document.getElementsByTagName('a');
                for ( i in links ) {
                    if ( links[i].href )
			links[i].target = '_blank';
                }
            } catch(e){}
            }());
            <?php } ?>
        </script>
    <?php
    $this->login_footer();
        
        
        
      
        
    }
    
    public function login_form(Array $args){
        $rememberme = ! empty( $_POST['rememberme'] );
        ?>
	
        <form name="loginform" id="loginform" class="form-horizontal login-form" action="" method="post">
            <div class="control-group">
                <label class="control-label" for="user_login"><?php _e('Username','pwp') ?></label>
                <div class="controls">
                    <input type="text" name="log" id="user_login" class="form-control input-sm" value="" />
                </div>
            </div>
            <div class="control-group">
		<label class="control-label" for="user_pass"><?php _e('Password', 'pwp') ?></label>
                <div class="controls">
                    <input type="password" name="pwd" id="user_pass" class="form-control input-sm" value="" />
                </div>
            </div>
            <div class="control-group">
                <div class="controls clearfix">
                    <?php do_action('login_form'); ?>
                    <label for="remember" class="checkbox"><input name="rememberme" type="checkbox" id="remember" value="forever" <?php checked( $rememberme ); ?> /> <?php esc_attr_e('Remember Me','pwp'); ?></label>
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn submit button btn-default btn-sm" value="<?php esc_attr_e('Log In', 'pwp'); ?>" />
                    <?php if ( $args['interim'] ) { ?>
                    <input type="hidden" name="interim-login" value="1" />
                    <?php } else { ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr($args['redirect_to']); ?>" />
                    <?php } ?>
                    <?php if ( $args['customize'] ) : ?>
                    <input type="hidden" name="customize-login" value="1" />
                    <?php endif; ?>
                    <input type="hidden" name="testcookie" value="1" />
                </div>
            </div>
	    <a  href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found', 'pwp' ); ?>"><?php _e( 'Lost your password?','pwp' ); ?></a>
<?php if ( get_option( 'users_can_register' ) ) : ?>
		<?php echo (  sprintf( '<a href="%s" >%s</a>', esc_url( wp_registration_url() ), __( 'Register','pwp' ) ) ); ?>
            <?php endif; ?>
        </form>
        <?php
    }
    
    
    public function showprofile_Action($errors){
	$this->profile($errors);
        
    }

    public function logout_Action() {
        check_admin_referer( 'log-out' );
	wp_logout();
	$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : get_option( 'siteurl' ) . '?loggedout=true';
	wp_safe_redirect( $redirect_to );
	exit();
    }
    
    private function is_post() {
        $http_post = ( 'POST' == $_SERVER['REQUEST_METHOD'] );
        return $http_post;
    }
    
    private function is_interim() {
        $interim_login = isset( $_REQUEST['interim-login'] );
        return $interim_login;
    }
    
    private function is_customize() {
        $customize_login = isset( $_REQUEST['customize-login'] );
        return $customize_login;
    }
    
    public function register_Action() {
        $errors = null;
        if(is_user_logged_in()){
	    $redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : '/' . $frontend_login->user_page() . 'profile?error=error';
            wp_safe_redirect( $redirect_to );
            exit();
	}
	if ( is_multisite() ) {
            // Multisite uses wp-signup.php
            wp_redirect( apply_filters( 'wp_signup_location', network_site_url('wp-signup.php') ) );
            exit;
	}

	if ( !get_option('users_can_register') ) {
            //wp_redirect( site_url('wp-login.php?registration=disabled') );
	    wp_redirect(site_url());
            exit();
	}


if( isset( $frontend_login->g['error'] ) ) {
            $_GET['error'] = $frontend_login->g['error'];
        }
        if( isset( $frontend_login->g['checkemail'] ) ) {
            $_GET['checkemail'] = $frontend_login->g['checkemail'];
        }
	$user_login = '';
	$user_email = '';
	if ( $this->is_post() ) {
            $user_login = $_POST['user_login'];
            $user_email = $_POST['user_email'];
            $errors = $this->register_new_user($user_login, $user_email);
            if ( !is_wp_error($errors) ) {
			$this->login_header(__('Registration Form', 'pwp'), '<p class="message register">' . __('Register For This Site', 'pwp') . '</p>',$errors);

		$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : esc_url( site_url($this->user_page() . '?checkemail=registered') );
                //wp_safe_redirect( $redirect_to );
		//exit();
                echo '<div id="login_error" class="alert alert-info">Welcome</div>\n';
		$this->login_footer('user_login');
		//break;
                return;
            }
	}

	$redirect_to = apply_filters( 'registration_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
	$this->login_header(__('Registration Form', 'pwp'), '',$errors);
        ?>
        <form class="form-horizontal" name="registerform" id="registerform" action="<?php echo esc_url( site_url( $this->user_page() . '?action=register') ); ?>" method="post">
            <div class="control-group">
                <label class="control-label" for="user_login"><?php _e('Username', 'pwp') ?></label>
                <div class="controls">
                    <input type="text" name="user_login" id="user_login" class="form-control input-sm" value="<?php echo esc_attr(wp_unslash($user_login)); ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="user_email"><?php _e('E-mail', 'pwp') ?></label>
                <div class="controls">
                    <input type="text" name="user_email" id="user_email" class="form-control input-sm" value="<?php echo esc_attr(wp_unslash($user_email)); ?>" />
                </div>
            </div>
            <div class="control-group">
                <div class="controls clearfix"> 
                    <?php do_action('register_form'); ?>
                    <p id="reg_passmail"><?php _e('A password will be e-mailed to you.', 'pwp') ?></p>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn submit button btn-default btn-sm" value="<?php esc_attr_e('Register', 'pwp'); ?>" />
                </div>
            </div>
            <a  href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e( 'Log in' ); ?></a>
        <a  href="<?php echo esc_url( wp_lostpassword_url() ); ?>" title="<?php esc_attr_e( 'Password Lost and Found' ,'pwp' ) ?>"><?php _e( 'Lost your password?','pwp' ); ?></a>
        
        </form>
        <?php
        $this->login_footer('user_login');
    
        
        
        
    }
    
    
    
    public function lostpassword_Action(){
        
        $errors = null;
        if ( $this->is_post() ) {
            $errors = $this->retrieve_password();
            if ( !is_wp_error( $errors ) ) {
                $redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : 'user/?action=login&checkemail=confirm';
		wp_safe_redirect( $redirect_to );
		exit();
            }
	}
        //fix na rewrite
        // @todo inaczej QAS get w prostym urlu
        //if( isset( $frontend_login->g['error'] ) ) {
        //    $_GET['error'] = $frontend_login->g['error'];
        //}
	if ( isset( $_GET['error']) && 'invalidkey' == $_GET['error'] )
	    $errors->add('invalidkey', __('Sorry, that key does not appear to be valid.','pwp'));
        $redirect_to = apply_filters( 'lostpassword_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
        do_action('lost_password');
	$this->login_header(__('Lost Password','pwp'), '<div class="alert alert-info">' . __('Please enter your username or email address. You will receive a link to create a new password via email.','pwp') . '</div>', $errors);
        $user_login = isset($_POST['user_login']) ? wp_unslash($_POST['user_login']) : '';
        ?>
        <form name="lostpasswordform" id="lostpasswordform" class="form-horizontal" action="<?php echo esc_url( site_url( $this->user_page() . '?action=lostpassword' ) ); ?>" method="post">
            <div class="control-group">
                <label for="user_login" class="control-label"><?php _e( 'Username or E-mail:','pwp' ) ?></label>
                <div class="controls">
                    <input type="text" name="user_login" id="user_login" class="form-control input-sm" value="<?php echo esc_attr( $user_login ); ?>" />
                </div>
            </div>
            <div class="control-group clearfix">
                <div class="controls">
                    <?php do_action( 'lostpassword_form' ); ?>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn submit button btn-default btn-sm" value="<?php esc_attr_e('Get New Password','pwp'); ?>" />
                </div>
            </div>
	     
            <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php _e('Log in','pwp') ?></a>
            <?php if ( get_option( 'users_can_register' ) ) : ?>
            <?php echo (  sprintf( '<a href="%s" >%s</a>', esc_url( wp_registration_url() ), __( 'Register','pwp' ) ) ); ?>
            <?php endif; ?>
        
        </form>
       
        <?php
        $this->login_footer('user_login');
    
        
        
    }
    
    

} // end class
endif; // end class exists check

$lang = null;
                                        if(function_exists( 'pll_current_language' ) && pll_current_language() != pll_default_language()){
                                            $lang = '/'.pll_current_language();
                                        }
global $frontend_login;
$args = array(
            'login_page'        => $lang.'/user/?action=login',
            'logout_page'       => $lang.'/user/?action=logout',
            'lost_password_page'=> $lang.'/user/?action=lostpassword',
            'register_page'     => $lang.'/user/?action=register',
    'user_page'=>$lang.'/user'
        );
$wp_error = new WP_Error();
$frontend_login = new Frontend_Login($args, $wp_error);

//template functions
function pwp_login_form($params = array()) {
    global $frontend_login;
    echo $frontend_login->get_login_form($params);
    //Frontend_Login::login_form($params);
};







function pwp_authenticate(){
    global $frontend_login;
    
    //start magic
    $wp_error = $frontend_login->route();
   
           if ( is_object($wp_error) && $wp_error->get_error_code() ) {
		$errors = '';
		$messages = '';
		foreach ( $wp_error->get_error_codes() as $code ) {
			$severity = $wp_error->get_error_data( $code );
			foreach ( $wp_error->get_error_messages($code  ) as $error ) {
				if ( 'message' == $severity )
					$messages .= '	' . $error . "<br />\n";
				else
					$errors .= '	' . $error . "<br />\n";
			}
		}
                if ( !empty($errors) )
			echo '<div id="login_error" class="alert alert-danger">' . apply_filters( 'login_errors', $errors ) . "</div>\n";
		if ( !empty($messages) )
			echo '<div class="alert alert-info">' . apply_filters( 'login_messages', $messages ) . "</div>\n";
                   
                
                        }
    
    //dump($_GET);
    
    //dump($frontend_login);
    
    //$f = $frontend_login->g['action'];
    //$frontend_login->$f($f);
}


/*
function pwp_login_form(){
     Frontend_Login::login_form();
}
 * 
 */


add_action( 'init', 'addMyRules' );
function addMyRules(){
    
   //dump(pll_current_language());
    //if(  pll_current_language() == 'pl'){
    add_rewrite_rule('^user([^/]*)/([^/]*)/?','index.php?page_id=148&action=showprofile','top');
    add_rewrite_rule('^user/profile([^/]*)/?','index.php?page_id=148&action=showprofile','top');
    add_rewrite_rule('^user/?','index.php?page_id=148','top');
    //}else{
    add_rewrite_rule('^en/user([^/]*)/([^/]*)/?','index.php?page_id=292&action=showprofile','top');
    add_rewrite_rule('^en/user/profile([^/]*)/?','index.php?page_id=292&action=showprofile','top');
    add_rewrite_rule('^en/user/?','index.php?page_id=292','top');
   // }
    add_rewrite_tag('%action%','([^&]+)');
    add_rewrite_tag('%error%','([^&]+)');
global $wp_rewrite;

//Call flush_rules() as a method of the $wp_rewrite object
$wp_rewrite->flush_rules( false );
//dump($wp_rewrite);
    
}

function zaddMyRules(){

   //dump(pll_current_language());
    //if(  pll_current_language() == 'pl'){
    add_rewrite_rule('^user([^/]*)/([^/]*)/?','index.php?page_id=60&action=showprofile','top');
    add_rewrite_rule('^user/profile([^/]*)/?','index.php?page_id=60&action=showprofile','top');
    add_rewrite_rule('^user/?','index.php?page_id=60','top');
    //}else{
    add_rewrite_rule('^en/user([^/]*)/([^/]*)/?','index.php?page_id=292&action=showprofile','top');
    add_rewrite_rule('^en/user/profile([^/]*)/?','index.php?page_id=292&action=showprofile','top');
    add_rewrite_rule('^en/user/?','index.php?page_id=292','top');
   // }
    add_rewrite_tag('%action%','([^&]+)');
    add_rewrite_tag('%error%','([^&]+)');
global $wp_rewrite;

//Call flush_rules() as a method of the $wp_rewrite object
$wp_rewrite->flush_rules( false );
//dump($wp_rewrite);

}


function zwp_shake_js() {
	if ( wp_is_mobile() )
		return;
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
function s(id,pos){g(id).left=pos+'px';}
function g(id){return document.getElementById(id).style;}
function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
</script>
<?php
}
