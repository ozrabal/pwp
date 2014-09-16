!function ($) {
    $(function() {
	$( '.okcookie' ).click( function(e) {
	    e.preventDefault();
	    $.ajax({
		type : 'POST',
                data : {
		    action : 'okcookie',
		    sec : cookie_sec
                },
                url : ajaxurl,
                success: function(data) {
		    if( data.cookies ) {
			$( '.cookieinfo' ).hide();
		    }
                }
            });
	});
    });
}(window.jQuery);