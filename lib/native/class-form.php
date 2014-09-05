<?php
/**
 * 
 */
abstract class Form {
    public 
        $elements,
        $body;

    private 
        $name='',
        $action;
    
    protected 
        $render_after_submit = true,
        $request = false,
        $errors = false;
    /**
     * 
     * @param array $params
     */
    public function __construct(  $params ) {

if(!is_array($params)){
    return FALSE;
}


	$this->set_name($params['name']);

	//$this->set_nonce('form_'.$this->get_name());


	if(filter_input(INPUT_POST, '_'.$this->get_name().'_name')){
        
	    $this->set_request(filter_input_array(INPUT_POST));
        }
                
        $this->set_params($params);
        
    /*           
       $this->render();
       
	//dump(count($this->request));

        if(count($this->request) > 1 && $this->get_errors() == false){
            //$this->render_after_submit = false;
            
            $this->submit();
            $this->body = '';
            
        }else if($this->get_errors()){
            $this->body = '<div class="alert alert-danger">'.__( 'In the form errors occurred', 'pwp' ).'</div>'.$this->body;
        }
        
        $this->print_form();
        */
    }

    private function set_nonce($name){

	$this->_nonce = wp_create_nonce($name);
	
    }





    protected function get_nonce(){
	if(!empty($this->_nonce)){
	    return $this->_nonce;
	}
}


    protected function add_nonce_field() {
	$this->addElement('hidden','_'.$this->get_name().'_nonce');

            $this->elements['_'.$this->get_name().'_nonce']->set_value($this->get_nonce());
    }

    protected function add_formname_field() {
	$this->addElement('hidden','_'.$this->get_name().'_name');
            $this->elements['_'.$this->get_name().'_name']->set_value($this->get_name());
    }



    protected function set_params( Array $params ){
	foreach($params['elements'] as $element){

	    $added = $this->addElement($element['type'],$element['name']);
	    //dump();

            if($added && isset($element['params']) && is_array($element['params'])){
                foreach($element['params'] as $param => $value){
                    // np set_class()
                    $this->elements[$element['name']]->{'set_'.$param}($value);
                
                }
            }else{
                echo ('Nieznany typ pola: '.$element['type']);
            }

	    //$this->add_nonce_field();
if(!is_admin()){
$this->add_formname_field();
}
            if($this->get_request() && isset($element['validator'])){
                
            $this->elements[$element['name']]->set_validator($element['validator']);
	    }
	}
    }
    
    public function set_errors($e){
        $this->errors = $e;
    }

    public function get_errors(){
        return $this->errors;
    }

    public function __call( $name, $arguments ) {
        print_r('Klasa '.__CLASS__.' nie posiada metody '.$name);
        return $this;
    }

    public function add_element($type, $name){
	return $this->addElement($type, $name);
    }

    public function addElement($type, $name) {
	$type = 'Formelement_'.ucfirst($type);
        if( class_exists( $type , true)){
            $this->elements[$name] = new $type($this, $name);
            return $this->elements[$name];
        }
        return false;
    }
    
    public function set_request($request){

        if(isset($request[$this->name])){
            $this->request = $request[$this->name];
        }else{
	    if(isset($request['_'.$this->get_name().'_name']) && $request['_'.$this->get_name().'_name'] == $this->name ){
		$this->request = $request;
	    }
	}
    }
    
    protected function is_edit(){
        if(isset($_GET['action']) && $_GET['action'] == 'edit'){
            return true;
        }
        return false;
    }
    
    public function get_request($value = null){
        
        //if podany klucz zwraca value else zwraca array
        if(!empty($value)){
            if(isset($this->request[$value])){
                return $this->request[$value];
            }
            return false;
        }
        return $this->request;
    }
    
    public function set_title($title){
        $this->title = $title;
    }
    public function get_title($tag = '%s'){
        if(isset($this->title))
        return sprintf( $tag ,$this->title);
    }
    public function set_name($name){
        $this->name = $name;
	return $this;
    }
    
    public function get_name(){
        return $this->name;
    }
    public function set_action($action){
	$this->action = $action;
        return $this;
    }
    public function get_action(){
	return $this->action;
    }
    
    public function render(){
        

	$this->body = '<form method="POST" name="'.$this->get_name().'" enctype="multipart/form-data"  action="">';

	foreach ($this->elements as $element){
            $element->valid();
	    $this->body .= $element->render();
	}

	$this->body .= wp_nonce_field( 'form_'.$this->get_name(), '_'.$this->get_name().'_nonce', true, 0 );

	$this->body .= '</form>';

	return $this->body;
        
    }
    
    public function print_form(){
        echo $this->body;
    }
    
    public function submit(){
        echo '<div class="alert alert-success">'.__( 'wysłano').'</div>';
    }
}


