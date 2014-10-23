<?php

add_action('pwp_init_configuration',  array('Configuration','init'));

class Configuration {

    static function init(){

        $meta = array(
            array(
	    'name' => 'pwp_post',
	    'title' => __( 'Post parameterss', 'pwp' ),
	    //'callback' => '',
	    'post_type' => 'post',
	    'elements' =>array(
		array(
		    'type' => 'textarea',
		    'name' => 'user',
		    'params'=> array(
			'label' => __( 'User email template', 'pwp' ),
			'class' => 'large-text',
		    ),
		)
	    )
            )
        );

        foreach( $meta as $box){
            new Metabox($box);
        }
    }
    
    
    
    public function __construct($options) {
    //dump($options);
	if(is_array($options)){

       $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_options';
       
        $this->options = $options[$active_tab];
	
	}else{
	    $this->options = $options;
	}

	$this->option_values = get_option( 'form_options' );
        add_action('admin_menu', array($this, 'add_menu'));
        add_action( 'admin_init', array($this,'t5_sae_register_settings' ));
  
        if ( ! empty ( $GLOBALS['pagenow'] )
            and ( 'options-general.php' === $GLOBALS['pagenow']
                or 'options.php' === $GLOBALS['pagenow']
            )
        )
        {
        }

    }


public function add_menu(){
//add_submenu_page( 'settings.php', __('Form options title', 'pwp'), __('Form options', 'pwp'), 'manage_options', 'pwp_setup', array($this,'render') );
//add_submenu_page( 'pwp_setup', 'My Custom Submenu Page', 'My Custom Submenu Page', 'manage_options', 'my-custom-submenu-page', array($this,'my_custom_submenu_page_callback') ); 



add_submenu_page( 'pwp_setup', __( 'Form_options title w cofnig', 'pwp' ), __('Form options cfg', 'pwp'), 'manage_options', 't5_sae_slug', array($this,'render_page')  ); 

//add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
//add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);

//add_options_page(
//        'T5 Settings API Example', // $page_title,
//        'Txx5 SAE',                  // $menu_title,
//        'manage_options',          // $capability,
//        't5_sae_slug',             // $menu_slug
//        array($this,'render_page')       // Callback
//    );




}

function render_page()
{
    ?>
    <div class="wrap">
        <div id="icon-themes" class="icon32"></div>
        <h2><?php print $GLOBALS['title']; ?></h2>
        <?php settings_errors() ?>
        <?php $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'display_options'; ?>
	
        <h2 class="nav-tab-wrapper">
            <a href="?page=t5_sae_slug&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Display Options</a>
            <a href="?page=t5_sae_slug&tab=social_options" class="nav-tab <?php echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">Social Options</a>
        </h2>
       
        <form action="options.php" method="POST">
            <?php
if( $active_tab == 'display_options' ) {

    settings_fields( 'form_options' );

    do_settings_sections( 't5_sae_slug' );

    } else {
            

	}
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function t5_sae_register_settings(){
    $option_name   = 'form_options';

    // Fetch existing options.
    

    //dump($this->option_values);   


    $default_values = array (
        'tekst' => 1,
        'color'  => 'blue',
        'long'   => ''
    );


    //dump($this->options->elements);


    // Parse option values into predefined keys, throw the rest away.
    $data = shortcode_atts( $default_values, $this->option_values );





    register_setting(
        'form_options', // group, used for settings_fields()
        $option_name,  // option name, used as key in database
        array($this,'validate')      // validation callback
    );



    /* No argument has any relation to the prvious register_setting(). */
    add_settings_section(
        'section_1', // ID
        'Some text fields', // Title
        array($this,'t5_sae_render_section_1'), // print output
        't5_sae_slug' // menu slug, see t5_sae_add_options_page()
    );


foreach($this->options->elements as $element){

    add_settings_field(
        $element->get_name(),
        $element->label->get_name(),
       // 't5_sae_render_section_1_field_1',
	    array($this, 'r'),
        't5_sae_slug',  // menu slug, see t5_sae_add_options_page()
        'section_1',
        array(
	    'element' =>$element,
'label_for'   => $element->get_name(),
		)
    );
    //dump($element);
    

}


}
function validate($values){
   //dump($values);
    //dump($this->option_values);   
    
    foreach($this->options->elements as $element){
        //dump($element->get_validator());
        $o = $element->validate($values[$element->get_name()],$element->get_validator() );
        if($o){ 
            
          
            
        add_settings_error(
                        'form_options',
                        'number-too-low',
                        $element->label->get_name().' : '.$o
                    );
        $values[$element->get_name()] = $this->option_values[$element->get_name()];
        }
    }
    //dump($o);
    return $values;
    //die();
}

function r($element){
    
    //dump($element);
    unset($element['element']->label);
    echo $element['element']->render();
}
function t5_sae_render_section_1()
{
    
    //dump($this->options->render());
    
    print '<p>Pick a number between 1 and 1000, and choose a color.</p>';




}
//function my_custom_submenu_page_callback() {
//	
//	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
//		echo '<h2>My Custom Submenu Page</h2>';
//	echo '</div>';
//
//}

}
    


// $this->options = Options::get_instance();
        $options['display_options'] = new Options();
        $options['display_options']->set_name('form_options')->set_action('options.php')->set_title(__('Form options', 'pwp'));
        

        $options['display_options']
                ->add_element('text','tekst')
                ->set_label( __( 'User email template', 'pwp' ))
                ->set_class('klasa')
                ;
        $options['display_options']
                ->add_element('text','tekst2')
                ->set_label( __( 'User email template2', 'pwp' ))
                ->set_class('klasa2')
                ->set_validator(array('notempty'));

        $options['display_options']
                    ->add_element('select', 'ilosc')
                    ->set_label('etykieta')
                    ->set_options(array(
                            'Wybierz ilość biletów' => '0',
                            '1' => '1',
                            '2' => '2',
                            '3' => '3',
                            '4' => '4',
                            '5' => '5',
                            '6' => '6',
                            '7' => '7',
                            '8' => '8',
                            '9' => '9',
                            '10' => '10',
                            ));

        $options['display_options']
                    ->add_element('image', 'obrazek')
                    ->set_label('Obrazek')
                    ->set_validator(array('notempty'));
            $elements_repeater = array(
		array(
		    'type' => 'text',
		    'name' => 'user_email_template',
		    'params'=> array(
			'label' => __( 'User email template', 'pwp' ),
			'class' => 'large-text',
		    ),
		),
            );

        $options['display_options']
	    ->add_element('repeatable','powtorz')
	    ->set_label('Powtarzalne')
            ->add_elements($elements_repeater)
            //->add_elements($elements_repeater)
            //->add_elements($elements_repeater)
	    ;


new Configuration($options);


