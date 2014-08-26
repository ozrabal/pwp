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
	    <a id="prev'.$this->selector.'" class="cycle-prev right carousel-control"><span class="icon "></span></a>
	    <a id="next'.$this->selector.'" class="cycle-next left carousel-control"> <span class="icon "></span></a>';





	$output = '<div class="slideshow-loader"></div>'
                . '<div id="slideshow-1"  class="offer-slideshow slideshow-outer">'
                .'<div class="slideshow-main carousel slide">'
                . '<div id="'.$this->selector.'" class="carousel-inner offer-slideshow slideshow cycle-slideshow '.$this->params['wrapper_class'].'"
		data-cycle-slides="> div"
            zdata-cycle-pause-on-hover=true
            data-cycle-fx="scrollHorz"
            data-cycle-swipe=true
            data-cycle-speed=1000
            data-cycle-timeout=0
                     data-cycle-caption="> .caption"
                    data-cycle-caption-template="{{html}}"
            data-cycle-prev=".slideshow-main .cycle-prev"
            data-cycle-next=".slideshow-main .cycle-next"
            
            zdata-cycle-loader="wait"
            zdata-cycle-auto-init=false
            zdata-cycle-delay="3000"
            data-cycle-auto-height="calc"
        
>';
	$i = 0;
	//$pager = '<div id="slide-navi'.$this->selector.'" class="gallery-slide-navi thumbnail-pager">';
$carousel_items = '';
$caption = null;
$visible = 3;
$att_count = count($attachments);
if($att_count <= 3){
    $visible = $att_count;
}


	foreach ( $attachments as $id => $attachment ) {
		$img = wp_get_attachment_image_src($id, $size);
		$link = '<img src="'. $img[0] .'" alt="" class="'.$this->params['image_class'].'"/>';
		
		$img_thumbnail = wp_get_attachment_image_src($id, 'gallery-thumbnail');
		$link_thumbnail = '<img src="'. $img_thumbnail[0] .'" height="85" alt=""/>';

		//$pager .= '<img src="'. $img[0] .'" alt="" />';

		if ( trim($attachment->post_excerpt) ) {
			$caption .= '<div class="caption">
			' . $attachment->post_excerpt . '
			</div>';
		}

		$output .= '<div class="col-xs-12 slide">'.$caption.$link.'</div>';

		$carousel_items .= '<div class="slideshow-thumbnail scol-md-2">'.$link_thumbnail.'</div>';
		
		
		
		 
	}
	//$pager .= "</div>";
	
	$output .= '</div> ';
	//$output .= $pager;
	//$output .= '<div id="slide-navi'.$this->selector.'" class="gallery-slide-navi"></div>';
	$output .= $pager;
	$output .= '</div></div> '
                . '<div class="container-fluid">
<div class="row">

<div class="col-md-1d2 gallery-navi clearfix">';
		
/*                . '<ul class="nav nav-pills">
<li class="active"><a href="media.html">Galeria</a></li>
<li><a href="filmy.html">Filmy</a></li>
<!--<li><a href="">Inne</a></li>-->


</ul>';

*/

$output .= apply_filters('gallery_menu','','');

	$output .= '<div class="col-sm-2  col-md-3  col-lg-5">



<h1 class="gallery-title">Galeria</h1>
</div>';
	$output .= '<div id="slideshow-2" class="hidden-xs col-xs-12 col-sm-10 col-md-9 col-lg-7">';
	$output .= '<div class="container-fluid">
            <div class="row">
            <div class="col-xs-2 col-sm-1 col-md-1 col-lg-1">
 <a href="#" class=" carousel-prev"></a>
</div>';
	//$output .= '<div class="xcol-sm-10 thumbnails">';
$output .= ' <div id="pager" class="col-xs-8  col-sm-10 col-md-10  col-lg-10  offer-scarousel offer-slideshow cycle-slideshow thumbnail-list "
        data-cycle-slides="> div"
                    data-cycle-timeout="0"

                    data-cycle-prev="#slideshow-2 .carousel-prev"
                    data-cycle-next="#slideshow-2 .carousel-next"
                    data-cycle-caption="#slideshow-2 .custom-caption"
                    data-cycle-caption-template="Slide {{slideNum}} of {{slideCount}}"
                    data-cycle-fx="carousel"
                     
                    data-cycle-carousel-visible="3"
                    data-cycle-carousel-fluid=true
                    data-cycle-allow-wrap="false"
      
	 
        >';



	$output .= $carousel_items;


	$output .= '</div>';
        $output .= ' <div class="col-xs-2 col-sm-1 col-md-1 col-lg-1">
  <a href="#" class=" carousel-next"></a>
</div>';
	$output .= '</div>';
	
	$output .= '</div>';
	$output .= '</div>';
$output .= '</div>';
	
	$output .= '</div>';
	$output .= '</div>';

	return $output;
    }
}