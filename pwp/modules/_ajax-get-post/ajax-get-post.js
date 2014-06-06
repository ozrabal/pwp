!function ($) {
    $(function(){
       
	
        $('#content').on('click','.ax-get', function(event) {
            event.preventDefault();
            //alert(this);
            var url = $(this).attr('href');
            
            
            
            
           
                $.get(url + '?ajax=true', function(data){ 
                    $('#content').slideUp('slow');
                    $('#content').html(data).slideDown('slow');
                    
                    //$(data).insertBefore('#content').slideDown('slow');
                    
                    //add history browser state and url into url bar
                    var stateObj = { foo: 1000 + Math.random()*1001 };
                    history.pushState(stateObj, "ajax page loaded...", url);
                    
                });
            
	});
    })
}(window.jQuery)