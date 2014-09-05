<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#advanced_dlg.colorpicker_title}</title>
	<script type="text/javascript" src="tiny_mce_popup.js?ver=358-20121205"></script>
	
	
</head>
<body id="colorpicker" style="" role="application" aria-labelledby="app_label">
	<span class="mceVoiceLabel" id="app_label" style="display:none;">{#advanced_dlg.colorpicker_title}</span>
<form onsubmit="insertAction();return false" action="#">
<?php

echo 'add';
echo '<div id="element-form"><table id="element-table" class="aform-table">\
			<tr>\
				<th><label for="element-name">Name</label></th>\
				<td><input type="text" id="element-name" name="name" value="Nazwa" /><br />\
				<small>Nazwa pola</small></td>\
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
			<input type="submit" id="formelement-submit" class="button-primary" value="Insert Gallery" name="submit" />\
		</p>\
		</div>';

?>
    <input id="color" value="cccccc"/>
</form>
    <div class="mceActionPanel">
	<input type="submit" id="insert" name="insert" value="{#apply}" />
		<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();"/>
	</div>

    <script>

//    // handles the click event of the submit button
//		form.find('#formelement-submit').click(function(){
//			// defines the options and their default values
//			// again, this is not the most elegant way to do this
//			// but well, this gets the job done nonetheless
//			var options = {
//				'name':	'',
//				'type': 'text',
//				'value': '',
//				'validator': '',
//				'container': '',
//				'label':'',
//				'options': '',
//				'callback': ''
//				};
//			var shortcode = '[field';
//
//			for( var index in options) {
//				var value = table.find('#element-' + index).val();
//
//				// attaches the attribute to the shortcode only if it's different from the default value
//				if ( value !== options[index] )
//					shortcode += ' ' + index + '="' + value + '"';
//			}
//
//			shortcode += ']';
//
//			// inserts the shortcode into the active editor
//			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
//
//			// closes Thickbox
//			tb_remove();
//		});




    </script>



</form>
</body>
</html>
        