<?php

/*
 * klasa - wrapper galerii, uruchamia konkretny typ galerii
 * dziala w shortkodzie a takze samodzielnie
 *
 * pliki konkretnych typow galerii:
 * katalog - nazwa klasy i typu
 * w tym katalogu plik o nazwie jak wyzej z klasa obslugujaca galerie
 * uzycie:
 * w shortkodzie: add_shortcode('gallery', array($gallery,'setup_gallery'));
 * samodzielnie:
 *  $galerry = new Gallery(array('ids' => '305,40', 'size' => 'medium')); parametry takie jak w predefinioanym shortkodzie gallery
 *  $gallery->show(); drukuje html
 * @todo dodac mozliwosc okreslania sciezek do obrazkow a nie tylko wywolanie z id attachmentow
 *
 */

class Gallery {

    private $default_gallery = 'fancybox';
    public $output = '';

    public function __construct( Array $params = null ) {
	$this->gallery_types();
	if ( count( $this->gallery_types ) > 1 ) {
	    add_action( 'wp_enqueue_media', array( $this, 'wp_enqueue_media' ) );
	    add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
	}


	if( $params ) {
	    $this->setup_gallery( $params );
	}
    }

    private function gallery_types(){
	//$this->gallery_types = array_diff( scandir( plugin_dir_path( __FILE__ ) . 'scripts/' ), array('..','.') );
        
       $names = array_diff( scandir( plugin_dir_path( __FILE__ ) . 'scripts/' ), array('..','.','.DS_Store') );
       foreach($names as $name){
           if(  file_exists( plugin_dir_path( __FILE__ ) . 'scripts/'.$name.'/class-'.$name.'.php' )){
           $scripts[$name] = get_file_data(plugin_dir_path( __FILE__ ) . 'scripts/'.$name.'/class-'.$name.'.php',array(
		'name'        => 'Gallery name','description' => 'Description'));
           }
       }
        //dump($scripts);
        //get_file_data($file, $default_headers);
        //$this->gallery_types = array_diff( scandir( plugin_dir_path( __FILE__ ) . 'scripts/' ), array('..','.') );
        $this->gallery_types = $scripts;

    }

    private function include_gallery_class( $gallery_slug ) {
	if ( file_exists( plugin_dir_path( __FILE__ ) . 'scripts/' . strtolower( $gallery_slug ) . '/class-' . strtolower( $gallery_slug ). '.php' ) ) {
	    include_once plugin_dir_path( __FILE__ ) . 'scripts/' . strtolower( $gallery_slug ) . '/class-' . strtolower( $gallery_slug ) . '.php';
	}
    }

    public function show() {
	print $this->output;
    }

    public function setup_gallery(Array $params ){
	    //jesli niezdefiniowany typ wtedy fancybox jako domyslny
	    if ( !isset( $params['type'] ) || $params['type'] == 'default' ) {
		$params['type'] = $this->default_gallery;
		

	    }
	    $params['type'] = apply_filters('gallery_type', $params['type']);
	    $this->params = $params;
	    //include klasy wlasciwej galerii
	    $this->include_gallery_class( $this->params['type'] );
	    if( class_exists( $this->params['type'] ) ) {
		$gallery = new $this->params['type']( $this );
		return $this->output;
	    }
    }

    public function include_files( $plugins = array() ) {
	if ( isset( $plugins ) && count( $plugins ) > 0 ) {

	    foreach( $plugins as $plugin ) {

		wp_register_script( 'jquery.' . strtolower( $plugin ), plugin_dir_url( __FILE__ ) . 'scripts/' . strtolower( $this->params['type'] ) . '/' . strtolower( $plugin ), 'jquery', false, true );
		wp_enqueue_script( 'jquery.' . strtolower( $plugin ) );
	    }
	}
    }

    public function include_styles( $styles = array() ) {
	if ( isset( $styles ) && count( $styles ) > 0 ) {
	    foreach( $styles as $style ) {
		wp_register_style( $style, plugin_dir_url( __FILE__ ) . 'scripts/' . strtolower( $this->params['type'] ) . '/' . strtolower( $style ) );
		wp_enqueue_style( $style );
	    }
	}
    }

    function wp_enqueue_media() {
	if ( ! wp_script_is( 'pwp-gallery-settings', 'registered' ) )
	    wp_register_script( 'pwp-gallery-settings', plugin_dir_url( __FILE__ ).'/gallery-settings.js', array( 'media-views' ), uniqid() );
	    wp_enqueue_script( 'pwp-gallery-settings' );
    }

    function print_media_templates() {
	?>
	<script type="text/html" id="tmpl-pwp-gallery-settings">
	    
            <label class="setting">
		<span><?php _e( 'Gallery type', 'pwp' ); ?></span>
		    <select class="type" name="type" data-setting="type">
			<?php foreach ( $this->gallery_types as $k => $name ) : ?>
                        <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $k, $this->default_gallery ); ?>><?php if(!empty($name['name'])){ _e( $name['name'], 'pwp' );}else{ _e( $k, 'pwp' ); }; ?></option>
			<?php endforeach; ?>
		    </select>
	    </label>
            <?php foreach ( $this->gallery_types as $k => $name ) {
                if(!empty($name['description'])){
                ?>
            
    <p class="description gallery-description" id="<?php echo esc_attr( $k ); ?>" <?php if(selected( $k, $this->default_gallery,false )){ ?>style="display: none;"<?php } ?>><?php _e( $name['description'], 'pwp' ); ?></p>
			
                <?php }}?>
            
            </script>
        
	<?php
    }
}

$gallery = new Gallery();
add_shortcode( 'gallery', array( $gallery, 'setup_gallery' ) );



//tools

//extract galleries from post content, strip non-gallery content
function pwp_extract_gallery( WP_Post $post ) {
    $post_content = $post->post_content;
    $pattern = '\[(\[?)(gallery)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
    preg_match_all( "/$pattern/s", $post_content, $match );
    $post->post_content = '';
    foreach( $match[0] as $g ) {
	$post->post_content .= $g;
    }
    $content = apply_filters( 'the_content', $post->post_content );
    return $content;
}                                               