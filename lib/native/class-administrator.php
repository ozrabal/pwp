<?php

class Administrator{
    private 
            $tabs = array(),
            $current_page,
            $current_tab
    ;

    static $instance = null;

    /**
     * singleton
     * @return Administrator
     */
    public static function init() {
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
	}
        return self::$instance;
    }

    /**
     * konstruktor
     */
    public function __construct() {

        $this->current_page = $this->get_current( 'page' );
        $this->current_tab = $this->get_current( 'tab' );
    }

    /**
     * pobiera slug strony lub taba ustawien na podstawie wartosci w get lub post
     * @param string $type page|tab
     * @return string|false
     */
    private function get_current( $type = 'page' ) {
        
        if( filter_input( INPUT_POST, '_wp_http_referer' ) ) {
            return $this->get_slug( filter_input( INPUT_POST, '_wp_http_referer' ), $type );
        } else {
            if( filter_input( INPUT_GET, $type ) ) {
                return filter_input( INPUT_GET, $type );
            }
            return false;
        }
    }
    
    /**
     * 
     * @param string $_wp_http_referer
     * @param string $type
     * @return string
     */
    private function get_slug( $_wp_http_referer, $type = 'page' ) {
        $parts = explode( $type . '=', $_wp_http_referer );
        if( isset( $parts[1] ) ) {
	    $slug = $parts[1];
	    $s = strpos( $slug, '&' );
	    if( $s !== FALSE ) {
                $slug = substr( $parts[1], 0, $s );
            }
	    return trim( $slug );
	}
    }

    /**
     * ustawia obsluge akcji admin_menu
     * @param array $args
     */
    public function add_page( $args ) {
        $this->page = $args;
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
    }
    
    /**
     * dodaje grupe opcji do strony
     * @param Options $options
     * @param string $page_slug
     */
    public function add_options_group( Options $options, $page_slug ) {
        $this->options[$page_slug] = $options;
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }
    
    
    /**
     * dodaje sekcje opcji do strony
     * @param Options $section
     * @param string $page_slug
     */
    public function add_section( $section, $page_slug ) {
        $this->sections[$page_slug] = $section;
    }
    
    /**
     * rejestruje opcje w systemie
     */
    public function register_settings(){
        
        $current = $this->current_page;
        if( $this->current_tab ) {
            $current = $this->current_tab;
        }
        foreach( $this->options as $tab => $option ) {
            if( $tab == $current ) {
                $this->set_settings_form( $tab, $option );
            }
        }
    }
    
    /**
     * rejestruje opcje i przydziela do odpwiedniej sekcji
     * @param string $tab
     * @param Option $option
     */
    private function set_settings_form( $tab, $option ) {
        
        register_setting( $tab, $option->get_name(), array( $this, 'validate' ) );
        $section_title = null;
        if( isset( $this->sections[$tab]['title'] ) ) {
            $section_title = $this->sections[$tab]['title'];
        } 
        add_settings_section( str_replace( '_', '-', $option->get_name() ), $section_title, array( $this, 'render_section' ), $tab );
        foreach( $option->elements as $element ) {
            $this->add_settings_field( $element, $option, $tab );
        }
    }
    
    /**
     * dodaje element do formularza
     * @param Formelement $element
     * @param Option $option
     * @param string $tab
     */
    private function add_settings_field( $element ,$option, $tab ) {
        $name = $element->get_name();
        if( isset( $element->label ) ) {
            $name = $element->label->get_name();
        }
        add_settings_field( $element->get_name(), $name, array( $this, 'render_element' ), $tab, str_replace('_', '-', $option->get_name()),array( 'field' => $element, 'label_for' => $element->get_name() ) );
    }
      
    /**
     * renderuje element formularza
     * @param Formelement $element
     */
    function render_element( $element ) {
        
        unset( $element['field']->label );
        if( isset( $_SESSION[$element['field']->get_name()] ) ) {
            $element['field']->set_class( $_SESSION[$element['field']->get_name()]['class'] );
            $element['field']->set_message( $_SESSION[$element['field']->get_name()]['message'] );
            unset( $_SESSION[$element['field']->get_name()] );
        }
        echo $element['field']->render();
    }
    /**
     * dodaje stronedo menu administracyjnego
     */
    public function add_menu() {
	if( isset($this->page['parent_slug'] ) ) {
	    add_submenu_page( $this->page['parent_slug'], $this->page['page_title'], $this->page['menu_title'], $this->page['capability'], $this->page['menu_slug'], array( $this, 'render_page' ), $this->page['icon'], $this->page['position'] );
	} else {
            add_menu_page( $this->page['page_title'], $this->page['menu_title'], $this->page['capability'], $this->page['menu_slug'], array( $this, 'render_page' ), $this->page['icon'], $this->page['position'] );
	}
    }
    
    /**
     * dodaje zakladke (tab)
     * @param string $name
     * @param string $page_slug
     */
    public function add_tab( $name = null, $page_slug = null ) {
        if( !empty($name) && !empty( $page_slug ) ) {
            $this->tabs[$page_slug][strtolower( str_replace( ' ', '-', $name ) )] = $name;
        }
    }
    
    /**
     * renderuje sekcje opcji w tabie lub na stronie
     */
    function render_section() {
        $current = $this->current_page;
        if( $this->current_tab ) {
            $current = $this->current_tab;
        }
        if( isset( $this->sections[$current]['content'] ) ) {
            echo '<p>' . $this->sections[$current]['content'] . '</p>';
        }
    }
    
    /**
     * wyswietla bledy validacji opcji
     */
    private function display_error(){
        $e = get_settings_errors( 'error-settings' );
        if( count( $e ) > 0 ) {
            echo '<div class="error settings-error below-h2" id="setting-error-error-settings"><p><strong>' . __( 'There were mistakes, not all changes have been saved', 'pwp' ) . '</strong></p></div>';
            dbug( $e );
        } else {
           settings_errors();
        }
    }
    
    /**
     * renderuje naglowki tabow
     */
    public function render_tab_navigation() {
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
    }
    
    /**
     * renderuje strone ustawien
     */
    public function render_page(){
        
        echo '<div class="wrap"><div id="icon-themes" class="icon32"></div><h2>' . $GLOBALS['title'] . '</h2>';
        $this->display_error();
       
	$this->render_tab_navigation();
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
    
    
    
    function validate(Array $values = null ) {
        if( $this->current_tab ) {
            $current = $this->current_tab;
        } else {
            $current = $this->current_page;
        }
        $this->option_values = get_option( $this->options[$current]->get_name() );
        foreach( $this->options[$current]->elements as $element ) {

	    //dump($element);

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

