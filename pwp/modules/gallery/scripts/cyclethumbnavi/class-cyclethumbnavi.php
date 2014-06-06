<?php
/*
 * Gallery Name: Pokaz slajdów w tle
 * Description: Tworzy z obrazów w galerii pokaz slajdów z nawigacją miniaturkami
 */

class Cyclethumbnavi {


    private $params;
    private $defaults = array(
	'files' => array(
	    'scripts' => array( )
	),
	'settings' => array(
	    'wrapper_class' => '',
	    'item_class' => '',
	    'image_class' => 'img-responsive ',
	    'size' => 'slide-large',
	    'timeout' => 0
	)
    );
    function __construct( Gallery $gallery = null, Array $params = null ) {
	
	if($gallery instanceof Gallery){
	    $gallery->include_files( $this->defaults['files']['scripts'] );
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
	$pager = '
	    <a id="prev'.$this->selector.'" class="cycle-prev right carousel-control"> '.__('Previous', 'pwp').'</a>
	    <a id="next'.$this->selector.'" class="cycle-next left carousel-control"> '.__('Next','pwp').'</a>';





	$output = '<div class="slideshow-loader"></div><div id="slideshow-1"  class="offer-slideshow slideshow-outer"><div id="'.$this->selector.'" class="offer-slideshow cycle-slideshow row '.$this->params['wrapper_class'].'"
								data-cycle-slides="> div"
      data-cycle-pause-on-hover=true
      data-cycle-fx="fadeout"
          data-cycle-swipe=true
          data-cycle-speed=4000
          data-cycle-timeout=1000
        data-cycle-prev="#slideshow-1 .cycle-prev"
        data-cycle-next="#slideshow-1 .cycle-next"
        data-cycle-caption="#slideshow-1 .custom-caption"
        data-cycle-caption-template="Slide {{slideNum}} of {{slideCount}}"
	data-cycle-loader="wait"
	zdata-cycle-auto-init=false
	 data-cycle-delay="3000"
        
>';
	$i = 0;
	//$pager = '<div id="slide-navi'.$this->selector.'" class="gallery-slide-navi thumbnail-pager">';
$carousel_items = '';
$caption = null;
$visible = 5;
$att_count = count($attachments);
if($att_count <= 5){
    $visible = $att_count;
}


	foreach ( $attachments as $id => $attachment ) {
		$img = wp_get_attachment_image_src($id, $size);
		$link = '<img src="'. $img[0] .'" alt="" class="'.$this->params['image_class'].'"/>';
		
		$img_thumbnail = wp_get_attachment_image_src($id, 'thumbnail');
		$link_thumbnail = '<img src="'. $img_thumbnail[0] .'" alt=""/>';

		//$pager .= '<img src="'. $img[0] .'" alt="" />';

		if ( trim($attachment->post_excerpt) ) {
			$caption .= '<div class="caption"><h2>
			' . $attachment->post_excerpt . '
			</h2></div>';
		}

		$output .= '<div class="col-xs-12 slide">'.$caption.$link.'</div>';

		$carousel_items .= '<div class="slideshow-thumbnail">'.$link_thumbnail.'</div>';
		
		
		
		 
	}
	//$pager .= "</div>";
	
	$output .= '</div> ';
	//$output .= $pager;
	//$output .= '<div id="slide-navi'.$this->selector.'" class="gallery-slide-navi"></div>';
	$output .= $pager;
	$output .= '</div> ';


	$output .= '<div class="outer ">';
	$output .= '<div id="slideshow-2" class="offer-carousel clearfix hidden-xs">';
	$output .= ' <a href="#" class="cycle-prev">next &raquo;</a>';
	$output .= '<div class="xcol-sm-10 thumbnails">';
$output .= ' <div id="pager" class="offer-slideshow cycle-slideshow thumbnail-list "
        data-cycle-slides="> div"
        data-cycle-timeout="0"
        data-cycle-prev="#slideshow-2 .cycle-prev"
        data-cycle-next="#slideshow-2 .cycle-next"
        data-cycle-caption="#slideshow-2 .custom-caption"
        data-cycle-caption-template="Slide {{slideNum}} of {{slideCount}}"
        data-cycle-fx="carousel"
        data-cycle-carousel-visible="'.$visible.'"
        data-cycle-carousel-fluid=false
        data-cycle-allow-wrap="false"
      
	 
        >';



	$output .= $carousel_items;


	$output .= '</div>';
	$output .= '</div>';
	$output .= ' <a href="#" class="cycle-next">&laquo; prev</a> ';
	$output .= '</div>';
	$output .= '</div>';

	return $output;
    }
}