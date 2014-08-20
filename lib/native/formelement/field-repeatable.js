//jQuery(function(jQuery) {
//jQuery('.repeatable-add').click(function() {
//	field = jQuery(this).closest('td').find('.custom_repeatable li:last').clone(true);
//	fieldLocation = jQuery(this).closest('td').find('.custom_repeatable li:last');
//	jQuery('input', field).val('').attr('name', function(index, name) {
//		return name.replace(/(\d+)/, function(fullMatch, n) {
//			return Number(n) + 1;
//		});
//	})
//	field.insertAfter(fieldLocation, jQuery(this).closest('td'))
//	return false;
//});
//
//jQuery('.repeatable-remove').click(function(){
//	jQuery(this).parent().remove();
//	return false;
//});
//	
//jQuery('.ui-sortable').sortable({
//	opacity: 0.6,
//	revert: true,
//	cursor: 'move',
//	handle: '.order'
//});
//
//
//});

//
//
//jQuery(document).ready(function($) {
//$(".ui-sortable").sortable({items: 'tr'});
//
//
//// Return a helper with preserved width of cells
//
//
//var fixHelper = function(e, ui) {
//	ui.children().each(function() {
//		$(this).width($(this).width());
//	});
//	return ui;
//};
//
//$(".ui-sortable").sortable({
//    items: 'tr',
//	helper: fixHelper
//});
//
//});



//
//
//////alert('repetable');
//
//
jQuery(function(jQuery) {
	// Add media to post
//	jQuery('.add-image').click(function() {
//		formID = jQuery(this).attr('rel');
//		formfield = jQuery(this).siblings('.upload_image');
//		preview = jQuery(this).siblings('.preview_image');
//		tb_show('Choose Image', 'media-upload.php?post_id=' + formID + '&type=image&TB_iframe=1');
//		window.orig_send_to_editor = window.send_to_editor;
//		window.send_to_editor = function(html) {
//			imgurl = html.match(/<img.*?src="(.*?)"/);
//			id = html.match(/wp-image-(.*?)"/, '');
//			formfield.val(id[1]);
//			preview.attr('src', imgurl[1]);
//			tb_remove();
//			window.send_to_editor = window.orig_send_to_editor;
//		}
//		return false;
//	});

	// Delete uploaded image
//	jQuery('.remove-image').click(function() {
//		var defaultImage = jQuery(this).parent().siblings('.default_image').text();
//
//		jQuery(this).parent().siblings('.upload_image').val(''); // Clean up the values
//		jQuery(this).parent().siblings('.preview_image').attr('src', defaultImage); // Clean up the values
//		return false;
//	});

	// Add repeatable row
//	jQuery('.repeatable-add').click(function() {
//	   
//		// Set min of rows
//		//var minNumberOfProducts = 2;
//		var uniqueID = document.querySelectorAll('.row').length;
//
//		// Declared variable
//		var row = jQuery(this).closest('.ui-sortable').find('tbody tr.row:last-child');
//		var clone = row.clone(true);
//
//		// Find the cloned fields and reset the values of it
//		//clone.find('input[type=text], text, textarea, select, input.upload_image').val(''); // Reset the values
//		//clone.find('.preview_image').attr('src', ''); // Reset the values
//
//		// Add new attribute to element
//		//jQuery('.row').attr('id', 'repeatable-[' + uniqueID + ']');
// //alert(uniqueID);
//console.log(clone);
//		row.insertAfter(clone);
//
//		//
//		return false;
//	});

 ile = jQuery('.repeatable-item').length;
	   

function set_default_inputs(field){
    jQuery(field).find('input[type=text], input[type=hidden], text, textarea, select, checkbox').val(''); // Reset the values

    //def =  field.find('img').attr('data-src-default');
    jQuery(field).find('img').attr('src',function(){
	return jQuery(this).attr('data-src-default');
    });

}


jQuery('.repeatable-add').click(function(e) {
    e.preventDefault();

    f = jQuery(this).parent().find('.repeatable-item:last-child').append('<hr class="this">');


    jQuery('.repeatable-remove',f).removeClass('disable');

    field = f.clone();
	//field = jQuery('.repeatable').find('.repeatable-item:last-child').clone();
	fieldLocation = jQuery(this).parent().find('.repeatable-item:last-child');
        set_default_inputs(field);


var id=1;


//	field.find('div').attr('id', function(index, id) {
//		return name.replace(/(\d+)/, function(fullMatch, n) {
//			return Number(n) + 1;
//		});
//	})
        
        
        
        //alert(fieldLocation.length)
        
        
        //field.insertAfter('sss');
        
	field.insertAfter(fieldLocation);
        //renumber(ui.item).




 ile = jQuery(this).find('.repeatable-item').length;
console.log(jQuery(this));
    renumber(this);


	b = '.open-media-button';
 d = jQuery(b).next();

 jQuery(ds.media.init);
	
	return false;
});




	// Remove repeatable row
	jQuery(document).on('click','.repeatable-remove',function(e){
	    e.preventDefault();
	     ile = jQuery(this).parent().parent().parent().find('.repeatable-item').length;
            console.log(ile);
            //alert(ile);
            //exit;
if(ile >1){
	    //jQuery('.ui-sortable').closest('.ui-sortable').find('.row:last-child').remove();
            
            var v = jQuery(this).closest('table');
            
                jQuery(this).closest('.repeatable-item').remove();
                
                //jQuery(v).addClass('this')
		console.log(jQuery(this).closest('table').addClass('this'));
                renumber(v);
		return false;
}else{
    //jQuery('.repeatable-item').find('input[type=text], input[type=hidden], text, textarea, select, checkbox').val(''); // Reset the values
       // jQuery('.repeatable-item').find('img').attr('src','');
jQuery(this).addClass('disable');
		set_default_inputs('.repeatable-item')
		return false;
}
	    });
        
       //var list = jQuery('.ui-sortable'); 
//function updateNames(list) {
//    list.each(function (idx) {
//        console.log(idx);
//        var inp = jQuery(this).find('input, textarea');
//        inp.each(function () {
//            this.name = this.name.replace(/(\[\d\])/, '[' + idx + ']');     
//            showNameAsValue(this)
//        })
//    });
//}
function showNameAsValue(el) {
    jQuery(el).html(el.name);
}


function renumber(item) {
  //tr = item[0].parentNode;
  
    console.log(jQuery(item).parent());
  
  jQuery(item).parent().find('.repeatable-item').each(function(index,element) {
    renumber_helper(index,element); 
    showNameAsValue(this)
  });
}

function renumber_helper(index,element) {
  inputs = jQuery('input', element);
  for (j = 0; j < inputs.length; j++) {
    input = inputs[j];
    name = input.name;
    input.name = name.replace(/(\d+)/,index);
  }
  textareas = jQuery('textarea',element);
  for (j = 0; j < textareas.length; j++) {
     textarea = textareas[j];
     name = textarea.name;
     textarea.name = name.replace(/(\d+)/,index);
  }
  selects = jQuery('select',element);
  for (j = 0; j < selects.length; j++) {
    select = selects[j];
    name = select.name;
    select.name = name.replace(/(\d+)/,index);
  }



  

  as = jQuery('a', element);
  for (j = 0; j < as.length; j++) {
    a = as[j];
    id = a.id;
    
    a.id = id.replace(/(\d+$)/,index);

    //console.log(id);
  }
  
  ass = jQuery('.attachment-fieldset', element);

  for (j = 0; j < ass.length; j++) {
    ab = ass[j];
    id = ab.id;

    ab.id = id.replace(/(\d+$)/,index);
    //console.log(id);
  }


}


jQuery('.ui-sortable-container').sortable({
	opacity: 0.6,
	revert: false,
	cursor: 'move',
	handle: '.order',
        update: function(event,ui){ renumber(ui.item); }
//        update: function (event, ui) {
//        updateNames(jQuery(this))
//    }
});


});