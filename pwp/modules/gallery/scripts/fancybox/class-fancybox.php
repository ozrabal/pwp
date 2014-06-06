<?php
/*
 * Gallery Name: Powiększanie
 * Description: Po kliknięciu w miniaturkę otwiera okienko z pełnowymiarowym zdjęciem
 */
class Fancybox {
    private $params;
    private $defaults = array(
	'files' => array(
	    'scripts' => array( 'jquery.fancybox-1.3.4.js', 'init.fancybox.js' ),
	    'styles' => array( 'jquery.fancybox-1.3.4.css' )
	 ),
	'settings' => array(
	    'wrapper_class' => 'row gallery-wide clearfix',
	    'item_class' => 'lightbox',
	    'image_class' => 'img-responsive',
	    'size' => 'thumbnail'

	)
    );

    function __construct( Gallery $gallery = null, Array $params = null ) {
	if( $gallery instanceof Gallery ){
	    $gallery->include_files( $this->defaults['files']['scripts'] );
	    $gallery->include_styles( $this->defaults['files']['styles'] );
	    $this->setup( $gallery->params );
	    $gallery->output = $this->create_gallery();
	}
    }

    public function setup( $params ) {
	$this->params = array_merge( $this->defaults['settings'], $params );
    }

    function create_gallery() {
	$post = get_post();
	$instance = uniqid();
	if ( ! empty( $this->params['ids'] ) ) {
		if ( empty( $this->params['orderby'] ) )
			$this->params['orderby'] = 'post__in';
		$this->params['include'] = $this->params['ids'];
	}

	$output = apply_filters('post_gallery', '', $this->params);
	if ( $output != '' )
		return $output;

	if ( isset( $this->params['orderby'] ) ) {
		$this->params['orderby'] = sanitize_sql_orderby( $this->params['orderby'] );
		if ( !$this->params['orderby'] )
			unset( $this->params['orderby'] );
	}
	$post_id = null;
	if(!empty($post->ID)){$post_id = $post->ID;};
	extract( shortcode_atts( array(
		'order'      => 'DESC',
		'orderby'    => 'menu_order ID',
		'id'         => $post_id,
		'columns'    => 3,
		'size'       => $this->params['size'],
		'include'    => '',
		'exclude'    => ''
	), $this->params ) );

	$id = intval( $id );
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
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	$selector = "gallery-{$instance}";
	//if ( apply_filters( 'use_default_gallery_style', true ) )
	$output .= '<div class="container-fluid container-gallery"><div id="'.$selector.'" class="gallery-lightbox  '.$this->params['wrapper_class'].'  galleryid-'.$id.'">';
$columns = round(10/$columns);

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		//$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);
		//$link =  wp_get_attachment_link($id, $size, false, false);
	    $src = wp_get_attachment_image_src( $id , 'large',true);
		$link = '<div class="col-xs-6 col-sm-'.$columns.' ">
                          <div class="thumbnail"><a href="'. $src[0].'" class="'.$this->params['item_class'].'" rel="'.$selector.'" title="'.wptexturize($attachment->post_excerpt).'">'.wp_get_attachment_image( $id, $size, null, array( 'class' => $this->params['image_class']) ).'</a>';
		
		$caption = null;

		if ( trim($attachment->post_excerpt) ) {
			$caption= '<div class="caption hidden-xs">' . wptexturize($attachment->post_excerpt) . '</div>';
		}
		$output .= $link.$caption.'</div></div>';
	}
	$output .= "</div></div>\n";
	return $output;
    }
}