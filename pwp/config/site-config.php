<?php
function pwp_theme_setup() {
    load_theme_textdomain( 'pwp', get_template_directory() . '/languages' );
    register_nav_menu( 'primary', __( 'Primary Menu', 'pwp' ) );
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'work-large', 1000, 1000,false);
     add_image_size( 'work-medium', 600, 600, false );
    add_image_size( 'work-thumbnail', 300, 300, array( 'center', 'center' ) );
    
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





//common admin template
$admin_template = new Admin(
    array(
        'theme_css' => 'pwp-style.css',
        'remove_menu' => array(
            'link-manager.php',
            'tools.php',
            'edit-comments.php',
	    'upload.php',
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
$admin_template->remove_menu_items();
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
            'name'               => __( 'Works', 'pwp' ),
            'singular_name'      => __( 'Work', 'pwp' ),
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
            'menu_name'          => __( 'Home slides', 'pwp' )
        );

        $slide_args = array(
            'labels'             => $slide_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
	    'taxonomies'	 => array('post_tag'),
            'rewrite'            => array( 'slug' => 'work' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
	    'supports'           => array( 'title','thumbnail' )
        );
        register_post_type( 'work', $slide_args );

        $file_labels = array(
            'name'               => __( 'File', 'pwp' ),
            'singular_name'      => __( 'File', 'pwp' ),
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
            'menu_name'          => __( 'File', 'pwp' )
        );

        $file_args = array(
            'labels'             => $file_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
	    //'taxonomies'	 => false,
            'rewrite'            => array( 'slug' => 'file' ),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
	    'supports'           => array( 'title' )
        );
        register_post_type( 'file', $file_args );

        
}
add_action( 'init', 'register_post_types' );


//metabox obrazek dodatkowy w brandach (logo na czarnym)
$work_meta = array(
    'name'      => 'file_meta',
    'title'     => __( 'Dodatkowe', 'pwp' ),
    'post_type' => 'file',
    'elements'  => array(
    array(
        'type'  => 'attachment',
        'name'  => 'file',
        'params'    => array(
            'label'     => __( 'Plik załącznika', 'pwp' ),
            
         
        )
    ),
    )
);
new Metabox( $work_meta );









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
    
    dump($q);
    
    
    
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
//$admins = new Administrator();
$admins = Administrator::init();

$page = array(
    'page_title'    => __( 'Test settings page', 'pwp' ),
    'menu_title'    => __( 'Test settings', 'pwp' ),
    'capability'    => 'manage_options',
    'menu_slug'	    => 'test-options',
    'icon'	    => '',
    'position'	    => null,
);
$admins->add_page( $page );
$admins->add_tab( 'Nowy tab', 'test-options' );
$options = new Options();
$options->set_name( 'a_options' )
        ->set_action( 'options.php' )
        ->set_title( __( 'Pierwsze opcje', 'pwp' ) );
$options->add_element( 'text', 'tekst' )
        ->set_label( __( 'User email templatec', 'pwp' ) )
        ->set_class( 'klasa' )
        ->set_validator( array( 'notempty' ) );

$admins->add_options( $options, 'test-options' );
$admins->add_options( $options, 'nowy-tab' );
$admins->add_tab( 'Inny tab', 'test-options' );
$options_tabs = new Options();
        $options_tabs->set_name( 'tab_options' )
                ->set_action( 'options.php' )
                ->set_title( __( 'opcje w tabie', 'pwp' ) );

        $options_tabs->add_element( 'text', 'tekstt' )
                    ->set_label( __( 'pole w tab', 'pwp' ) )
                    ->set_class( 'klasa' )
                    ->set_validator( array( 'notempty' ) );

	$options_tabs->add_element( 'attachment', 'zalacznik' )
                    ->set_label( __( 'Załacznik', 'pwp' ) )
                    ->set_class( 'klasa' )
                    ->set_validator( array( 'notempty' ) );

        $options_tabs->add_element( 'image', 'obrazek' )
                    ->set_label( 'Obrazek' )
                    ->set_comment( 'komentarz' )
                    ->set_validator( array( 'notempty' ) );


	$elements_repeater = array(
            array(
                'type' => 'text',
		'name' => 'user_email_templates',
		'params'=> array(
                    'label' => __( 'User email templatex', 'pwp' ),
                    'class' => 'large-text',
                ),
            ),
	    array(
		'type' => 'text',
		'name' => 'zalacznik',
		'params'=> array(
                    'label' => __( 'Ue', 'pwp' ),
                    'class' => 'large-text',
                ),
	    )
        );

        $options_tabs->add_element( 'repeatable', 'powtorz' )
                    ->set_label( 'Powtarzalne' )
                    ->set_comment( 'komentarz do repeatable' )
                    ->add_elements( $elements_repeater );
        $admins->add_options( $options_tabs, 'inny-tab' );




//metabox obrazek dodatkowy w brandach (logo na czarnym)
$brand_meta = array(
    'name'      => 'brand_meta',
    'title'     => __( 'Dodatkowe', 'pwp' ),
    'post_type' => 'post',
    'elements'  => array(
    array(
        'type'  => 'image',
        'name'  => 'logo_black',
        'params'    => array(
            'label'     => __( 'Logo na czarnym tle', 'pwp' ),
            'comment'   => __( 'Logo producenta z tłem w kolorze czarnym', 'pwp' ),
            'class'     => 'large-text',
        )
    ),
	array(
	    'type'=>'repeatable',
	    'name'=>'powtarzalne',

'params' => array(
		'label' => 'Powtarzalne z obrazkami',

	    'options' => array(
		array(
	    'type'	=> 'image',
	    'name'	=> 'file34',
	    'params'	=> array(
	        'label'	    => __( 'jpeg', 'pwp' ),
	        'comment'   => __( 'Plik załącznika tylko w jpg', 'pwp' ),
	        'class'	    => 'large-text',
                'data'      => array(
                    'mime'      => 'image/jpeg',
                    'title'     => 'obrazki jpg',
                    'select'    => __( 'Insert image', 'pwp' )
                )
            )
	),
		array(

		  'type' => 'text',
		'name' => 'zalacznik',
		'params'=> array(
                    'label' => __( 'Ue', 'pwp' ),
                    'class' => 'large-text',
                ),

		)

		)),

	)
    )
);
new Metabox( $brand_meta );
