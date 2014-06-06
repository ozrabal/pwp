<?php
//namespace pwp{

	class Editor{

		var $args =array();

		function __construct($args){
			$this->args = $args;
		}

		public function remove_quicktags( $qtInit ){
			$buttons = explode(',', $qtInit['buttons']);
			for( $i=0; $i < count($this->args['remove_quicktags']); $i++ ){
				if( ($key = array_search($this->args['remove_quicktags'][$i], $buttons)) !== false)
					unset($buttons[$key]);
				}
			$qtInit['buttons'] = implode(',', $buttons);
			return $qtInit;
		}

		public function remove_buttons($init){
			$init['theme_advanced_disable'] = $this->args['remove_buttons'];
			return $init;
		}

		function add_buttons($buttons) {
			$buttons[] = $this->args['add_buttons'];
			return $buttons;
		}

		function add_editor_buttons(){
			if(isset($this->args['add_buttons']) && count($this->args['add_buttons']) > 0){
				add_filter('mce_buttons_2', array($this,'add_buttons'));
			}
		}

		function remove_editor_buttons(){
			if(isset($this->args['remove_quicktags']) && count($this->args['remove_quicktags']) > 0 ){
				add_filter('quicktags_settings', array($this,'remove_quicktags'), 10, 1);
			}
			add_filter('tiny_mce_before_init', array($this,'remove_buttons'));
		}

		function editor_styles($settings){
			$settings['style_formats'] = json_encode( $this->args['editor_style']);
			return $settings;
		}

		function add_editor_style(){
			if(isset($this->args['editor_style']) && count($this->args['editor_style']) > 0 ){
				add_filter( 'tiny_mce_before_init', array(&$this,'editor_styles'));
			}
		}

		function add_editor_stylesheet(){
			if(isset($this->args['editor_css'])){
                                //add_theme_support('editor_style');
                                add_action('init', array($this,'add_style'));

				//add_editor_style($this->args['editor_css']);
			}
		}
function add_style() {
    add_editor_style($this->args['editor_css']);
}

	}