<?php

class Formelement_Attachment extends Formelement{
    protected $type = 'attachment';

    public function __construct( $form, $name) {
	add_action('init', array($this,'enqueue_scripts'));
        add_action('admin_init', array($this,'enqueue_scripts'));
	parent::__construct( $form, $name );
    }

    function enqueue_scripts() {
	wp_enqueue_script( 'field-image', plugins_url( '/field-image.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
    }

    private function get_filename( $id = null ) {
	if( $id ){
	    $att = get_post( $id );
	    $att_file = explode('/',$att->guid);
	    $filename = array_pop($att_file);
	    return $filename;
	}
	return null;
    }

    
    public function render() {
        parent::render();
        wp_enqueue_media();
	$this->set_class('field-box');
        
        
        $body =  $this->get_before() . $this->get_label();
        $body .= '<div '.$this->cssclass().'>';
        $body .= '<a class="button button-secondary open-media-button" '.$this->get_data().'  id="open-media-modal'.$this->get_id().'" ><span class="pwp-icon dashicons  dashicons-admin-media"></span> '.__( 'Add / Change file', 'pwp' ).'</a>';
        $body .= '<a class="button button-secondary remove-media-button" id="remove-media'.$this->get_id().'" ><span class="pwp-icon dashicons dashicons-dismiss"></span> '.__( 'Remove file', 'pwp' ).'</a>';

        $body .= '<div id="m_open-media-modal' . mt_rand().$this->get_id().'" class="attachment-fieldset open-media-modal'.$this->get_id().'">';
        $body .= '<input type="hidden" ' . $this->set_id('attachment-id')->id() . $this->name() . $this->value() . '>';
	$body .= '<input type="text" class="large-text disabled-noframe" disabled="disabled"' . $this->set_id('attachment-filename')->id() . $this->name() . ' value="'.$this->get_filename($this->get_value()).'">';
        //$body .= '<div class="dashicons dashicons-format-image"></div>';
	//$body .= '<img class="slide-image" id="attachment-src" data-src-default="'.$this->get_thumbnail().'" src="'.$this->get_thumbnail( $this->get_value() ).'" />';
	$body .= '</div>';
        $body .= $this->get_message();
        $body .= '</div>';
        $body .= $this->get_comment('<p class="description">%s</p>');
        $body .= $this->get_after();
        return $body;

    }

}