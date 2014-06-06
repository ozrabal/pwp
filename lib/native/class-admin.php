<?php

class Admin{

	var $args =array();

	function __construct($args){
		$this->args = $args;
	}

	function theme_style() {
		wp_enqueue_style('theme_style', plugin_dir_url('pwp/lib/'.$this->args['theme_css'], __FILE__).$this->args['theme_css']);
	}

	function add_theme_style(){
	    if(isset($this->args['theme_css'])){
		add_action('admin_enqueue_scripts', array($this, 'theme_style'));
	}
	}

	function remove_menu_pages() {
	    $user = wp_get_current_user();
	    if( $user->user_login != 'adminBluenote' ) {
		foreach( $this->args['remove_menu'] as $p ) {
		    remove_menu_page( $p );
		}
	    }
	}

	function remove_menu_items(){
		add_action( 'admin_menu', array($this,'remove_menu_pages'));
	}

	function remove_admin_bar_links() {
		global $wp_admin_bar;
		foreach($this->args['remove_admin_bar'] as $l){
			$wp_admin_bar->remove_menu($l);
		}
		return $wp_admin_bar;
	}

	function remove_bar_links(){
		return add_action( 'wp_before_admin_bar_render', array($this,'remove_admin_bar_links'));
	}

}