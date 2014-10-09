<?php

/**
   * Form class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
abstract class Form {

    public
        $elements,
        $body;

    private 
        
        $action;
    
    protected 
        $render_after_submit = true,
        $request = false,
        $errors = false;

    /**
     * konstruktor
     * @param array $params
     */
    public function __construct(  $params ) {

	if( !is_array( $params ) ) {
	    return false;
	}

	$this->set_name( $params['name'] );
	
	if( filter_input( INPUT_POST, '_' . $this->get_name() . '_name' ) ) {
	    $this->set_request( filter_input_array( INPUT_POST ) );
        }
	$this->set_params( $params );
    }

    /**
     * ustawia nonce do wysylki
     * @param string $name
     */
    private function set_nonce( $name ) {
	$this->_nonce = wp_create_nonce( $name );
    }

    /**
     * pobiera nonce
     * @return string
     */
    protected function get_nonce() {
	if( !empty( $this->_nonce ) ) {
	    return $this->_nonce;
	}
    }

    /**
     * dodaje do formularza ukryte pole nonce _{nazwa formularza}_nonce
     */
    protected function add_nonce_field() {
	$this->addElement( 'hidden', '_' . $this->get_name() . '_nonce' );
	$this->elements['_' . $this->get_name() . '_nonce']->set_value( $this->get_nonce() );
    }

    /**
     * dodaje do formularza ukryte pole name _{nazwa formularza}_name
     */
    protected function add_formname_field() {
	if( !is_admin() ) {
	    $this->addElement( 'hidden', '_' . $this->get_name() . '_name' );
	    $this->elements['_' . $this->get_name() . '_name']->set_value( $this->get_name() );
	}
    }

    /**
     * ustawia paramerty pola formularza
     * @param array $element
     */
    private function set_element_params( $element ) {

	foreach( $element['params'] as $param => $value ) {
	    $this->elements[$element['name']]->{ 'set_' . $param }( $value );
        }
    }

    /**
     * ustawia validator dla elementu
     * @param array $element
     */
    private function set_element_validator( $element ) {
	if( $this->get_request() && isset( $element['validator'] ) ) {
	    $this->elements[$element['name']]->set_validator( $element['validator'] );
	}
    }

    /**
     * ustawia parametry poszczegolnych elementow formularza
     * @param array $params
     */
    protected function set_params( $params ) {
        
	$this->set_method( $params );

        if( isset( $params['elements'] ) ) {
	    foreach( $params['elements'] as $element ) {
		$added = $this->addElement( $element['type'], $element['name'] );
		if( $added && isset( $element['params'] ) && is_array( $element['params'] ) ) {
		    $this->set_element_params( $element );
		}
		$this->add_formname_field();
		$this->set_element_validator( $element );
	    }
	}
    }

    /**
     * ustawia wartosci bledow
     * @param mixed|array|object $e
     */
    public function set_errors( $e ) {
        $this->errors = $e;
    }

    /**
     * zwraca bledy
     * @return mixed|array|object
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * __call
     * @param string $name
     * @param array $arguments
     * @return Form
     */
    public function __call( $name, $arguments ) {
        dbug( 'Klasa ' . __CLASS__ . ' nie posiada metody ' . $name . print_r( $arguments ) );
        return $this;
    }

    /**
     * wrapper dla addElement
     * @param string $type
     * @param string $name
     * @return Formelement
     */
    public function add_element( $type, $name ) {
	return $this->addElement( $type, $name );
    }


    /**
     * dodaje element do formularza, inicjuje obiekt
     * @param string $type
     * @param string $name
     * @return Formelement
     */
    public function addElement( $type, $name ) {
	$type = 'Formelement_' . ucfirst( $type );
        if( class_exists( $type , true ) ) {
            $this->elements[$name] = new $type( $this, $name );
            return $this->elements[$name];
        }
	dbug( 'Nieznany typ pola: ' . $type . 'w formularzu ' . $this->get_name() );
	return false;
    }

    /**
     * ustawia wewnetrzna zmienna request z POST
     * @param array $request
     */
    public function set_request( $request ) {

        if( isset( $request[$this->name] ) ) {
            $this->request = $request[$this->name];
        } else {
	    if( isset( $request['_' . $this->get_name() . '_name'] ) && $request['_' . $this->get_name() . '_name'] == $this->name ) {
		$this->request = $request;
	    }
	}
    }

    /**
     * okresla czy jestemy w ekranie edycji (postu, taxonomii..)
     * @return boolean
     */
    protected function is_edit(){
	if( filter_input( INPUT_GET, 'action' ) == 'edit' ) {
	    return true;
        }
        return false;
    }

    /**
     * pobiera caly request lub konkretna wartosc
     * @param string $value
     * @return boolean
     */
    public function get_request( $value = null ) {
        
        //if podany klucz zwraca value else zwraca array
        if( !empty( $value ) ) {
            if( isset( $this->request[$value] ) ) {
                return $this->request[$value];
            }
            return false;
        }
        return $this->request;
    }

    /**
     * ustawia tytul formularza
     * @param string $title
     * @return \Form
     */
    public function set_title( $title ) {
        $this->title = $title;
        return $this;
    }

    /**
     * zwraca tytul formularza i dekoruje tagami html
     * @param string $tag
     * @return string
     */
    public function get_title( $tag = '%s' ) {
        if( isset( $this->title ) )
        return sprintf( $tag, $this->title );
    }

    /**
     * ustawia nazwe formularza
     * @param type $name
     * @return \Form
     */
    public function set_name( $name ) {
        $this->name = sanitize_key( $name );
	return $this;
    }

    /**
     * zwraca nazwe formularza (slug)
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * ustawia akcje formularza
     * @param string $action
     * @return \Form
     */
    public function set_action( $action = null ) {
	$this->action = $action;
        return $this;
    }

    /**
     * zwraca akcje formularza
     * @return string
     */
    public function get_action() {
	return $this->action;
    }

    /**
     * ustawia metode wysylki formularza (POST/GET)
     * @param array $params
     * @return \Form
     */
    public function set_method( $params ) {
	if( isset( $params['method'] ) && in_array( $params['method'], array( 'POST', 'GET' ) ) ) {
	    $this->method = $params['method'];
	} else {
	    $this->method = 'POST';
	}
	return $this;
    }

    /**
     * zwraca metode wysylki formularza
     * @return string
     */
    public function get_method() {
	return $this->method;
    }

    /**
     * renderuje html formularza
     * @return string
     */
    public function render() {
    
	$this->body = '<form method="' . $this->get_method() . '" name="' . $this->get_name() . '" enctype="multipart/form-data"  action="' . $this->get_action() . '">';
	foreach ($this->elements as $element){
            $element->valid();
	    $this->body .= $element->render();
	}
	$this->body .= wp_nonce_field( 'form_' . $this->get_name(), '_' . $this->get_name() . '_nonce', true, 0 );
	$this->body .= '</form>';
	return $this->body;
    }

    /**
     * drukuje formularz
     */
    public function print_form() {
        echo $this->body;
    }

    /**
     * wyswietla komunikat o wyslaniu formularza
     */
    public function submit() {
	//dump($_FILES);

	//if(isset($_FILES)){
	    foreach($this->elements as $element){
		if(method_exists($element, 'on_submit')){
		    $element->on_submit();
		}
	    }
	    //dump($this->elements);

	//}
	//die();
        //echo '<div class="alert alert-success">' . __( 'Form send', 'pwp' ) .'</div>';
    }
}