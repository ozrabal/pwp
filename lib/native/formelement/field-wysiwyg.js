//wylaczenie edytora na czas przenoszenia
//funkcja eksperymentalna
var _triggerAllEditors = function( event, creatingEditor ) {
    var postbox, textarea;
    postbox = jQuery(event.target);
    textarea = postbox.find('textarea.wp-editor-area');
    textarea.each(function(index, element) {
        var editor, is_active;
        editor = tinyMCE.EditorManager.get(element.id);
        is_active = jQuery(this).parents('.tmce-active').length;
        if (creatingEditor) {
            if (!editor && is_active) {
                tinyMCE.execCommand('mceAddControl', true, element.id);
            }
        } else {
            if (editor && is_active) {
                editor.save();
                tinyMCE.execCommand('mceRemoveControl', true, element.id);
            }
        }
    });
};
jQuery('#poststuff').on('sortstart', function(event) {
    _triggerAllEditors(event, false);
}).on('sortstop', function(event) {
    _triggerAllEditors(event, true);
});