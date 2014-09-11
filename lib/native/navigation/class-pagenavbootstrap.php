<?php

class Navigation_PagenavBootstrap{


    
//stronicowanie bootstrap
static function link_pages( $args = '' ) {
    $defaults = array(
        'before'            => '',
        'after'             => '',
        'link_before'       => '',
        'link_after'        => '',
        'next_or_number'    => 'number',
        'nextpagelink'      => __( 'Next page', 'pwp' ),
        'previouspagelink'  => __( 'Previous page', 'pwp' ),
        'pagelink' => '%',
        'echo' => 1
    );

    $r = wp_parse_args( $args, $defaults );
    $r = apply_filters( 'wp_link_pages_args', $r );
    extract( $r, EXTR_SKIP );

    global $page, $numpages, $multipage, $more, $pagenow;

    $output = '';
    if ( $multipage ) {
        if ( 'number' == $next_or_number ) {
            $output .= $before . '<ul class="pagination pagination-sm pull-right hidden-print">';
            $laquo = $page == 1 ? 'class="disabled"' : '';
            $icon = $page == 1 ? 'icon-white' : '';
            $output .= '<li ' . $laquo .'>' . _wp_link_page( $page - 1 ) . '<i class="glyphicon ' . $icon . ' glyphicon-chevron-left"></i></a></li>';
            for ( $i = 1; $i < ( $numpages + 1 ); $i = $i + 1 ) {
                $j = str_replace('%',$i,$pagelink);
                if ( ( $i != $page ) || ( ( !$more ) && ( $page ==1  ) ) ) {
                    $output .= '<li>';
                    $output .= _wp_link_page( $i ) ;
                } else {
                    $output .= '<li class="active">';
                    $output .= _wp_link_page( $i );
                }
                $output .= $link_before . $j . $link_after ;
                $output .= '</a>';
                $output .= '</li>';
            }
            $raquo = $page == $numpages ? 'class="disabled"' : '';
	    $icon = $page == $numpages ? 'icon-white' : '';
            $output .= '<li ' . $raquo .'>' . _wp_link_page( $page + 1 ) . '<i class="glyphicon  ' . $icon . ' glyphicon-chevron-right"></i></a></li>';
            $output .= '</ul>' . $after;
        } else {
            if ( $more ) {
                $output .= $before . '<ul class="pager">';
                $i = $page - 1;
                if ( $i && $more ) {
                    $output .= '<li class="previous">' . _wp_link_page( $i );
                    $output .= $link_before. $previouspagelink . $link_after . '</a></li>';
                }
                $i = $page + 1;
                if ( $i <= $numpages && $more ) {
                    $output .= '<li class="next">' . _wp_link_page( $i );
                    $output .= $link_before. $nextpagelink . $link_after . '</a></li>';
                }
                $output .= '</ul>' . $after;
            }
        }
    }
    if ( $echo )
        echo $output;
    return $output;
}          


//pagination for bootstrap
static function paginate() {
    if( is_single() )
		return;

	global $wp_query;

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/**	Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/**	Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div><ul class="pagination pagination-sm pull-right hidden-print">' . "\n";

	/**	Previous Post Link */
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link('<i class="fa fa-angle-left"></i>') );

	/**	Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			echo '<li class="disabled"><a>…</a></li>';
	}

	/**	Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/**	Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			echo '<li class="disabled"><a>…</a></li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/**	Next Post Link */
	if ( get_next_posts_link() )
		printf( '<li>%s</li>' . "\n", get_next_posts_link('<i class="fa fa-angle-right"></i>') );

	echo '</ul></div>' . "\n";

}



}