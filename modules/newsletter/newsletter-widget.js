
!function ($) {
    $(function(){
    
        $("#newsletter").submit(function(e){
            e.preventDefault();
            var form = $(this).serialize();
            $.ajax({
                type : 'POST',
                data : {
                    action : 'subscribe',
                    data: form 
                },
                url : pwpax.ajaxurl,
                beforeSend: function(){
                    //alert(form);
                },
                success: function(data) {

                    if(data.class == 'alert alert-success'){
                        $('#newsletter .form-main').hide();
                    }
                    $('#newsletter .alert').removeClass().empty().addClass(data.class).append(data.alert).show();
                }
            }); 
        })
    })
}(window.jQuery)