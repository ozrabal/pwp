<?php
/**
   * Formelement_Map class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

/*
//pobiera mape stron
add_action( 'the_content', 'get_pages_map' );
function get_pages_map( $content ) {
    $current_post_location = get_post_meta( get_the_ID(), 'latlong', true );
    if( !$current_post_location ){
	return $content;
    }
    $current_post_location = explode( ',', $current_post_location );
    $map_args  = array(
	'post_type'	=> 'page',
	'post_status'	=> 'publish',
	'meta_query' => array(
	    array(
		'key'	    => 'latlong',
		'compare'   => 'EXIST',
	    ),
	),
    );
    $map_query = new WP_Query( $map_args );
    if( $map_query->have_posts() ) {
	while ( $map_query->have_posts() ) {
	    $map_query->the_post();
	    $page_location = explode( ',', get_post_meta( get_the_ID(), 'latlong', true ) );
	    $page_location[] = '<div id="content"><h3><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>' . get_the_content() . '</div>';
	    $markers[] = $page_location;
	}
    }
    ?>
    <style>
	.entry-content img, .comment-content img, .widget img {
	    max-width: inherit;
	}
	#map {
	    height: 300px;
	    border: 1px solid #000;
	}
    </style>
    <script>
	var markers = <?php echo json_encode( $markers, JSON_HEX_QUOT | JSON_HEX_TAG ); ?>;
    </script>
    <div id="map"></div>
    <script>
	window.onload = function() {
	    var latlng = new google.maps.LatLng(<?php echo trim($current_post_location[0]) ?>, <?php echo trim($current_post_location[1]) ?>);
	    var map = new google.maps.Map(document.getElementById('map'), {
		center: latlng,
		zoom: <?php echo trim($current_post_location[2]) ?>,
		mapTypeId: google.maps.MapTypeId.<?php echo strtoupper(trim($current_post_location[3])); ?>
	    });
	    var infowindow;
	    for (var i = 0; i < markers.length; i++) {
		var marker = new google.maps.Marker({
		    position: new google.maps.LatLng(markers[i][0],markers[i][1] ),
		    map: map,
		    title: 'click to description',
		    info: markers[i][4],
		    icon: 'http://google.com/mapfiles/ms/micons/green-dot.png',
		});
		(function(marker, i) {
		    google.maps.event.addListener(marker, 'click', function() {
			if (infowindow) infowindow.close();
			infowindow = new google.maps.InfoWindow({
			    content: this.info
			});
			infowindow.open(map, marker);
		    });
		})(marker, i);
	    }
	}
    </script>
    <?php
    wp_enqueue_script( 'maps', 'http://maps.google.com/maps/api/js?sensor=false' );
    return $content;
}
 */
class Formelement_Map extends Formelement_Input {
    protected $type = 'map';
    
    /**
     * konstruktor
     * @param Form $form
     * @param string $name
     */
    public function __construct( $form, $name ) {
	
        add_action( 'admin_init', array( $this, 'admin_enqueue_scripts' ) );
	parent::__construct( $form, $name );
	$this->set_class( 'geodata' );
    }
    
    /**
     * dolaczenie skryptow
     */
    function admin_enqueue_scripts() {

	wp_enqueue_script( 'maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&sensor=false' );
	wp_enqueue_script( 'field-map', plugins_url( '/field-map.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
	wp_localize_script( 'field-map', 'geocode_notfound', __( 'No results were found for the search criteria', 'pwp' ) );
    }
    
    /**
     * renderuje pole
     * @return string
     */
    public function render(){
	
	parent::render();
	$type = 'hidden';
	if( WP_DEBUG ) {
	    $type = 'text';
	}
        return  $this->get_before() . $this->get_label() . ''
		. '<div id="field_' . $this->get_name() . '" class="map-field box">'
		. '<input onkeydown="if (event.keyCode == 13){ codeAddress(); return false;}" id="geocode_field_'.$this->get_name().'" class="controls" type="text" placeholder="' . __( 'Type location', 'pwp' ) . '">'
		. '<input type="button" class="code-address button button-small" value="' . __( 'Show on map', 'pwp' ) . '" >'
		. '<div id="map_field_' . $this->get_name() . '" class="map-box"></div>'
		. '<input ' . $this->id() . ' type="' . $type . '" ' . $this->name() . $this->value() . $this->cssclass() . '/>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after() . ''
		. '</div>';
	}
}