<?php

//zmienia logo w logowaniu do zaplecza
function pwp_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/images/admin-logo.png);
            background-size: auto;
	    height: 105px;
	    width: auto;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'pwp_login_logo' );

/**
 * Display the variables
 *
 * @uses WP_DEBUG if TRUE then print output
 * 
 * @param $var variable to dump array | string
 * @return string Description
 */
function dump( $var, $echo = 1 ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG == TRUE ) {
        $output =  '<pre>' . print_r( $var, true ) . '</pre>';
    }
    
    if ( $output && $echo ){
        echo $output;
    }
}

/**
 * Return current post type
 * 
 * @return string Post type or null
 */
function pwp_current_post_type() {
  global $post, $typenow, $current_screen;
  if($post && $post->post_type) {
    return $post->post_type;
  } elseif($typenow) {
    return $typenow;
  } elseif($current_screen && $current_screen->post_type) {
    return $current_screen->post_type;
  } elseif(isset($_REQUEST['post_type'])) {
    return sanitize_key( $_REQUEST['post_type'] );
  } elseif(isset($_GET['post'])) {
    $thispost = get_post($_GET['post']);
    return $thispost->post_type;
  } else {
    return null;
  }
}


/**
 * Return inline style for background image
 */
function pwp_page_background(){
    $image_src = wp_get_attachment_image_src(get_post_thumbnail_id() ,'slide-large' );
    if(!empty($image_src)){
	echo 'style="background-image: url('.$image_src[0].');"';
    }
}

//lista menu z listy podstron
function pwp_child_pages() {
    $string = null;
    global $post;
    if (  $post->post_parent )
	$childpages = wp_list_pages( 'sort_column=post_title&title_li=&child_of=' . $post->post_parent . '&echo=0&post_type=resource' );
    else
	$childpages = wp_list_pages( 'sort_column=post_title&title_li=&child_of=' . $post->ID . '&echo=0&post_type=resource' );
    if ( $childpages ) {
	$string = '<ul class="nav nav-pills nav-stacked brand-menu">' . $childpages . '</ul>';
    }
    return $string;
}

//ukrywamy info o nowych wersjach
add_action('admin_menu','pwp_hide_update_notices');
function pwp_hide_update_notices() {
    remove_action( 'admin_notices', 'update_nag', 3 );
}

function pwp_no_widow($str) {


$str = htmlentities($str, null, 'utf-8');
$s = explode(" ",$str);
foreach ($s as $i => $j){
  
    if ((strlen($j)==1 )|| (strlen($j)==2)){

        $s[$i]="$j&nbsp;";
    }
}
//$return = implode(" ",$s) ;
$return  = null;
foreach($s as $el){
    if(strrpos($el, '&nbsp;')){
    $return .= $el;
    }else{
	 $return .= $el. '  ';
    }
}
//$return = preg_replace('/(?<=\b[a-z]) /i', '&nbsp;', $str);

$return = html_entity_decode($return,null, 'utf-8');


return $return;
}
add_filter('the_content', 'pwp_no_widow');
add_filter('the_excerpt', 'pwp_no_widow');