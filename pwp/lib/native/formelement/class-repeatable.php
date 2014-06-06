<?php
class Formelement_Repeatable extends Formelement{
    protected
	$type = 'repeatable',
	$request,
        $body = null,
        $iter = -1
    ;
    
    function enqueue_media_repeatable() {
	wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ) );
	wp_enqueue_script( 'field-image', plugins_url( '/field-image.js', __FILE__ ), array( 'jquery'), PWP_VERSION );
	//wp_enqueue_script( 'field-repeatable', plugins_url( '/field-repeatable.js', __FILE__ ), array( 'jquery-ui-sortable' ),PWP_VERSION,true );
    }




    public function __construct( $form, $name ) {
       
        //$name = $name.'[1]';
       
        //add_action('wp_enqueue_scripts',array('Formelement_Repeatable', 'enqueue_media_repeatable'));
     
//	$v = get_post_meta($post->ID,'pwp_form',true);
//dump($v);
////$this->options = $v;
//	$form->options = $v;
//        
//dump($form->options);
if(is_admin()){

     //wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ) );
       // wp_enqueue_script( 'field-repeatable', plugins_url( '/field-repeatable.js', __FILE__ ), array( 'jquery' ),PWP_VERSION );
add_action( 'init', array($this, 'enqueue_media_repeatable') );


}else{
    add_action( 'init', array($this, 'enqueue_media_repeatable') );
   //wp_enqueue_script('field-repeatable',  plugins_url( '/field-repeatable.js', __FILE__ ), array( 'jquery','jquery-ui-sortable' ),PWP_VERSION,true  );
      
     // wp_enqueue_script( 'field-repeatable');
      //wp_enqueue_script('jquery-ui-sortable');
}

 
        parent::__construct( $form, $name );
        
//       $this->p = $this->form->get_name().'['.$this->get_name().']';
//         $this->set_name($this->p);
//        //echo __METHOD__;
    }

    public function set_options($params){
        
	return $this->add_elements($params);
    }


    public function add_elements($params){
        //$this->iter = 1;
        ///dump($params);
       //$this->set_name($this->form->get_name().'['.$this->get_name().']'.'['.$this->iter.']');

                

       
	$element_count = 0;
	//dump($this->form->options[$this->get_name()]);
	

	if(($this->form instanceof Options )  && isset($this->form->options[$this->get_name()])){
//dump(($this->form->options[$this->get_name()]));
	$element_count = count($this->form->options[$this->get_name()]);


	}

	if($element_count == 0){
	    $element_count = 1;
	}

       for($i=1; $i <= $element_count; $i++){
        $this->iter += 1;
	
         foreach($params as $element){

	    $added = $this->add_element($element['type'],$element['name']);
            if($added && isset($element['params']) && is_array($element['params'])){
                foreach($element['params'] as $param => $value){
                    // np set_class()
                   
                    $this->elements[$this->iter][$element['name']]->{'set_'.$param}($value);
                 
                }
            }else{
                echo ('Nieznany typ pola: '.$element['type']);
            }
           
            
//            if($this->get_request() && isset($element['validator'])){
//                
//            $this->elements[$element['name']]->set_validator($element['validator']);
//        }
            
	}
       
       }
        
        return $this;
    }


    

    public function add_element($type, $name){
       
        
        $type = 'Formelement_'.ucfirst($type);
        if( class_exists( $type )){
            
            $this->elements[$this->iter][$name] = new $type($this, $name);
            
            return $this->elements[$this->iter][$name];
        }
        return false;
        
    }
    
    public function render(){
//dump($this->elements);
            
          //dump($this->get_value());  
            
//$old = $this->get_name();

$this->p = $this->form->get_name().'['.$this->get_name().']';
$this->set_name($this->p);

$this->body .= $this->get_before().  $this->get_label();

$this->body .=  '<div '.$this->cssclass().'>';


//dump($this->elements);

if(isset($this->elements)){

    


if(  is_admin()){
$this->body .= '<table class="meta ds-input-table repeatable"><tbody class="ui-sortable">'; 
}else{
    $this->body .='<div '.$this->set_class('ui-sortable repeatable ')->cssclass().'>';
}


$a = $this->get_value();
if($a < 1){
    $a = 1;
}


//dump($this);

for($i = 0;$i<count($a);$i++){
    if(  is_admin()){
    $this->body .= '<tr class="row sortable-item repeatable-item inline-edit-row  quick-edit-row  alternate"><td class="order "><div class="dashicons dashicons-menu"></div></td><td>';
    $this->body .= $this->get_title('<h4>%s</h4>');
    
    }else{
        $this->body .='<div class="order sortable-item repeatable-item"><a class="order"><span class="glyphicon glyphicon-resize-vertical"></span>';
        $this->body .= $this->get_title('<span class="repeatable-title">%s</span>');
        $this->body .= '</a>';
    }
    
    //dump($this->elements);
   // foreach($this->elements as $el){

         foreach ($this->elements[0] as $element){
             //dump($element);
             
             $n = $element->get_name();
             //dump($n);
              $element->set_name(''.$i.']['.$n.'');
              $element->set_id($n.'_'.$i);
              $element->label->set_for($n.'_'.$i);
               //$element->set_name('['.$i.']['.$element->get_name().']');
              if(isset($a[$i][$n])){
               $element->set_value($a[$i][$n]);
              }
        $this->body .= $element->render();
        $element->set_name($n);
         }
    //}
         if(  is_admin()){
          $this->body .= '</td><td class="remove"><a class="repeatable-remove dashicons dashicons-no" href="#"></a></td></tr>';
         }else{
             $this->body .= '<a class="repeatable-remove dashicons dashicons-no" href="#"><span class="glyphicon glyphicon-minus"></span></a></div>';
         }

	
         $this->body .= $this->get_comment('<p class="description">%s</p>');
         $this->body .= $this->get_after();

}
    
    
//    
//    //dump($this->elements[$i]);
//foreach($this->elements as $el){
//   
//    //dump($el[0]->get_name());
//        foreach($this->elements as $iter => $el){
//$this->body .= '<tr class="row sortable-item inline-edit-row  quick-edit-row  alternate"><td class="order "><div class="dashicons dashicons-menu"></div></td><td>';
//	    
////$this->body .= '<input type="button" class="button add-image" value="Add image" rel="'.$iter.'" />';
//          foreach ($el as $element){
//	      //dump($this->get_name());
////dump($old);
//
//	      //dump($this->form->options[$old][$iter][$element->get_name()]);
//if(isset($this->form->options[$old][$iter][$element->get_name()])){
//	      $element->set_value($this->form->options[$old][$iter][$element->get_name()]);
//}
//
//              $element->set_name(''.$iter.']['.$element->get_name().'');
//
//
//
//
//	      if($this->form->get_request()){
////dump($this->get_name());
//            $this->set_value($this->form->get_request( $this->get_name() ) );
//
//	    
//
//            if(isset($this->validator)){
//
//                $this->validate($this->form->get_request( $this->get_name() ), $this->validator);
//            }
//        }
//
//          $this->body .= $element->render();  
//          }
//          
//          
//          
//          $this->body .= '</td><td class="remove"><a class="repeatable-remove dashicons dashicons-no" href="#"></a></td></tr>';
//        }
//}
        //return $this->name();
        if(  is_admin()){
      $this->body .= '</tbody></table>';  
       //$this->body .=  '<ul class="hl clearfix ds-repeater-footer ><li class="right ">';
        }else{
            $this->body .= '</div>';
        }
	$this->body .=  '<a href="#" class="repeatable-add button "><span class="pwp-icon dashicons dashicons-plus"></span> Add set</a>';

        
        
     }else{
   $this->body = '<div class="pwp-error"><p class="description">&nbsp;Nie zadeklarowano zawartości pola: '.$this->get_title().'</p></div>';
}
	//$this->body .=  '</li></ul>';
        
        $this->body .= '</div>';
        //$this->body .='<script type="text/javascript">alert(\'dupa\');</script>';
        return $this->body;
    }
    
    
    
    
        public function nowszy_render(){

            
          dump($this->get_value());  
            
$old = $this->get_name();

$this->p = $this->form->get_name().'['.$this->get_name().']';
$this->set_name($this->p);
$this->body .=  '<div class="dsslider_manager_extras">';

$this->body .= '<table class="meta ds-input-table"><tbody class="ui-sortable">'; 



        foreach($this->elements as $iter => $el){
$this->body .= '<tr class="row sortable-item inline-edit-row  quick-edit-row  alternate"><td class="order "><div class="dashicons dashicons-menu"></div></td><td>';
	    
//$this->body .= '<input type="button" class="button add-image" value="Add image" rel="'.$iter.'" />';
          foreach ($el as $element){
	      //dump($this->get_name());
//dump($old);

	      //dump($this->form->options[$old][$iter][$element->get_name()]);
if(isset($this->form->options[$old][$iter][$element->get_name()])){
	      $element->set_value($this->form->options[$old][$iter][$element->get_name()]);
}

              $element->set_name(''.$iter.']['.$element->get_name().'');




	      if($this->form->get_request()){
//dump($this->get_name());
            $this->set_value($this->form->get_request( $this->get_name() ) );

	    

            if(isset($this->validator)){

                $this->validate($this->form->get_request( $this->get_name() ), $this->validator);
            }
        }

          $this->body .= $element->render();  
          }
          
          
          
          $this->body .= '</td><td class="remove"><a class="repeatable-remove dashicons dashicons-no" href="#"></a></td></tr>';
        }
        //return $this->name();
        
      $this->body .= '</tbody></table>';  
       //$this->body .=  '<ul class="hl clearfix ds-repeater-footer ><li class="right ">';

	$this->body .=  '<a href="#" class="repeatable-add button "><span class="wp-media-buttons-icon dashicons dashicons-plus"></span>Add set</a>';

        
        
        
	//$this->body .=  '</li></ul>';
        
        $this->body .= '</div>';
        
        return $this->body;
    }
    
    public function stary_render(){
         //dump($this->form->get_request());
         //parent::render();
//dump($this->name);
//dump($this->form->options);


$old = $this->get_name();

$this->p = $this->form->get_name().'['.$this->get_name().']';
$this->set_name($this->p);
$this->body .=  '<div class="dsslider_manager_extras">';

$this->body .= '<table class="meta ds-input-table"><tbody class="ui-sortable">'; 

dump($this->form->options[$old][2]);

dump(count($this->elements));



array_push($this->elements, $this->elements[0]);

//$this->elements[2] = $this->elements[1];

//dump($this->elements[2]);


        foreach($this->elements as $iter => $el){
$this->body .= '<tr class="row"><td class="order"><div class="dashicons dashicons-menu"></div></td><td>';
	    

          foreach ($el as $element){
	      //dump($this->get_name());
//dump($old);

	      //dump($this->form->options[$old][$iter][$element->get_name()]);

	      $element->set_value($this->form->options[$old][$iter][$element->get_name()]);


              $element->set_name(''.$iter.']['.$element->get_name().'');




	      if($this->form->get_request()){
//dump($this->get_name());
            $this->set_value($this->form->get_request( $this->get_name() ) );

	    

            if(isset($this->validator)){

                $this->validate($this->form->get_request( $this->get_name() ), $this->validator);
            }
        }

          $this->body .= $element->render();  
          }
          
          
          
          $this->body .= '</td><td class="remove"><a class="repeatable-remove button" href="#"><div class="dashicons dashicons-no"></div></a></td></tr>';
        }
        //return $this->name();
        
      $this->body .= '</tbody></table>';  
        //$this->body .=  '<ul class="hl clearfix ds-repeater-footer"><li class="right">';

	$this->body .=  '<a href="#" class="repeatable-add ds-button">Add set</a>';

	//$this->body .=  '</li></ul>';
        
       // $this->body .= '</div>';
        
        return $this->body;
    }
       
    
    public function set_request($request){

        if(isset($request[$this->name])){
            $this->request = $request[$this->name];
        }
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
}



//class Formelement_Repetable extends Formelement{
//    protected 
//            $type = 'repetable',
//            $request = false,
//            $options = array(),
//            $iterator = 0
//	    ;
//
//    
//    public function addElement(Array $element) {
//	$type = 'Formelement_'.ucfirst($element['type']);
//        if( class_exists( $type )){
//            
//            $this->elements[$element['name']] = new $type($this, $element['name']);
//            return $this->elements[$element['name']];
//        }
//        //return false;
//    }
//   public function repeat(){
//        
//        $this->options[] = $this->options[0];
//        
//        
//        
//        
//        dump($this->options);
//        //echo __METHOD__;
//    }
//
//       public function get_name(){
//           return $this->form->get_name().'['.  parent::get_name().']['.$this->iterator.']';
//           ++$this->iterator;
//       }
//
//    public function set_options(Array $options){
//            
//            
//           foreach($options as $key=>$element){
//               
//               
//        //$n = $this->get_name();
//            
//            //$this->set_name($this->form->get_name().'['.$this->get_name().']['.$this->iterator.']');
//               
//               $this->options[$this->iterator][] = $this->addElement($element);
//               $this->iterator++;
//               
//           }
//            
////	foreach($options as $element ){
////
////            //$opt = new Formelement_Option($this->form, $name, $this);
////
////	    $opt = $this->addElement($element);
////
////            //$opt->set_value($value);
////            $this->options[$this->iterator][] = $opt;
////            $this->iterator++;
////                        $this->options[$this->iterator][] = $opt;
////
////        }
////        //$this->iterator++;
//        
//        
//    }
//
//public function get_options(){
//        $r = null;
//	foreach($this->options as $k => $o){
//	   // dump($k);
//foreach($o as $option){
//    //$option->form = null;
//    //$option->set_name('dupa');
//    //dump($option);
//            $r .= $option->render();
//            
//}
//        }
//        return $r;
//    }
//
//    public function set_request($request){
//
//        if(isset($request[$this->name])){
//            $this->request = $request[$this->name];
//        }
//    }
//
//    public function get_request($value = null){
//
//        //if podany klucz zwraca value else zwraca array
//        if(!empty($value)){
//            if(isset($this->request[$value])){
//                return $this->request[$value];
//            }
//            return false;
//        }
//        return $this->request;
//    }
//
//public function render() {
//    foreach($this->options as $option){    
//    //$this->options = $option;
//        foreach($option as $opt){
//            //dump($opt);
//            dump($this->get_name());
//            parent::render();
//
//        //return $opt->render();
//        return '<br>'.$this->get_options();
//    }
//    }
//}
//
//
//    
//    
//public function aaa_render() {
//    foreach($this->options as $option){    
//    //$this->options = $option;
//        parent::render();
//dump($option);
//
//        return '<br>'.$this->get_options();
//    }
//}
//    public function zrender() {
//	parent::render();
//
//	//unset($this->form);
//	//dump($this);
//
//	return $this->get_label();
//
//	
//	
//    }
//    
//}