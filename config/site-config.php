<?php

//add_action('the_content','dump_var');

function dump_var($content){
$t = get_option( 'taxonomy_17' );
 //dump($t);
 

$c = get_option('tab_options');
//dump($c);


$a = get_post_meta(get_the_ID(),'subtitle',true);

//dump($a);
$b = get_post_meta(get_the_ID(),'video',true);

//dump($b);

    
    
    
    
    //dump(get_post_meta($GLOBALS['post']->ID,'video',true));

    return $content;
}


add_action('the_content','get_form');
function get_form($content){
    form('kontaktz');
    return $content;
}


//add_action('the_content','get_map');

function get_map($content){


    $a = get_post_meta(get_the_ID(),'latlong',true);
$a = explode(',', $a);
dump($a);

?>
<style>

    #map {
  height: 300px;
  border: 1px solid #000;
}

</style>
<div id="map"></div>

<script>
window.onload = function() {

    var latlng = new google.maps.LatLng(<?php echo trim($a[0]) ?>, <?php echo trim($a[1]) ?>);
    var map = new google.maps.Map(document.getElementById('map'), {
        center: latlng,
        zoom: <?php echo trim($a[2]) ?>,
        mapTypeId: google.maps.MapTypeId.<?php echo strtoupper(trim($a[3])); ?>
    });
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Set lat/lon values for this property',
        draggable: false
    });
    
};
</script>

<?php

wp_enqueue_script( 'maps', 'http://maps.google.com/maps/api/js?sensor=false' );
	

    

    return $content;
}




function pwp_theme_setup() {
    load_theme_textdomain( 'pwp', get_template_directory() . '/languages' );
    register_nav_menu( 'primary', __( 'Primary Menu', 'pwp' ) );
    add_theme_support( 'post-thumbnails' );
    //add_image_size( 'gallery-thumbnail', 140, 85, array( 'center', 'center' ) );
    //add_image_size( 'work-large', 1000, 1000,false);
     //add_image_size( 'work-medium', 600, 600, false );
    //add_image_size( 'image-square', 400, 400, array( 'center', 'center' ) );
    //add_image_size( 'image-wide', 1170, 372, true);
}
add_action( 'after_setup_theme', 'pwp_theme_setup' );
add_filter( 'show_admin_bar', '__return_false');

function pwp_remove_recent_comments_style() {  
        global $wp_widget_factory;  
        remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );  
    }  
add_action( 'widgets_init', 'pwp_remove_recent_comments_style' );
//rss tylko z newsami
add_filter( 'pre_get_posts', 'pwp_feed_category' );
function pwp_feed_category( $query ) {
    if( $query->is_feed ) {
	$query->set( 'cat', '1' );
    }
    return $query;
}

//przekierowanie do startowej jesli puste wyszukiwanie
add_action( 'pre_get_posts', 'pwp_blank_search' );
function pwp_blank_search( $query ) {
    if( isset( $_GET['s'] ) && $_GET['s'] == '' ) {
	wp_redirect( home_url() );
	exit;
    }
}

//$_SESSION = null;
//dump($_SESSION);
//unset($_SESSION);

//common admin template
$admin_template = new Admin(
    array(
        'theme_css' => 'pwp-style.css',
        'remove_menu' => array(
            'link-manager.php',
            'tools.php',
            'edit-comments.php',
	    //'upload.php',
	    'edit.php?post_type=form',
	    'profile.php',
	    'customize.php'
        ),
        'remove_admin_bar' => array(
            'wp-logo',
            'updates',
            'edit',
            'new-content',
            'comments'
        ),
    )
);
$admin_template->add_theme_style();
//$admin_template->remove_menu_items();
$admin_template->remove_bar_links();

//site config
$site = new Site(
    array(
        'remove_head_element' => array(
	    'rsd_link',
	    'feed_links',
	    'feed_links_extra',
	    'wlwmanifest_link',
	    'wp_generator',
	    'start_post_rel_link',
	    'index_rel_link',
	    'adjacent_posts_rel_link',
            'adjacent_posts_rel_link_wp_head'
         )
    )
);
$site->remove_head_element();

//typy tresci
function register_post_types(){
        //rejestracja typu postu 'work'
         $slide_labels = array(
            'name'               => __( 'Discography', 'pwp' ),
            'singular_name'      => __( 'Discography', 'pwp' ),
            'add_new'            => __( 'Add New', 'pwp' ),
            'add_new_item'       => __( 'Add New work', 'pwp' ),
            'edit_item'          => __( 'Edit slide', 'pwp' ),
            'new_item'           => __( 'New slide', 'pwp' ),
            'all_items'          => __( 'All slides', 'pwp' ),
            'view_item'          => __( 'View slide', 'pwp' ),
            'search_items'       => __( 'Search slides', 'pwp' ),
            'not_found'          => __( 'No slides found', 'pwp' ),
            'not_found_in_trash' => __( 'No slides found in Trash', 'pwp' ),
            'parent_item_colon'  => __( ':', 'pwp' ),
            'menu_name'          => __( 'Discography', 'pwp' )
        );

        $slide_args = array(
            'labels'             => $slide_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
	    //'taxonomies'	 => array('post_tag'),
            'rewrite'            => array( 'slug' => 'discography' ),
            'capability_type'    => 'page',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
	    'supports'           => array( 'title','thumbnail','editor','page-attributes' )
        );
        register_post_type( 'discography', $slide_args );

        $file_labels = array(
            'name'               => __( 'Review', 'pwp' ),
            'singular_name'      => __( 'Review', 'pwp' ),
            'add_new'            => __( 'Add New', 'pwp' ),
            'add_new_item'       => __( 'Add New file', 'pwp' ),
            'edit_item'          => __( 'Edit file', 'pwp' ),
            'new_item'           => __( 'New file', 'pwp' ),
            'all_items'          => __( 'All files', 'pwp' ),
            'view_item'          => __( 'View file', 'pwp' ),
            'search_items'       => __( 'Search files', 'pwp' ),
            'not_found'          => __( 'No files found', 'pwp' ),
            'not_found_in_trash' => __( 'No files found in Trash', 'pwp' ),
            'parent_item_colon'  => __( ':', 'pwp' ),
            'menu_name'          => __( 'Reviews', 'pwp' )
        );

        $file_args = array(
            'labels'             => $file_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
	    //'taxonomies'	 => false,
            'rewrite'            => array( 'slug' => 'review' ),
            'capability_type'    => 'page',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
	    'supports'           => array( 'title','thumbnail','editor','page-attributes' )
        );
        register_post_type( 'review', $file_args );


	$file_labels = array(
            'name'               => __( 'Event', 'pwp' ),
            'singular_name'      => __( 'Event', 'pwp' ),
            'add_new'            => __( 'Add New', 'pwp' ),
            'add_new_item'       => __( 'Add New file', 'pwp' ),
            'edit_item'          => __( 'Edit file', 'pwp' ),
            'new_item'           => __( 'New file', 'pwp' ),
            'all_items'          => __( 'All files', 'pwp' ),
            'view_item'          => __( 'View file', 'pwp' ),
            'search_items'       => __( 'Search files', 'pwp' ),
            'not_found'          => __( 'No files found', 'pwp' ),
            'not_found_in_trash' => __( 'No files found in Trash', 'pwp' ),
            'parent_item_colon'  => __( ':', 'pwp' ),
            'menu_name'          => __( 'Events', 'pwp' )
        );

        $file_args = array(
            'labels'             => $file_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
	    //'taxonomies'	 => false,
            'rewrite'            => array( 'slug' => 'event' ),
            'capability_type'    => 'page',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
	    'supports'           => array( 'title','thumbnail','excerpt','editor','page-attributes' )
        );
        register_post_type( 'event', $file_args );
        
}
add_action( 'init', 'register_post_types' );



////widgets initialization
//$widgets = new Widgets(
//    array(
//        'unregister_widget' => array(
//            'WP_Widget_Pages',
//	    'WP_Widget_Archives',
//            'WP_Widget_Calendar',
//	    'WP_Widget_Links',
//            'WP_Widget_Meta',
//            'WP_Widget_Search',
//            'WP_Widget_Categories',
//            'WP_Widget_Recent_Posts',
//            'WP_Widget_Recent_Comments',
//            'WP_Widget_RSS',
//            'WP_Widget_Tag_Cloud',
//            
//            'WP_Nav_Menu_Widget'
//        ),
//        'register_widget' => array(
//            'Button_Widget',
//	    'Featuredarticles_Widget',
//	    //'Calendar_Widget',
//	    //'Contentblock_Widget',
//	    'Newsletter_Widget',
//	   // 'Bannersingle_Widget',
//           // 'Bannermulti_Widget',
//	    'Latestarticles_Widget'
//        )
//    )
//);
//$widgets->remove_widgets();
//$widgets->register_widgets();

$prefix = 'pwp_';
$event_meta_config = array(
    'id'             => 'event_page_fields',
    'title'          => __( 'Event parameters' ,'pwp' ),
    'pages'          => array( 'event' ),
    'context'        => 'advanced',
    'priority'       => 'high',
    'fields'         => array(),
    'local_images'   => false,
    'use_with_theme' => false
);
$event_meta_fields =  new AT_Meta_Box( $event_meta_config );
//$event_meta_fields->addTextarea( $prefix . 'event_subtitle', array( 'name' => __( 'Sub title', 'pwp' ), 'style' => 'height:80px', 'desc' => 'Podtytuł wydarzenia (niebieski tekst wyświetlany w opisie wydarzenia)') );
//$event_meta_fields->addTextarea( $prefix . 'event_band', array( 'name' => __( 'Band', 'pwp' ), 'style' => 'height:80px', 'desc' => 'Skład zespołu') );

$event_meta_fields->addDate( $prefix . 'event_start', array( 'name' => __( 'Start date', 'pwp' ), 'format' => 'yy-mm-dd' , 'desc' => 'Data rozpoczęcia wydarzenia (format: RRRR-MM-DD)') );
$event_meta_fields->addTime( $prefix . 'event_time', array( 'name'=> __( 'Start time' , 'pwp' ), 'format' => 'HH:mm', 'desc' => 'Godzina rozpoczęcia (format: GG:MM)' ) );
$event_meta_fields->addDate( $prefix . 'event_end', array( 'name'=> __( 'End date', 'pwp' ), 'format' => 'yy-mm-dd', 'desc' => 'Data zakończenia wydarzenia, w przypadku wydarzeń jednodniowych wystarczy pozostawić pole puste, wypełni się automatycznie (format: RRRR-MM-DD)' ) );
//$event_meta_fields->addTextarea( $prefix . 'event_price', array( 'name'=> __( 'Price', 'pwp' ),'style' => 'height:80px', 'desc' => 'Informacja o cenie biletów' ) );
//$event_meta_fields->addText( $prefix . 'event_ticket', array( 'name' => __( 'Ticket info', 'pwp' ), 'desc' => 'Informacja o spzedaży biletów (do nabycia:...) w przypadku eBilet nie trzeba wpisywać nazwy "eBilet", wystarczy podać link w polu poniżej' ) );
//$event_meta_fields->addText( $prefix . 'event_ebilet', array( 'name'=> __( 'eBilet', 'pwp' ), 'desc' => 'Link do serwisu eBilet' ) );
//$event_meta_fields->addCheckbox( $prefix . 'event_reservation_form', array( 'name'=> __( 'Hide Reservation form', 'pwp' ) ,'desc' => 'Czy przy wydarzeniu NIE wyświetlać formularza rezerwacji biletów?') );
$event_meta_fields->Finish();

//jesli nie ustawiono data zakonczenia eventu = data rozpoczecia
add_action( 'save_post', 'validate_event_end' );
function validate_event_end() {
    global $post_id;
    if( $_POST['post_type'] = 'event' ) {
	if( isset( $_POST['pwp_event_start'] ) && ( $_POST['pwp_event_end'] == '' || !isset( $_POST['pwp_event_end'] ) ) ) {
	    $_POST['pwp_event_end'] = $_POST['pwp_event_start'];
	    update_post_meta( $post_id, 'pwp_event_end', $_POST['pwp_event_start'] );
	}
    }
}



//metabox pola dodatkowe w dyskografii
$discography_meta = array(
    //'name'      => 'category',
    'title'     => __( 'Dodatkowe', 'pwp' ),
    //'tax' => 'post_tag',
    'elements'  => array(
    array(
        'type'  => 'date',
        'name'  => 'release_date',
        'params'    => array(
            'label'     => __( 'Data wydania płyty', 'pwp' ),
            //'validator' => array('notempty'),
            'comment' => 'opis'
         
        )
    ),
	array(
        'type'  => 'image',
        'name'  => 'header',
        'params'    => array(
            'label'     => __( 'Obrazek w nagłówku strony', 'pwp' ),
             'comment' => 'opis opis'
         
        )
    ),
    )
);
$d = new Taxmeta( $discography_meta );
//$d->render();
//dump($d);



//metabox pola dodatkowe w dyskografii
$page_meta = array(
    'name'      => 'page_meta',
    'title'     => __( 'Ustawienia wyświetlania', 'pwp' ),
    'post_type' => array('page'),
    //'allow_posts' => array('rule' => 'id','params'=> array(63)),
    'elements'  => array(

array(
        'type'  => 'map',
        'name'  => 'latlong',
        'params'    => array(
            'label'     => __( 'Location', 'pwp' ),
            'comment' => 'Lokalizacja'


        ),

    ),
     array(
        'type'  => 'date',
        'name'  => 'event_start',
        'params'    => array(
            'label'     => __( 'Start', 'pwp' ),
            'comment' => 'bla',
'validator' => array('notempty'),

        ),

    ),


        array(
        'type'  => 'date',
        'name'  => 'event_stop',
        'params'    => array(
            'label'     => __( 'Stop', 'pwp' ),
            'comment' => 'bla',
'validator' => array('notempty'),

        ),
            
    ),
    array(
        'type'  => 'checkbox',
        'name'  => 'expanded',
        'params'    => array(
            'label'     => __( 'Pokazuj zwinięte', 'pwp' ),
	    'comment' => 'Domyślnie panele są rozwinięte i niezwijalne',

//'validator' => array('notempty'),
        )
    ),
    )
);
new Metabox( $page_meta );

//metabox obrazek dodatkowy w brandach (logo na czarnym)
$video_gallery = array(
    'name'      => 'gallery',
    'title'     => __( 'Galeria video', 'pwp' ),
    'posts' =>array(67),
    'post_type' => array('page'),
    //'allow_posts' => array('rule' => 'id','params'=> array(63,67)),
    'elements'  => array(

	array(
	    'type'=>'repeatable',
	    'name'=>'video',

'params' => array(
		'title' => 'Film',

	    'options' => array(
		array(
	    'type'	=> 'text',
	    'name'	=> 'url',
	    'params'	=> array(
	        'label'	    => __( 'URL filmu', 'pwp' ),
	        'comment'   => __( 'Plik załącznika tylko w jpg', 'pwp' ),
	        'class'	    => 'large-text',

            )
	),
		array(

		  'type' => 'textarea',
		'name' => 'caption',
		'params'=> array(
                    'label' => __( 'Opis', 'pwp' ),
                    'class' => 'large-text',
                ),

		),
                array(
	    'type'	=> 'image',
	    'name'	=> 'thumbnail',
	    'params'	=> array(
	        'label'	    => __( 'miniaturka filmu', 'pwp' ),
	        'comment'   => __( 'Plik załącznika tylko w jpg', 'pwp' ),
	        'class'	    => 'large-text',

            )
	),

		)),

	),
             array(
        'type'  => 'date',
        'name'  => 'event_starta',
        'params'    => array(
            'label'     => __( 'Start', 'pwp' ),
            'comment' => 'bla',
'validator' => array('notempty'),

        ),

    ),
    )
);
new Metabox( $video_gallery );






//Gets post cat slug and looks for single-[cat slug].php and applies it
add_filter('single_template', create_function(
	'$the_template',
	'foreach( (array) get_the_category() as $cat ) {
		if ( file_exists(TEMPLATEPATH . "/single-{$cat->slug}.php") )
		return TEMPLATEPATH . "/single-{$cat->slug}.php"; }
	return $the_template;' )
);

//szablony dla single custom post type
add_filter('single_template','pwp_cpt_page_template');
function pwp_cpt_page_template($the_template){
    global $post;
    if(file_exists(TEMPLATEPATH . '/single-'.$post->post_type.'.php')){
	return TEMPLATEPATH . '/single-'.$post->post_type.'.php';
    }
    return $the_template;
}

//remove_role('logged_in');
/*
add_role(
    'logged_in',
    __( 'Zalogowany Architekt', 'pwp' ),
    array(
        'read'         => true,  // true allows this capability

    )
);
*/

//ustawia current w menu
function nav_parent_class($classes, $item) {
    $cpt_name = 'resource';
    $parent_slug = 'dla-architekta';

    if ($cpt_name == get_post_type() && !is_admin()) {
        global $wpdb;

        // get page info (we really just want the post_name so it can be compared to the post type slug)
        $page = get_page_by_title($item->title, OBJECT, 'page');
    if( !empty($page->post_name)){
        // check if slug matches post_name
        if( $page->post_name == $parent_slug ) {
            $classes[] = 'current-menu-ancestor';
        }

    }
    }
    return $classes;
}

add_filter('nav_menu_css_class', 'nav_parent_class', 1, 2);







//add_filter('upload_dir', 'cgg_upload_dir');
function cgg_upload_dir($dir)
{
    // xxx Lots of $_REQUEST usage in here, not a great idea.

    // Are we where we want to be?
    if (!isset($_REQUEST['action']) || 'upload-attachment' !== $_REQUEST['action']) {
        return $dir;
    }

    // make sure we have a post ID
    if (!isset($_REQUEST['post_id'])) {
        return $dir;
    }

    // modify the path and url.
    $type = get_post_type($_REQUEST['post_id']);
    $uploads = apply_filters("{$type}_upload_directory", $type);
    $dir['path'] = path_join($dir['basedir'], $uploads);
    $dir['url'] = path_join($dir['baseurl'], $uploads);

    return $dir;
}

//add_filter('posts_where', 'limitMediaLibraryItems_56456', 10, 2 );
function limitMediaLibraryItems_56456($where) {
    global  $wpdb;

    // Do not modify $where for non-media library requests
    //if ($pagenow !== 'post.php') {
        //return $where;
    //}

    
    //dump($_POST);
    $b = get_post($_POST['post_id']);
    //dump($b->post_type);
    $predefined = get_post_types(array('_builtin' => true));
     $custom = get_post_types(array('_builtin' => false));
    //dump($predefined);
    if(  !in_array($b->post_type, $predefined )){
    
             $where .= " AND {$wpdb->posts}.guid LIKE '%{$b->post_type}%'"; 
    }else{
        
        
        //$where .= " AND {$wpdb->posts}.guid ";
        foreach($custom as $type){
            $where .= "AND {$wpdb->posts}.guid NOT LIKE '%{$type}%' "; 
        }
        
       
    }
    
    
  

    return $where;
}

//add_filter( 'ajax_query_attachments_args', 'alter', 1, 1 );

function alter($q){
    
    //dump($q);
    
    
    
}


function kli_ezg_mediapopup_exclude($a) {
        
    //dump($_POST);
    //$b = get_post($_POST['post_id']);
    //dump($b->post_type);
    //$predefined = get_post_types(array('_builtin' => true));
    //dump($predefined);
    //if(  !in_array($b->post_type, $predefined )){
    
            add_filter( 'posts_where', 'limitMediaLibraryItems_56456', 1 );
    //}
    }
    //add_action( 'wp_ajax_query-attachments', 'kli_ezg_mediapopup_exclude', 1 );


//domyslny poziom dostepu do zasowbow dla architektow

 //add_action('save_post', 'save_my_metadata');
 function save_my_metadata($ID = false, $post = false)
{
    if($post->post_type != 'resource')
        return;
    update_post_meta($ID, 'my_metadata', 'logged_in');
}

//dodajemy style selektor do tinymce
//add_filter( 'mce_buttons_2', 'pwp_tinymce_buttons' );
function pwp_tinymce_buttons( $buttons ) {
    array_unshift( $buttons, 'styleselect' );
    return $buttons;
}

//Add styles/classes to the "Styles" drop-down
//add_filter( 'tiny_mce_before_init', 'pwp_custom_tinymce_styles' );
function pwp_custom_tinymce_styles( $settings ) {
    $style_formats = array(
        array(
            'title'     => __( 'Bigger text', 'pwp' ),
            'selector'  => 'p',
            'classes'   => 'bigger-text',
        ),
    );
    $settings['style_formats'] = json_encode( $style_formats );
    return $settings;
}

//pobiera i wyswietla liste plikow
function pwp_file_list($directory = null,$filter){
    //dump($directory);
    $directory = '_dla-architekta/'.$directory;
    $known_extensions = array('pdf', 'zip', 'rar');
    if(is_dir($directory)){

    echo '<ul class="file-list">';
	foreach(array_diff(scandir($directory),array('.','..','.DS_Store')) as $file){
		if(is_file($directory.'/'.$file)&&(($filter)?ereg($filter.'$',$file):1)){
			//echo"\t\t\t\t\t\t\t\t\t";


		$extension = substr($file, strrpos($file, '.')+1);
		if(!in_array( $extension,$known_extensions)){
		    $extension = 'file';
		}

			echo'<li class="'.$extension.'"><a  href="'.  site_url().'/'.$directory.'/'.$file.'" rel="nofollow">'.$file.'</a></li>';
			//echo"\r\n";
		}
	}
	echo '</ul>';

    }
}




unset($admin);
unset($options);
/*opcje*/
//$adminss = new Administrator();
////$adminss = Administrator::init();
//
//$page = array(
//    //'parent_slug' => 'edit.php?post_type=form',
//    'page_title'    => __( 'Test settings page', 'pwp' ),
//    'menu_title'    => __( 'Test settings', 'pwp' ),
//    'capability'    => 'manage_options',
//    'menu_slug'	    => 'test-optionss',
//    'icon'	    => '',
//    'position'	    => null,
//);
//$adminss->add_page( $page );
//$adminss->add_tab( 'Nowy tab', 'test-optionss' );
//
//$options = new Options();
//
//$options->set_name( 'a_options' )
//        ->set_action( 'options.php' )
//        ->set_title( __( 'Pierwsze opcje', 'pwp' ) );
//
//$options->add_element( 'text', 'tekst' )
//        ->set_label( __( 'User email templatec', 'pwp' ) )
//        ->set_class( 'klasa' )
//        ->set_validator( array( 'notempty' ) );
//
//$adminss->add_options( $options, 'test-optionss' );
//$adminss->add_options( $options, 'nowy-tab' );
//$adminss->add_tab( 'Inny tab', 'test-optionss' );
//$options_tabs = new Options();
//        $options_tabs->set_name( 'tab_options' )
//                ->set_action( 'options.php' )
//                ->set_title( __( 'opcje w tabie', 'pwp' ) );
//
//        $options_tabs->add_element( 'text', 'tekstt' )
//                    ->set_label( __( 'pole w tab', 'pwp' ) )
//                    ->set_class( 'klasa' )
//                    ->set_validator( array( 'notempty' ) );
//
//	$options_tabs->add_element( 'attachment', 'zalacznik' )
//                    ->set_label( __( 'Załacznik', 'pwp' ) )
//                    ->set_class( 'klasa' )
//                    ->set_validator( array( 'notempty' ) );
//
//        $options_tabs->add_element( 'image', 'obrazek' )
//                    ->set_label( 'Obrazek' )
//                    ->set_comment( 'komentarz' )
//                    ->set_validator( array( 'notempty' ) );
//
//
//	$elements_repeater = array(
//            array(
//                'type' => 'text',
//		'name' => 'user_email_templates',
//		'params'=> array(
//                    'label' => __( 'User email templatex', 'pwp' ),
//                    'class' => 'large-text',
//                ),
//            ),
//	    array(
//		'type' => 'text',
//		'name' => 'zalacznik',
//		'params'=> array(
//                    'label' => __( 'Ue', 'pwp' ),
//                    'class' => 'large-text',
//                ),
//	    )
//        );
//
//        $options_tabs->add_element( 'repeatable', 'powtorz' )
//                    ->set_title( 'Powtarzalne' )
//                    ->set_comment( 'komentarz do repeatable' )
//                    ->add_elements( $elements_repeater );
//        $adminss->add_options( $options_tabs, 'inny-tab' );








/**********/

if (!class_exists('WP_EX_PAGE_ON_THE_FLY')){
    /**
    * WP_EX_PAGE_ON_THE_FLY
    * @author Ohad Raz
    * @since 0.1
    * Class to create pages "On the FLY"
    * Usage:
    *   $args = array(
    *       'slug' => 'fake_slug',
    *       'post_title' => 'Fake Page Title',
    *       'post content' => 'This is the fake page content'
    *   );
    *   new WP_EX_PAGE_ON_THE_FLY($args);
    */
    class WP_EX_PAGE_ON_THE_FLY
    {

        public $slug ='';
        public $args = array();
        /**
         * __construct
         * <a href="/param">@param</a> array $arg post to create on the fly
         * @author Ohad Raz
         *
         */
        function __construct($arg){
            add_filter('the_posts',array($this,'fly_page'));
            $this->args = $args;
            $this->slug = $args['slug'];
        }

        /**
         * fly_page
         * the Money function that catches the request and returns the page as if it was retrieved from the database
         * <a href="/param">@param</a>  array $posts
         * @return array
         * @author Ohad Raz
         */
        public function fly_page($posts){
            global $wp,$wp_query;
            $page_slug = $this->slug;

            //check if user is requesting our fake page
            if(count($posts) == 0 && (strtolower($wp->request) == $page_slug || $wp->query_vars['page_id'] == $page_slug)){

                //create a fake post
                $post = new stdClass;
                $post->post_author = 1;
                $post->post_name = $page_slug;
                $post->guid = get_bloginfo('wpurl' . '/' . $page_slug);
                $post->post_title = 'page title';
                //put your custom content here
                $post->post_content = "Fake Content";
                //just needs to be a number - negatives are fine
                $post->ID = -42;
                $post->post_status = 'static';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->comment_count = 0;
                //dates may need to be overwritten if you have a "recent posts" widget or similar - set to whatever you want
                $post->post_date = current_time('mysql');
                $post->post_date_gmt = current_time('mysql',1);

                $post = (object) array_merge((array) $post, (array) $this->args);
                $posts = NULL;
                $posts[] = $post;

                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->is_home = false;
                $wp_query->is_archive = false;
                $wp_query->is_category = false;
                unset($wp_query->query["error"]);
                $wp_query->query_vars["error"]="";
                $wp_query->is_404 = false;
            }

            return $posts;
        }
    }//end class
}//end if


//dump($wp);
//
//$args = array(
//        'slug' => 'fake_slug',
//        'post_title' => 'Fake Page Title',
//        'post content' => 'This is the fake page content'
//);
//new WP_EX_PAGE_ON_THE_FLY($args);





/********
 *
 *
 *
 *
 *
 */


/*
 * Virtual Themed Page class
 *
 * This class implements virtual pages for a plugin.
 *
 * It is designed to be included then called for each part of the plugin
 * that wants virtual pages.
 *
 * It supports multiple virtual pages and content generation functions.
 * The content functions are only called if a page matches.
 *
 * The class uses the theme templates and as far as I know is unique in that.
 * It also uses child theme templates ahead of main theme templates.
 *
 * Example code follows class.
 *
 * August 2013 Brian Coogan
 *
 */




// There are several virtual page classes, we want to avoid a clash!
//
//
class Virtual_Themed_Pages_BC
{
    public $title = '';
    public $body = '';
    private $vpages = array();  // the main array of virtual pages
    private $mypath = '';
    public $blankcomments = "blank-comments.php";


    function __construct($plugin_path = null, $blankcomments = null)
    {
	if (empty($plugin_path))
	    $plugin_path = dirname(__FILE__);
	$this->mypath = $plugin_path;

	if (! empty($blankcomments))
	    $this->blankcomments = $blankcomments;

	// Virtual pages are checked in the 'parse_request' filter.
	// This action starts everything off if we are a virtual page
	add_action('parse_request', array(&$this, 'vtp_parse_request'));
    }

    function add($virtual_regexp, $contentfunction)
    {
	$this->vpages[$virtual_regexp] = $contentfunction;
    }


    // Check page requests for Virtual pages
    // If we have one, call the appropriate content generation function
    //
    function vtp_parse_request(&$wp)
    {
	//global $wp;
//dump($wp);
//dump($this->vpages);
	//if (empty($wp->query_vars['pagename']))
	    //return; // page isn't permalink

	//$p = $wp->query_vars['pagename'];
	$p = $_SERVER['REQUEST_URI'];

	$matched = 0;
	foreach ($this->vpages as $regexp => $func)
	{
	    if (preg_match($regexp, $p))
	    {
		$matched = 1;
		break;
	    }
	}
	

	// Do nothing if not matched
	if (! $matched)
	    return;

	// setup hooks and filters to generate virtual movie page
	add_action('template_redirect', array(&$this, 'template_redir'));
	add_filter('the_posts', array(&$this, 'vtp_createdummypost'));

	// we also force comments removal; a comments box at the footer of
	// a page is rather meaningless.
	// This requires the blank_comments.php file be provided
	add_filter('comments_template', array(&$this, 'disable_comments'), 11);

	// Call user content generation function
	// Called last so it can remove any filters it doesn't like
	// It should set:
	//    $this->body   -- body of the virtual page
	//    $this->title  -- title of the virtual page
	//    $this->template  -- optional theme-provided template
	//          eg: page
	//    $this->subtemplate -- optional subtemplate (eg movie)
	// Doco is unclear whether call by reference works for call_user_func()
	// so using call_user_func_array() instead, where it's mentioned.
	// See end of file for example code.
	$this->template = $this->subtemplate = null;
	$this->title = null;
	unset($this->body);
	call_user_func_array($func, array(&$this, $p));

	if (! isset($this->body)) //assert
	    wp_die("Virtual Themed Pages: must save ->body [VTP07]");

	return($wp);
    }


    // Setup a dummy post/page
    // From the WP view, a post == a page
    //
    function vtp_createdummypost($posts)
    {

	// have to create a dummy post as otherwise many templates
	// don't call the_content filter
	global $wp, $wp_query;

	//create a fake post intance
	$p = new stdClass;
	// fill $p with everything a page in the database would have
	$p->ID = -1;
	$p->post_author = 1;
	$p->post_date = current_time('mysql');
	$p->post_date_gmt =  current_time('mysql', $gmt = 1);
	$p->post_content = $this->body;
	$p->post_title = $this->title;
	$p->post_excerpt = '';
	$p->post_status = 'publish';
	$p->ping_status = 'closed';
	$p->post_password = '';
	$p->post_name = 'movie_details'; // slug
	$p->to_ping = '';
	$p->pinged = '';
	$p->modified = $p->post_date;
	$p->modified_gmt = $p->post_date_gmt;
	$p->post_content_filtered = '';
	$p->post_parent = 0;
	$p->guid = get_home_url('/' . $p->post_name); // use url instead?
	$p->menu_order = 0;
	$p->post_type = 'page';
	$p->post_mime_type = '';
	$p->comment_status = 'closed';
	$p->comment_count = 0;
	$p->filter = 'raw';
	$p->ancestors = array(); // 3.6

	// reset wp_query properties to simulate a found page
	$wp_query->is_page = TRUE;
	$wp_query->is_singular = TRUE;
	$wp_query->is_home = FALSE;
	$wp_query->is_archive = FALSE;
	$wp_query->is_category = FALSE;
	unset($wp_query->query['error']);
	$wp->query = array();
	$wp_query->query_vars['error'] = '';
	$wp_query->is_404 = FALSE;

	$wp_query->current_post = $p->ID;
	$wp_query->found_posts = 1;
	$wp_query->post_count = 1;
	$wp_query->comment_count = 0;
	// -1 for current_comment displays comment if not logged in!
	$wp_query->current_comment = null;
	$wp_query->is_singular = 1;

	$wp_query->post = $p;
	$wp_query->posts = array($p);
	$wp_query->queried_object = $p;
	$wp_query->queried_object_id = $p->ID;
	$wp_query->current_post = $p->ID;
	$wp_query->post_count = 1;

	return array($p);
    }


    // Virtual Movie page - tell wordpress we are using the given
    // template if it exists; otherwise we fall back to page.php.
    //
    // This func gets called before any output to browser
    // and exits at completion.
    //
    function template_redir()
    {
	//    $this->body   -- body of the virtual page
	//    $this->title  -- title of the virtual page
	//    $this->template  -- optional theme-provided template eg: 'page'
	//    $this->subtemplate -- optional subtemplate (eg movie)
	//

	if (! empty($this->template) && ! empty($this->subtemplate))
	{
	    // looks for in child first, then master:
	    //    template-subtemplate.php, template.php
	        //dump($this->template);
	    load_template($this->template);
	    get_template_part($this->template, $this->subtemplate);
	}
	elseif (! empty($this->template))
	{
	    // looks for in child, then master:
	    //    template.php
	    get_template_part($this->template);
	
	}
	elseif (! empty($this->subtemplate))
	{
	    // looks for in child, then master:
	    //    template.php
	    get_template_part($this->subtemplate);
	}
	else
	{
	    get_template_part('page');
	}

	// It would be possible to add a filter for the 'the_content' filter
	// to detect that the body had been correctly output, and then to
	// die if not -- this would help a lot with error diagnosis.

        exit;
    }


    // Some templates always include comments regardless, sigh.
    // This replaces the path of the original comments template with a
    // empty template file which returns nothing, thus eliminating
    // comments reliably.
    function disable_comments($file)
    {
	if (file_exists($this->blankcomments))
	   return($this->mypath.'/'.$blankcomments);
	return($file);
    }


} // class


// Example code - you'd use something very like this in a plugin
//
if (1)
{
    // require 'BC_Virtual_Themed_pages.php';
    // this code segment requires the WordPress environment

    $vp =  new Virtual_Themed_Pages_BC();
    $vp->add('#/mypattern/unique#i', 'mytest_contentfunc');

    // Example of content generating function
    // Must set $this->body even if empty string
    function mytest_contentfunc($v, $url)
    {

	//dump($url);
	// extract an id from the URL
	$id = 'none';
	if (preg_match('#unique/(\d+)#', $url, $m))
	    $id = $m[1];
	// could wp_die() if id not extracted successfully...

	$v->title = "My Virtual Page Title";
	$v->body = "Some body content for my virtual page test - id $id\n";
	$v->template = 'page'; // optional
	$v->subtemplate = 'billing'; // optional
    }


    $vp->add('#/uzytkownik#i', 'mytest_contentfunca');
$vp->add('#/user#i', 'mytest_contentfunca');
    // Example of content generating function
    // Must set $this->body even if empty string
    function mytest_contentfunca($v, $url)
    {

	//dump($url);
	// extract an id from the URL
	$id = 'none';
	if (preg_match('#unique/(\d+)#', $url, $m))
	    $id = $m[1];
	// could wp_die() if id not extracted successfully...

	$v->title = __( 'sUser profile page', 'pwp');
	$v->body = "Some body content for my virtual page test - id $id\n";
	$v->template = plugin_dir_path( __FILE__ ).'template.php'; // optional
	$v->subtemplate = 'billing'; // optional
    }

}




// end
