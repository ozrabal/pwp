<?php



class Navigation_Childpagenav{


    static function previous(){
	global $post;
    if ( isset($post->post_parent) && $post->post_parent > 0 ) {
	$children = get_pages('&sort_column=post_date&sort_order=asc&child_of='.$post->post_parent.'&parent='.$post->post_parent);
    };
    foreach( $children as $child ) { $child_id_array[] = $child->ID; }
    $prev_page_id = self::relative_value_array($child_id_array, $post->ID, -1);
    $output = '';
    if( '' != $prev_page_id ) {
        $output .= '<a href="' . get_page_link($prev_page_id) . '"><span class="glyphicon glyphicon-chevron-left"></span> <span class="hidden-xs">'. substr_replace(get_the_title($prev_page_id),'...',32) . '</span></a>';
    }
    return $output;
    }

    static function next(){
	global $post;
    if ( isset($post->post_parent) && $post->post_parent > 0 ) {
        $children = get_pages('&sort_column=post_date&sort_order=asc&child_of='.$post->post_parent.'&parent='.$post->post_parent);
    };
    foreach( $children as $child ) { $child_id_array[] = $child->ID; }
    $next_page_id = self::relative_value_array($child_id_array, $post->ID, 1);
    $output = '';
    if( '' != $next_page_id ) {
        $output .= '<a href="' . get_page_link($next_page_id) . '"><span class="hidden-xs">'. substr_replace(get_the_title($next_page_id),'...',32) . '</span><span class="glyphicon glyphicon-chevron-right"></span></a>';
    }
    return $output;
    }


    // function to find location within array
static function relative_value_array($array, $current_val = '', $offset = 1) {
    $values = array_values($array);
    $current_val_index = array_search($current_val, $values);
    if( isset($values[$current_val_index + $offset]) ) {
        return $values[$current_val_index + $offset];
    }
    return false;
}


}