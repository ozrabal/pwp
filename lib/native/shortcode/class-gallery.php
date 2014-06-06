<?php

class Shortcode_Gallery {




    public function __construct() {

add_shortcode('pwp_gallery', array($this,'pwp_gallery_shortcode'));
    }
    

function pwp_gallery_shortcode($attr) {
	global $post;

	static $instance = 0;
	$instance++;

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'div',
		'icontag'    => 'div',
		'captiontag' => 'div',
		'columns'    => 1,
		'size'       => 'medium',
		'include'    => '',
		'exclude'    => ''

	), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
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

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "";
	$size_class = sanitize_html_class( $size );

	$gallery_div = '<div id="'.$selector.'" class="carousel slide" data-ride="carousel">';
	//$gallery_div = "<div id='$selector' data-cycle-timeout=7000 data-cycle-pager='.cycle-pager' data-cycle-slides='> div' data-cycle-fx='scrollHorz' class='cycle-slideshow gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
$gallery_div .= '<div class="carousel-inner">';
$indicator = null;
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
      $attr['link'] = 'file';
	$i = 0;

	foreach ( $attachments as $id => $attachment ) {

	    $active = null;
	    if($i == 0){
		$active = 'active';
	    }

		//$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

//$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

$link = wp_get_attachment_image_src($id, $size);

		$output .= "<{$itemtag} class='item {$active} slide-thumbnail'>";
		$output .= "<img src='".$link[0]."' alt='' class=\"img-responsive\">";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='carousel-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
		$indicator .= '<li data-target="#'.$selector.'" data-slide-to="'.$i.'" class="'.$active.'"></li>';
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '';



	}

	$output .= "
		</div>";

$output .= '<a class="left carousel-control" href="#'.$selector.'" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#'.$selector.'" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>';

$output .= '<ol class="carousel-indicators">
    '.$indicator.'

  </ol>';

$output .= "</div> \n ";
	return $output;
}


}