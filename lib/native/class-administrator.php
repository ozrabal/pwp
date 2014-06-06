<?php

class Administrator{
    private 
            $tabs = array(),
            $current_page,
            $current_tab
	   
    ;
static $instance = null;

     public static function init() {
        if ( is_null( self::$instance ) )
            self::$instance = new self();
        return self::$instance;
    }



    public function __construct() {
        $this->current_page = $this->get_current_page();
        $this->current_tab = $this->get_current_tab();
    }
    
    private function get_current_page() {
        if( isset( $_POST['_wp_http_referer'] ) ) {
            $parts = explode( 'page=', $_POST['_wp_http_referer'] );
            if( isset( $parts[1] ) ) {
            $page = $parts[1];
            $t = strpos( $page, '&' );
            if( $t !== FALSE ) {
                $page = substr( $parts[1], 0, $t );
            }
            $current_page = trim( $page );
            return $current_page;
            }
        } else {
            if( isset( $_REQUEST['page'] ) ) {
                return $_REQUEST['page'];
            }
            return false;
        }
    }
    
    private function get_current_tab() {
       if( isset( $_POST['_wp_http_referer'] ) ) {
            $parts = explode( 'tab=', $_POST['_wp_http_referer'] );
            if( isset( $parts[1] ) ) {
                $tab = $parts[1];
		$t = strpos($tab, '&' );
		if( $t !== false ) {
		    $t = substr($parts[1],0,$t);
		} else {
		    return $tab;
		}
		return $t;
		//return $page = $parts[1];
            }
        } else {
	    if( isset( $_REQUEST['tab'] ) ) {
		return $_REQUEST['tab'];
            }
        }
        return false;
    }
    
    public function add_page( Array $args ) {
        $this->page = $args;
        //if($this->current_page == $this->page['menu_slug']){
            add_action('admin_menu', array($this, 'add_menu'));
        //}
    }
    
    public function add_options( Options $options, $page_slug ){
        //if($this->current_page == $this->page['menu_slug']){

	
            $this->options[$page_slug] = $options;

	   
            $this->page_slug = $page_slug;
            add_action( 'admin_init', array($this,'register_settings' ));
            
        //}
    }
    public function add_section($section, $page_slug){
        $this->sections[$page_slug] = $section;
    }
    public function register_settings(){
        //z tego options tylko aktualne, te z taba albo strony
	
        //dump($this->options[$this->current_tab]);
if(isset($_POST['_wp_http_referer'])){
      //$this->active_tab = $this->current_page();
      
     
}


	
 if($this->current_tab){
            $current = $this->current_tab;
        }else{
            $current = $this->current_page;
        }

	
	foreach ($this->options as $tab => $option){
	    if($tab == $current){
        register_setting(
               // $option->get_name(),
            //str_replace('_', '-', $option->get_name()), // group, used for settings_fields()
         $tab ,
            $option->get_name(),  // option name, used as key in database
            array($this,'validate')      // validation callback
        );
if(isset($this->sections[$tab]['title'])){
    $section_title = $this->sections[$tab]['title'];
}else{
    $section_title=null;
}
        add_settings_section(
            str_replace('_', '-', $option->get_name()), // ID
               // $tab,
            $section_title, // Title
            array($this, 'render_section'), // print output
            //$this->page['menu_slug'] // menu slug, see t5_sae_add_options_page()
            //$this->page_slug
                $tab
                //$option->get_name()

        );

        foreach($option->elements as $element){

	    if(isset($element->label)){
	    $name = $element->label->get_name();
	    }else{
		$name = $element->get_name();
	    }

            add_settings_field(
                $element->get_name(),
                $name,
                array( $this, 'render_element' ),
                //$this->page['menu_slug'],  // menu slug, see t5_sae_add_options_page()
                //$this->page_slug,
                    $tab,
                    //$option->get_name(),

                str_replace('_', '-', $option->get_name()),
                    //$tab,
                    ///$option->get_name(),
                array(
                    'element' =>$element,
                    'label_for'   => $element->get_name(),
                )
            );
        }

	    }
        }

//        foreach ($this->options as $tab => $option){
//        register_setting(
//               // $option->get_name(),
//            //str_replace('_', '-', $option->get_name()), // group, used for settings_fields()
//         $tab ,
//            $option->get_name(),  // option name, used as key in database
//            array($this,'validate')      // validation callback
//        );
//
//        add_settings_section(
//            str_replace('_', '-', $option->get_name()), // ID
//               // $tab,
//            'Tytuł sekcji', // Title
//            array($this, 'render_section'), // print output
//            //$this->page['menu_slug'] // menu slug, see t5_sae_add_options_page()
//            //$this->page_slug
//                $tab
//                //$option->get_name()
//
//        );
//
//        foreach($option->elements as $element){
//            add_settings_field(
//                $element->get_name(),
//                $element->label->get_name(),
//                array( $this, 'render_element' ),
//                //$this->page['menu_slug'],  // menu slug, see t5_sae_add_options_page()
//                //$this->page_slug,
//                    $tab,
//                    //$option->get_name(),
//
//                str_replace('_', '-', $option->get_name()),
//                    //$tab,
//                    ///$option->get_name(),
//                array(
//                    'element' =>$element,
//                    'label_for'   => $element->get_name(),
//                )
//            );
//        }
//        }
    }
    
    function render_element($element){
        unset($element['element']->label);
        if(isset($_SESSION[$element['element']->get_name()])){
            $element['element']->set_class($_SESSION[$element['element']->get_name()]['class']);
            $element['element']->set_message($_SESSION[$element['element']->get_name()]['message']);
            unset($_SESSION[$element['element']->get_name()]);
        }
        echo $element['element']->render();
    }

    public function add_menu(){
	if(isset($this->page['parent_slug'])){
	    add_submenu_page( $this->page['parent_slug'], $this->page['page_title'], $this->page['menu_title'], $this->page['capability'], $this->page['menu_slug'], array($this,'render_page'), $this->page['icon'], $this->page['position'] );
	}else{
            
	    add_menu_page( $this->page['page_title'], $this->page['menu_title'], $this->page['capability'], $this->page['menu_slug'], array($this,'render_page'), $this->page['icon'], $this->page['position'] );
	}
    }
    
    public function add_tab($name, $page_slug){
        $this->tabs[$page_slug][strtolower(str_replace( ' ', '-', $name ))] = $name;
        
    }
    
    function render_section(){
        if( $this->current_tab ) {
            $current = $this->current_tab;
        } else {
            $current = $this->current_page;
        }
       if(isset($this->sections[$current]['content'])){
        
       echo '<p>'.$this->sections[$current]['content'].'</p>';
       }
    }
    
    public function render_page(){
        
        echo '<div class="wrap">';
        echo '<div id="icon-themes" class="icon32"></div>';
        echo '<h2>'.$GLOBALS['title'].'</h2>';
        //settings_errors();
        $e = get_settings_errors('error-settings');
       if(count($e) > 0){
           
           echo '<div class="error settings-error below-h2" id="setting-error-error-settings"> 
<p><strong>'.__('There were mistakes, not all changes have been saved', 'pwp').'</strong></p></div>';
        //dump($e);
       }else{
           settings_errors();
       }
       
	if(isset($this->tabs[$this->current_page])){
            $page= '?';
            if(isset($this->page['parent_slug'])){
                $page = $this->page['parent_slug'].'&';
            }
            echo '<h2 class="nav-tab-wrapper">';
            foreach($this->tabs[$this->current_page] as $tab => $tab_name){
                $active = null;
                if($this->current_tab){
                    if($this->current_tab == $tab){
                        $active = 'nav-tab-active';
                    }
                }else{
                    $a = array_values($this->tabs[$this->current_page]);
                    if(strtolower(str_replace( ' ', '-', $a[0] )) == $tab){
                        $active = 'nav-tab-active';
                    }
                }
                echo'<a href="'.$page.'page='.$this->page['menu_slug'].'&tab='.$tab.'" class="nav-tab '.$active.'">'.$tab_name.'</a>';
            }
            echo '</h2>';
        }
        echo '<form action="options.php" method="POST">';
        
        //settings_fields( str_replace('_', '-', $this->options[$this->page_slug]->get_name()) );
        //settings_fields(  );
        //do_settings_sections( $this->page['menu_slug'] );
        //dump($this->current_page);
        if(!$this->current_tab){
            settings_fields( $this->current_page );
            do_settings_sections( $this->current_page );
            
        }else{
            settings_fields($this->current_tab);
            do_settings_sections( $this->current_tab );
        }
	
        submit_button();
        echo '</form>';
        echo '</div>';
    }
    
    function validate(Array $values ) {
        if( $this->current_tab ) {
            $current = $this->current_tab;
        } else {
            $current = $this->current_page;
        }
        $this->option_values = get_option( $this->options[$current]->get_name() );
        foreach( $this->options[$current]->elements as $element ) {
            if( $element->get_validator() ) {
                $o = $element->validate( $values[$element->get_name()], $element->get_validator());
                //$this->set_message($element, $o, $current);
                
                //dump($o);
                if( $o ) { 
                    //dump(implode('|', $o['error']));
                   // add_settings_error( $this->options[$current]->get_name(), 'error-settings', $element->label->get_name().' : '.$o );
                                       add_settings_error( 'error-settings', 'error-settings', $element->label->get_name().' : '.$o );

                    $values[$element->get_name()] = $this->option_values[$element->get_name()];
                    $element->set_class( 'error' );
                    $_SESSION[$element->get_name()]['class'] = 'pwp-error';
                    $_SESSION[$element->get_name()]['message'] = array( 'error', $o );
                }
            }
            
        }
	
    //die();
	
        return $values;
    }
    
   
    
    
}

