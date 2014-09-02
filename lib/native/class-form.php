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
    public function __construct( Array $params ) {
	$this->set_name($params['name']);

        
        //sprawdzac nonce
        dump(filter_input_array(INPUT_POST));
        echo '--------------';
        if(isset($_POST)){
        
        //if(isset($_REQUEST[$this->get_name()])){
            
            //@todo sprawdzic czy prawidlowy
            //$this->set_request($_REQUEST);
            
            
            $this->set_request(filter_input_array(INPUT_POST));
 
            //filter_input( INPUT_REQUEST, 'tag_ID', FILTER_SANITIZE_NUMBER_INT, FILTER_NULL_ON_FAILURE );
            dump($this->get_request());
            //die();
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
        
       // dump($this->get_request());
	$this->body = '<form method="POST" name="'.$this->get_name().'" enctype="multipart/form-data"  action="">';
//dump($this);
	foreach ($this->elements as $element){
            $element->valid();
            //$this->form->get_request();
           // dump($element->form);
            
            dump($element->form);
            dump( $element->valid());
       
            
           
	    $this->body .= $element->render();
	}

	$this->body .= '</form>';



	return $this->body;
        
    }
    
    public function print_form(){
      
        
        //dump($this->body);
        echo $this->body;
    }
    
    public function submit(){
       
        
        echo '<div class="alert alert-success">'.__( 'wysłano').'</div>';
        
    }
}


