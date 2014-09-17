jQuery( function( $ ) { 

//$('.lightbox').fancybox({
//		'padding' : 0,
//		'height': 800,
//		'autoDimensions': true,
//		'autoScale': true,
//		'overlayColor' : '#000000',
//		//'titleShow' : true,
//		'onComplete': function() {
//			$("#fancybox-title").css({'left':'auto'})}
//	});
if(jQuery.fn.fancybox !== undefined) {
	    $('.lightbox').fancybox({
		'padding' : 0,
		'height': 800,
		'autoDimensions': false,
		'autoScale': true,
		'overlayColor' : '#000000',
		'titleShow' : true,
		'onComplete': function() {
		$("#fancybox-title").css({'left':'auto'});}
	    });
	}
        
        
        $.fn.setAllToMaxHeight = function() {
  return this.height(Math.max.apply(this, $.map(this.children(), function(e) { 
    return $(e).height();
  })));
};
//$('div.thumbnail').setAllToMaxHeight()

        //opis caption do title w lightbox
//	$('.wp-caption img').each(function() {
//	    $(this).parent().attr('title',$(this).parent().parent().find('.wp-caption-text').text());
//	});

 });