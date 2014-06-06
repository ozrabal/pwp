
(function() {
   tinymce.create("tinymce.plugins.formcreator", {
      init : function(ed, url) {
	  ed.addButton("addform", {
         title : 'example.desc',
         image : '../jscripts/tiny_mce/plugins/example/img/example.gif',
         onclick : function() {
            //ed.windowManager.alert('Hello world!! Selection: ' + ed.selection.getContent({format : 'text'}));
	    ed.windowManager.open({
   url : '/wp-content/plugins/pwp/modules/contact/addelement.php',
   width : 800,
   height : 600,
   //resizable: "yes",
                              inline: true,
                              close_previous: "no",
                             // popup_css: false
}, {
   custom_param : 1
})
         }
      });

//	  ed.addButton('addform', {
//            title : 'Form insert',
//            image : url+'images/insert.png',
//            onclick : function() {
//               var posts = prompt("Typ pola", ("text","aaa"));
//               var text = prompt("List Heading", "This is the heading text");
//
//               if (text != null && text != ''){
//                  if (posts != null && posts != '')
//                     ed.execCommand('mceInsertContent', false, '[recent-posts posts="'+posts+'"]'+text+'[/recent-posts]');
//                  else
//                     ed.execCommand('mceInsertContent', false, '[recent-posts]'+text+'[/recent-posts]');
//               }
//               else{
//                  if (posts != null && posts != '')
//                     ed.execCommand('mceInsertContent', false, '[recent-posts posts="'+posts+'"]');
//                  else
//                     ed.execCommand('mceInsertContent', false, '[recent-posts]');
//               }
//
//
//
//            }
//         });


         ed.addButton('formcreator', {
            title : 'Form element insert',
            image : url+'images/insertelement.png',
            onclick : function() {
//               var posts = prompt("Typ pola", ("text","aaa"));
//               var text = prompt("List Heading", "This is the heading text");
//
//               if (text != null && text != ''){
//                  if (posts != null && posts != '')
//                     ed.execCommand('mceInsertContent', false, '[recent-posts posts="'+posts+'"]'+text+'[/recent-posts]');
//                  else
//                     ed.execCommand('mceInsertContent', false, '[recent-posts]'+text+'[/recent-posts]');
//               }
//               else{
//                  if (posts != null && posts != '')
//                     ed.execCommand('mceInsertContent', false, '[recent-posts posts="'+posts+'"]');
//                  else
//                     ed.execCommand('mceInsertContent', false, '[recent-posts]');
//               }

// triggers the thickbox
		var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
		W = W - 80;
		H = H - 84;
		tb_show( 'Form element', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=element-form' );
            }
         });
      },
      createControl : function(n, cm) {
         return null;
      },
      getInfo : function() {
         return {
            longname : "Form Creator",
            author : 'Piotr Łepkowski',
            authorurl : 'http://webkowski.com',
            infourl : 'http://webkowski.com',
            version : "0.1"
         };
      }
   });
   tinymce.PluginManager.add('formcreator', tinymce.plugins.formcreator);

   // executes this when the DOM is ready
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form = jQuery('<div id="element-form"><table id="element-table" class="aform-table">\
			<tr>\
				<th><label for="element-name">Name</label></th>\
				<td><input type="text" id="element-name" name="name" value="Nazwa" /><br />\
				<small><?php echo 'nazwa'; ?>Nazwa pola</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-type">Type</label></th>\
				<td><select name="type" id="element-type">\
					<option value="null">Wybierz typ elementu</option>\
					<option value="text">Text</option>\
					<option value="textarea">Textarea</option>\
					<option value="select">Select</option>\
					<option value="comment">Comment</option>\
					<option value="checkbox">Checkbox</option>\
					<option value="hidden">Hidden</option>\
					<option value="submit">Submit</option>\
				</select><br />\
				<small>Typ pola</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-value">Value</label></th>\
				<td><input type="text" name="value" id="element-value" value="" /><br /><small>Wartość, zawartość</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-options">Options</label></th>\
				<td><input type="text" name="options" id="element-options" value="" /><br />\
					<small>lista opcji pola select (oddzielona przecinkami) w formacie: etykieta|wartość</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-validator">Validator</label></th>\
				<td><input type="text" name="validator" id="element-validator" value="" /><br />\
					<small>lista validatorow (oddzielona przecinkami)</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-callback">Callback</label></th>\
				<td><input type="text" name="callback" id="element-callback" value="" /><br />\
					<small>Funkcja wywolywana dla ustawienia wartosci parametru (nazwa funcji,parametr)</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-container">Klasa CSS kontenera</label></th>\
				<td><input type="text" name="container" id="element-container" value="" /><br />\
					<small>Klasa CSS kontenera w którym zamkniety jest element wraz z etykietą</small></td>\
			</tr>\
			<tr>\
				<th><label for="element-label">Etykieta</label></th>\
				<td><input type="text" id="element-label" name="label" value="" /><br />\
					<small>Etykieta elementu</small>\
				</td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="formelement-submit" class="button-primary" value="Insert Gallery" name="submit" />\
		</p>\
		</div>');

		var table = form.find('table');
		form.appendTo('body').hide();

		// handles the click event of the submit button
		form.find('#formelement-submit').click(function(){
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = {
				'name':	'',
				'type': 'text',
				'value': '',
				'validator': '',
				'container': '',
				'label':'',
				'options': '',
				'callback': ''
				};
			var shortcode = '[field';

			for( var index in options) {
				var value = table.find('#element-' + index).val();

				// attaches the attribute to the shortcode only if it's different from the default value
				if ( value !== options[index] )
					shortcode += ' ' + index + '="' + value + '"';
			}

			shortcode += ']';

			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

			// closes Thickbox
			tb_remove();
		});
	}); 




})();