<?php


       
    
add_action( 'pwp_init_access-control', array( 'Accesscontrol','init' ) );





class Accesscontrol{
   static $instance = null;
   protected $superuser = array();
   
   
   
   
   
   
    public static function init() {
        
        if ( is_null( self::$instance ) )
            self::$instance = new Accesscontrol();
        return self::$instance;
    }


    private function set_superuser(){
        $this->superuser[] = 'administrator';
    }
    
   public function __construct(){
if(!is_admin()){
    require_once PWP_ROOT.'lib/external/class-securewalker.php';
	dump(__CLASS__);
	add_action('wp', array($this, 'check'));
//
	add_filter('the_posts', array($this, 'the_posts'));
//
	add_filter('wp_nav_menu_args', array($this, 'wp_nav_menu_args'), 99999);
	add_filter('wp_page_menu_args', array($this , 'wp_page_menu_args'));
//
//	add_filter( 'get_previous_post_where', array($this,'adjacent_bis') );
//	add_filter( 'get_next_post_where', array($this,'adjacent_bis' ));

        
        //add_filter('posts_join_paged', array($this, 'posts_join_paged'), 10, 2);
        //add_filter('posts_where_paged', array($this, 'posts_where_paged'), 10, 2);
        
        
        
}
	$this->setup();
    }

    
    
    
    
    public  function posts_join_paged($join, $query)
  {
    global $wpdb;
dump($join);
    
    
    if (is_admin()) return $join;

    if (!is_singular() && !is_admin()) {
      $join .= " LEFT JOIN {$wpdb->postmeta} PMWPAC3 ON PMWPAC3.post_id = {$wpdb->posts}.ID AND PMWPAC3.meta_key = 'access' ";
    }

    return $join;
  }

  public  function posts_where_paged($where, $query)
  {
    global $wpdb;
    dump($where);
    if (is_admin()) return $where;

    if (!is_singular() && !is_admin()) {
      if (!is_user_logged_in()) {
        $where .= " AND (PMWPAC.meta_value IS NULL OR PMWPAC3.meta_value = '1' OR PMWPAC.meta_value != 'true') ";
      } else {
        $where .= " AND (PMWPAC3.meta_value IS NULL OR PMWPAC3.meta_value = '".serialize(array('allowed' => 'logged_in'))."' ) ";
      }
    }

    return $where;
  }
    
    
    
    
    
    
    
    
    
    
    
    
    public function wp_page_menu_args($args)
  {
        
       
    // Only remove the walker if it is ours
    if (isset($args['walker']) && get_class($args['walker']) == 'WpacSecureWalker') {
      $args['walker'] = new WpacSecureWalker(new Walker_Page(), $this);
    } elseif (isset($args['walker'])) {
      $args['walker'] = new WpacSecureWalker($args['walker'], $this);
    } else {
      $args['walker'] = new WpacSecureWalker(new Walker_Page(), $this);
    }

    return $args;
  }

function adjacent_bis( $where ) {
    //pobrac id postow zabronionych
 global $current_user;
 get_currentuserinfo();

 //dump($current_user->roles[0]);
    $args = array(
		    'post_type'	    => array( 'post' ),
		    //'showposts'	    => $slider_options['limit'],
		    //'lang'              => $current_language,
//		    'tax_query'         => array(
//                        array(
//                            'taxonomy'  => 'templatesection',
//                            'field'     => 'slug',
//                            'terms'     => 'strona-glowna-slideshow',
//                        ),
//                    ),
		    'meta_query'        => array(
			'relation'      => 'AND',
			 array(
			    'key'       => 'access',
			  'value'     => serialize(array('allowed' => $current_user->roles[0])),
			    'compare'   => '<>',
			)

		    ),
		    'orderby'           => 'menu_order',
		    'order' => 'ASC'
		);
		$query = new WP_Query( $args );
		//dump($query);
		if ( $query->have_posts() ) {
		    while ( $query->have_posts() ) {
			    $query->the_post();
			    $allowed[] = get_the_ID();
			}
		}

$a = implode(',', $allowed);
unset($allowed);

    global $wpdb;
    //return $where . " AND p.ID NOT IN ( SELECT post_id FROM $wpdb->postmeta WHERE ($wpdb->postmeta.post_id = p.ID ) AND $wpdb->postmeta.access = 'my_field' )";
    return $where . " AND p.ID NOT IN ( '".$a."')";



}


    public function the_posts($posts){
        global $wp_query;
 $wp_query->found_posts;
 
//dump($wp_query->found_posts);
        
        //$wp_query->found_posts = 8;
        
    dump($posts);    
if(!is_singular()){
   foreach($posts as $key => $post){

if(!$this->is_allowed($post->ID)){
 
    unset($posts[$key]);
}



   }
//dump($posts);
}
   return array_values($posts);


}


public function wp_nav_menu_args($args){
    //dump($args);
    //require_once PWP_ROOT.'lib/external/class-securewalker.php';
    $args['walker'] = new WpacSecureWalker($args['walker'], $this);
    return $args;
}



private function get_allowed($post_id){
    $allowed = get_post_meta($post_id, 'access', true);
    dump($allowed);
    if(!empty($allowed)){
    return $allowed;
    }
}


private function user_can($allowed){
    
    
    global $current_user;
   
    
     $intersections = array_intersect($current_user->roles, array($allowed));
     
     if(!empty($intersections)){
         return true;
     }
     return false;
}



public function is_allowed($post_id = null){
    global $wp_roles, $current_user;
    
    if(!empty($post_id)){
        
        
      if (current_user_can( 'manage_options' )) {
            return true;
        }
        
$allowed = $this->get_allowed($post_id);
    //dump($allowed);
    
     
    if (is_user_logged_in() && !empty($allowed)) {
      get_currentuserinfo();
    
     
     
     if($allowed){
     
     if(!is_array( $allowed )){
         $allowed = array($allowed);
     }
     }else{
         //$allowed = $wp_roles->get_names();
         $allowed_roles_tmp = $wp_roles->get_names();
        $allowed_roles = array();

        foreach ($allowed_roles_tmp as $role => $garbage) {
          $allowed[] = $role;
        }
     }
     //dump($allowed);
     
     
     $intersections = array_intersect($current_user->roles, $allowed);
     ///dump($intersections);
     if(count($intersections)){
        return true;
     }
     return false;
      
    }
    if(!empty($allowed)){
        return false;
    }
    }
    return true;
    
}




public function check(){
    global $post;
    
    if (is_singular()) { 
   if($this->is_allowed($post->ID)){
      // dump('dozwolony');
   }  else {
   dump('zabroniony');
   header('Location: ' . wp_login_url(site_url($_SERVER['REQUEST_URI'])));
exit;
   }
    }
    
}


public static function remove_restricted_posts($posts, $query = false){
    dump($posts);
    
}







public function check_access(){
    
    global $post;
    
     dump($post);
 global $current_user;
    if (/*is_admin() ||*/ !$post) {
      return;
    }
     $allowed = $this->get_allowed($post->ID);
    if(!empty($allowed)){
        if (is_user_logged_in()) {
            get_currentuserinfo();
           
        //dump($current_user->roles);
            
        
        //$intersections = array_intersect($current_user->roles, array($allowed));
        
            //dump($intersections);
        if (is_singular()) {
        if($this->user_can($allowed)){
            return;
        }else{
            dump('redirect do logowania');
            header('Location: ' . wp_login_url(site_url($_SERVER['REQUEST_URI'])));
        }
        }
        
        
        }else{
            dump('redirect do logowania');
            header('Location: ' . wp_login_url(site_url($_SERVER['REQUEST_URI'])));
	    //wp_redirect(wp_login_url(site_url($_SERVER['REQUEST_URI'])));
        }
        exit; 
    }
    
    
    
    
}







    public function c(){
 global $post;
 global $wp_roles, $current_user;
 
 
 
 $allowed = $this->get_allowed($post->ID);
 
if(!empty($allowed)){

    
    
    
if (is_user_logged_in()) {
      get_currentuserinfo();

$intersections = array_intersect($current_user->roles, $allowed);

echo 'inter';
//dump($intersections);





if (is_singular()) {
if(empty($intersections)){
    dump('access denied');
    //header('Location: ' . add_query_arg('redirect_to', $_SERVER['REQUEST_URI'], home_url()));


}else{
    
}

	//dump($allowed);

	
	//dump(__METHOD__);

	//exit;
}
}else{
    dump('niedozwolony');
}

}



}

    



public function setup(){

     //pola metabox w typie postu form
        $metad = array(
            array(
                'name'      => 'access',
                'title'     => __( 'Acces manager', 'pwp' ),
                //'callback'=> '',
                'post_type' => array('post','page'),
                'elements'  => array(

                    
                   

                    array(
                        'type' => 'select',
                        'name' => 'access',
                        'params'=> array(
                            'label' => __( 'Allow access to:', 'pwp' ),
                            'comment' => __('Content of this page are restricted to only this user group','pwp'),
'class'	    => 'large-text block',
		'options' => $this->get_roles(),
			    'default' => 'logged_in'
		
                        ),
                    ),
                )
            )
        );
        foreach( $metad as $box ){
            new Metabox( $box );
        }
}


//pobiera kategorie do selecta
function get_roles(  ) {


    global $wp_roles;
    $default_roles = array();
    //$default_roles = array('administrator','editor', 'author', 'contributor', 'subscriber', 'pending');
    
    
     $roles = $wp_roles->get_names();
    // dump($roles);

     $roless['Bez ograniczeń'] = '';
foreach($roles as  $role => $name){
    if(!in_array( $role, $default_roles)){
    $roless[$name] = $role;
    }
}

     //dump($roless);
return $roless;
     /*
    $categories = get_categories( $args );
    $cat[__('Wybierz kategorię')] = '';
    foreach( $categories as $category ) {
	$cat[$category->name] = $category->term_id;
    }
    return $cat;
    */
}





}