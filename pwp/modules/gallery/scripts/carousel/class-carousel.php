<?php
/*
 * Gallery Name: Pokaz slajdów responsywny
 * Description: Tworzy z obrazów w galerii responsywny pokaz slajdów z nawigacją
 */
class Carousel extends Gallery{

    private $params;
    private $defaults = array(
	
	'settings' => array(
	    'wrapper_class' => 'gallery-slideshow col-md-12 clearfix',
	    'item_class' => '',
	    'image_class' => 'img-responsive ',
	    'size' => 'large',
	    'timeout' => 0
	)
    );

    function __construct( Gallery $gallery = null, Array $params = null ) {
	if($gallery instanceof Gallery){
	
	    $this->setup($gallery->params);
	    $gallery->output = $this->create_gallery();
	}
    }

     public function setup($params){
	$this->params = array_merge( $this->defaults['settings'], $params );
    }

    function create_gallery() {
	$attr =  $this->params;
	$post = get_post();
	$this->instance = 0;
	$this->instance = uniqid();
	if ( ! empty( $attr['ids'] ) ) {
	    if ( empty( $attr['orderby'] ) )
		$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	    }
	$output = apply_filters('post_gallery', '', $attr);
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'columns'    => 1,
		'size'       => $this->params['size'],
		'include'    => '',
		'exclude'    => '',
		'timeout'   => $this->params['timeout']
	), $attr));


	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
		$img = wp_get_attachment_image_src($att_id, $size);

			$output .= '<img src="'. $img[0] .'" alt="" />\n';
		return $output;
	}
	$this->selector = "gallery-{$this->instance}";
	//if ( apply_filters( 'use_default_gallery_style', true ) )


	$pager = '<a class="left carousel-control" href="#'.$this->selector.'" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#'.$this->selector.'" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>';





	$output = '<div class="clear-both"><div id="'.$this->selector.'" class="in-post carousel slide" data-ride="carousel">';
	
$indicator = null;
	$i = 0;
	

	//$pager = '<div id="slide-navi'.$this->selector.'" class="gallery-slide-navi thumbnail-pager">';
$output .= '<div class="carousel-inner">';


	foreach ( $attachments as $id => $attachment ) {

	    $active = null;
	    if($i == 0){
		$active = 'active';
	    }


		$img = wp_get_attachment_image_src($id, $size);
		$link = '<img src="'. $img[0] .'" alt="" class="'.$this->params['image_class'].'"/>';
		//$pager .= '<img src="'. $img[0] .'" alt="" />';
                
		$output .= '<div class="item '.$active.'">';
                $output .= $link;
		if ( trim($attachment->post_excerpt) ) {
			$output .='<div class="carousel-caption">' . wptexturize($attachment->post_excerpt) .'</div>';
		}
                $output .= '</div>';
		$indicator .= '<li data-target="#'.$this->selector.'" data-slide-to="'.$i.'" class="'.$active.'"></li>';
	$i++;


		}



	//$pager .= "</div>";

	$output .= '</div> ';

	$output .= '<ol class="carousel-indicators">'.$indicator.'</ol>';

	//$output .= $pager;
	//$output .= '<div id="slide-navi'.$this->selector.'" class="gallery-slide-navi"></div>';
$output .= $pager;

	$output .= '</div></div> ';
	return $output;
    }


}

