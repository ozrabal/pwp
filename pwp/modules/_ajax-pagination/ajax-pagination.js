!function ($) {
    $(function(){
        var page = parseInt( axp.start ) + 1;
	var max = parseInt( axp.max );
	var nextLink = axp.next_url;
	var label = axp.label;
	if( page <= max ) {
            //id of main content
            $( '#content' ).append('<a id="load-more" href="#">'+label.load_more+'</a>');
            //css class of pagination links to remove
            $('.navigation').remove();
	}
        $('#load-more').bind('click', function(event) {
            event.preventDefault();
            if(page <= max) {
                $(this).text(label.loading);
                $.get(nextLink + '?ajax=true', function(data){ 
                    $(data).hide().insertBefore('#load-more').slideDown('slow');
                    //add history browser state and url into url bar
                    var stateObj = { foo: 1000 + Math.random()*1001 };
                    history.pushState(stateObj, "ajax page loaded...", nextLink);
                    page++;
                    nextLink = nextLink.replace(/\/page\/[0-9]*/, '/page/'+ page );
                    if(page <= max) {
                        $('#load-more').text(label.load_more);
                    } else {
                        $('#load-more').text(label.all_loaded);
                    }
                });
            }
	});
    })
}(window.jQuery)