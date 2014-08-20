/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


!function ($) {
    $(function(){
	$(".okcookie").click(function(e){
	    e.preventDefault();
	    $.ajax({
		type : 'POST',
                data : {
		    action : 'okcookie',
		    sec : cookie_sec,
                },
                url : ajaxurl,
                success: function(data) {
		    if(data.cookies){
			$('.cookieinfo').hide();
		    }
                }
            });
	});
    })
}(window.jQuery)