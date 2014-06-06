<?php


class Walker_Thumbnailmenu extends Walker_Nav_Menu{


	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<div class=\"sub-menu\">\n";
	}



	/**
	 * @see Walker::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</div>\n";
	}



/*
	<div class="three columns box">
		<div class="image">
			<a href="" title="" class="spec-button multimedia">
				<img src="images/spec-multimedia-s.png" alt="" width="213" height="198">
			</a>
		</div>
		<h2><a href="" title="">Multimedia</a></h2>
	</div>
*/


//<div class="col-sm-3 col-xs-6 box">
//
//<a href=""><img src="images/spec-multimedia-static.png" class="img-responsive"></a>
//<h2><a class="read-more pull-left" href="">
//<span class="orange">›</span>›</a><a href="">Multimedia</a></h2>
//
//</div>


		function start_el(&$output, $item, $depth=0, $args = Array(), $current_object_id = 0) {
			global $wp_query;
			//dump($item);
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
                //echo $args->item_class;
			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
			$class_names = ' class="' . esc_attr( $class_names ) . ' '.$args->item_class.'"';

			$output .= $indent . '<div id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
			$custom['slug']= ! empty( $item->slug )        ? ''   . esc_attr( $item->slug        ) .'' : '';

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .' class="spec-button '.$custom['slug'].'">';
			$item_output .='<img src="'.get_template_directory_uri().'/images/spec-'.$custom['slug'].'-static.png" alt="" class="img-responsive">' ;
			//$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			//$item_output .= '<span class="sub">' . $item->description . '</span>';
			$item_output .= '</a>';
			$item_output .= '<h2><a'. $attributes .'class="read-more pull-left"><span class="orange">›</span>›</a><a '. $attributes .'>'.apply_filters( 'the_title', $item->title, $item->ID ).'</a></h2>';

			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
		/**
		 * @see Walker::end_el()
		 * @since 3.0.0
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param object $item Page data object. Not used.
		 * @param int $depth Depth of page. Not Used.
		 */
		function end_el( &$output, $item, $depth = 0, $args = array() ) {
			$output .= "</div>\n";
		}
}



add_filter( 'wp_setup_nav_menu_item','custom_nav_item' );
function custom_nav_item($menu_item) {
    $menu_item->slug = get_post_meta( $menu_item->ID, '_menu_item_slug', true );
    return $menu_item;
}
