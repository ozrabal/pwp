<?php
class Formelement_File extends Formelement{
    protected $type = 'file';



    public function __construct($form, $name){
	dump($_FILES);
   dump($_POST);
   dump($form->get_name());
   //die();
	if(isset($_FILES[$form->get_name()])){

	$this->validate_setting($form->get_name());
	}
	parent::__construct($form, $name);


	//die();
    }

public function validate_setting($s) {
    $keys = array_keys($_FILES);
    $i = 0;
    require_once ( ABSPATH . 'wp-admin/includes/file.php' );
    foreach ( $_FILES as $image ) {
$im = array();
	foreach($image as $param => $val){
	    dump($val);
	   $im[$param] = $val['plik'];
	}

	// if a files was upload
	dump($im);
	//if ($image['size']) {

$upload_overrides = array( 'test_form' => false );
 
    // save the file, and store an array, containing its location in $file
    $file = wp_handle_upload( $im ,$upload_overrides);

   dump($file);

	die();
	//}

	}

}

	
    



    public function render(){

        
             return  $this->get_before().$this->get_label().'<input '.$this->id().$this->type().$this->name().$this->value().$this->cssclass().'/>'.$this->get_message().$this->get_comment('<p class="description">%s</p>').$this->get_after();

        
	//return '<input type="file" name="img">';
	
    }

    
}
