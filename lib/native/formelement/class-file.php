<?php

class Formelement_File extends Formelement {

    protected $type = 'file';

    public function __construct( $form, $name ) {

	parent::__construct( $form, $name );
    }

    public function on_submit() {
	if( isset( $_FILES[$this->get_name()] ) && $_FILES[$this->get_name()]['name'] != '' ) {
	    require_once ( ABSPATH . 'wp-admin/includes/file.php' );
	    $upload_overrides = array( 'test_form' => false );
	    $file = wp_handle_upload( $_FILES[$this->get_name()] ,$upload_overrides);
	    if( isset( $file['file'] ) ) {
		$_POST[$this->get_name()] = $file;
		$this->form->set_request($_POST);
	    }
	}
    }

    public function render() {

	return  $this->get_before() . $this->get_label() . '<input ' . $this->id() . $this->type() . $this->name() . $this->value() . $this->cssclass() . '/>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }
}