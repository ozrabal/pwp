<?php

class Formelement_Wysiwyg extends Formelement {
    protected $type = 'wysiwyg';
    private $options = array('tinymce' => false);
    
    public function __construct( $form, $name ) {
	parent::__construct( $form, $name );
    }

    public function set_options($options){
        if(is_array($options)){
            $this->options = $options;
        }
    }
    
    public function get_id(){
        return str_replace(array('[',']'), '_', $this->name(false)) . 'e';
    }
    
    public function get_options(){
        
        return $this->options;
        
    }
    
    public function render() {
	if( $this->form instanceof Formelement_Repeatable){


	$this->set_comment(__('Nie można edytora w repeatable','pwp'));
	$this->set_before('<div class="pwp-error">');
	$this->set_after('</div>');
	$this->set_class('pwp-error');
	return $this->get_before().$this->get_label().$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();


    }
    
    

	parent::render();
	ob_start();

	echo"

<script>


var _triggerAllEditors = function(event, creatingEditor) {
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
			}
			else {
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



</script>

";

        
$this->options['textarea_name'] = $this->name(false);
	
wp_editor( $this->get_value(), $this->get_id(), $this->get_options() );
        
        
        
        $editor = ob_get_clean();
	//echo $this->get_name();

	return $this->get_before().$this->get_label().$editor.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();
//return $this->get_before().$this->get_label().$editor.'<textarea '.$this->id().$this->cssclass().'>'.$this->get_value().'</textarea>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();


    }


}