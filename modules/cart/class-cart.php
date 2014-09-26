<?php
/**
   * Cart module class
   *
   * @package    PWP
   * @subpackage Cart
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */

class Cart extends Module{

    static $instance = null;

    /**
     * Inicjalizacja modulu
     * singleton
     * @return Cart
     */
    static function init() {
	if( is_null( self::$instance ) ) {
            self::$instance = new Cart();
	}
        return self::$instance;
    }


    public function __construct() {

	parent::__construct();

        self::register_post_type();
        self::register_metabox();
	$this->action_slug = 'cart';

	if( !session_id() ) {
	    Pwp::load_module( 'session' );
	}

	if( isset( $_SESSION['cart'] ) ) {
	    $this->items = $_SESSION['cart'];
	}

	$this->page = new Virtualpage();

	$this->route();

	wp_enqueue_style( 'dashicons' );
	add_action( 'init', array( $this, 'add_price_box' ) );
	add_action( 'the_content', array( $this, 'add_cart_button' ) );

	register_widget( 'Cart_Widget' );
    }

    public function register_post_type(){

	   $args = array(
            'labels'             => array(
		'name'               => __( 'Orders', 'pwp' ),
		'singular_name'      => __( 'Order', 'pwp' ),
		'add_new'            => __( 'Add New', 'pwp' ),
		'add_new_item'       => __( 'Add New Order', 'pwp' ),
		'edit_item'          => __( 'Edit Form', 'pwp' ),
		'new_item'           => __( 'New Form', 'pwp' ),
		'all_items'          => __( 'All Forms', 'pwp' ),
		'view_item'          => __( 'View Form', 'pwp' ),
		'search_items'       => __( 'Search Forms', 'pwp' ),
		'not_found'          => __( 'No forms found', 'pwp' ),
		'not_found_in_trash' => __( 'No forms found in Trash', 'pwp' ),
		'parent_item_colon'  => __( ':', 'pwp' ),
		'menu_name'          => __( 'Order', 'pwp' )
	    ),
            'public'             => false,
            'show_ui'            => true,
            'query_var'          => false,
            'supports'           => array( 'title', 'custom-fields' )
        );
	register_post_type( 'order', $args );


    }


        /**
     * rejestracja pol meta dla typu postu form
     */
    static function register_metabox(){

	$box = array(
            'name'      => 'pwp_order',
            'title'     => __( 'Order parameters', 'pwp' ),
            'post_type' => array( 'order' ),
            'elements'  => array(
                array(
                    'type' => 'text',
                    'name' => 'order_id',
                    'params'=> array(
                        'label' => __( 'Order id', 'pwp' ),
                        'class' => 'large-text',
                        'validator'=>array('notempty','email')

                    ),

                ),
		array(
                    'type' => 'orderitem',
                    'name' => 'order_item',
                    'params'=> array(
                        'label' => __( 'Order content', 'pwp' ),
                        'class' => 'large-text',
			
                        ),
                ),
               
              
               
                
                
		
            )
        );
        new Metabox( $box );
    }




    public function add_price_box(){


        $box = 
            array(
                'name'      => 'Shop',
                'title'     => __( 'Price', 'pwp' ),
                //'callback'=> '',
                'post_type' => array('post'),
                'elements'  => array(




                    array(
                        'type' => 'text',
                        'name' => 'price',
                        'params'=> array(
                            'label' => __( 'Cena', 'pwp' ),
                            'comment' => __('Cena podstawowa','pwp'),
			    'class'	    => 'small-text block'


                        ),
                    ),
                )
            
        );

            new Metabox( $box );

	
	
    }


    private function update_cart(){
	$_SESSION['cart'] = $this->items;
    }

   

    private function in_cart($item){

		foreach($this->items as $key => $cart_item){

		    
			if($cart_item->ID == $item->ID && $cart_item->price == $item->price){
//dump($key);
		    //dump($cart_item->ID);
		    
			    return $key;
			}


		}
		return null;
	}

    private function add_to_cart( $object){

	
  $object->subtotal = $object->qty * $object->price;


$object_index = $this->in_cart( $object );


//dump($object_index);

//die();

	    if(is_null($object_index)){


$this->items[]  = $object;

	    }else{

$this->items[$object_index]->qty +=  $object->qty;
$this->items[$object_index]->subtotal += $object->subtotal;

	    }

		


//dump($this->items);
//die();

	
	

	

$this->update_cart();
	//$_SESSION['cart'] = $this->items;
	//dump(count($_SESSION['cart']));
    }

    public function add_Action(){

	if ( wp_verify_nonce( $_POST['object_nonce'], 'object_'.$_POST['object_id'] ) ) {

	//dump($_POST);

	$object = get_post(intval($_POST['object_id']));

	if($object){

$price = get_post_meta( $object->ID, 'price', true );

	//dump($price);

	//$this->items[] = $object;


			$object->price = intval($price);

			$object->qty = intval($_POST['object_qty']);

		$product = new stdClass();
		$product->ID = $object->ID;
		$product->post_title = $object->post_title;
		$product->price = $object->price;
		$product->qty = $object->qty;


	$this->add_to_cart( $product );


	}
	}
	
    }



    public function add_cart_button( $content ) {

	
if( 'post' != get_post_type()){
    return $content;
}
	$price = get_post_meta( get_the_ID(), 'price', true );
echo 'Cena: '.$price;

	$object_nonce = wp_create_nonce('object_'.get_the_ID());
	$content .= '<form action="" method="post">';
	$content .= '<input type="text" name="cart" value="add">';
	$content .= '<input type="text" name="object_qty" value="1" >';
	$content .= '<input type="text" name="object_id" value="'.  get_the_ID() . '">';
	$content .= '<input type="text" name="object_nonce" value="'.$object_nonce.'">';

	$content .= '<button class="btn">Dodaj do koszyka <div class="dashicons dashicons-cart"></div></button>';
	$content .= '</form>';
	return $content;
    }

    public function cart_panel () {
	
    

	


	echo '<a href="'.esc_url( home_url( '/cart' ) ).'">zobacz koszyk</a>';

echo '<h3> Wartość zakupów: '.$this->calculate_grand_total().'</h3>';


    }

    public function index_Action(){

        //dump(__METHOD__);
        //$this->page->add('#(cart)|(\/[?])(.[a-z]*)[=](.[a-z]*)#', array( $this, 'mytest_contentfunc'));

   //$this->page->add('/(cart)(\\/[?])(.[a-z]*)[=](.[a-z]*)/i', array( $this, 'mytest_contentfunc'));
        
        $this->page->add('/cart/', array( $this, 'mytest_contentfunc'));






    }


   // Example of content generating function
    // Must set $this->body even if empty string
    function mytest_contentfunc($v, $url){

	//dump($v);
	// extract an id from the URL
	//$id = 'none';
	//if (preg_match('/cartt/', $url, $m))
	//    $id = $m[0];
	// could wp_die() if id not extracted successfully...
//dump($v);
//dump($this->items);
	array_walk( $this->items, array( $this,'show_title' ) );
	$this->page->body = 'koszyk' . $this->it;
	$this->page->title = "Zawartosc koszyka";
       	$this->page->template = 'page'; // optional
	$this->page->subtemplate = 'billing'; // optional


	
	//dump($this->items);

	foreach( $this->items as $item ){

	    $this->page->body .= $item->post_title .' [ '.$item->qty.' ] '.$item->subtotal .'<br>';
	}

$this->page->body .= '<hr> Razem: ';
	$this->page->body .= $this->calculate_grand_total();
$this->page->body .= ' <a href="'.esc_url( home_url( '/cart/?cart=pay' ) ).'">Realizuj zamówienie</a>';
	//dump($v);

    }

    private function calculate_grand_total(){

	$gt = 0;
	foreach ($this->items as $item){
	    $gt += $item->price * $item->qty;
	}
return $gt;
    }



    public function pay_Action(){
	dump(__METHOD__);

	if(is_user_logged_in()){

	    $this->page->add('/cart/', array( $this, 'pay_content'));

	}else{
	    
	    $this->page->add('/cart/', array( $this, 'unregistered_pay_content'));
	}


    }
public function accept_Action(){
	dump(__METHOD__);

	$new_post = array(
'post_title'    => 'Zamówienie',
'post_content'  => '',
'post_status'   => 'publish',
'post_type'     => 'order'
);

//insert the the post into database by passing $new_post to wp_insert_post
//store our post ID in a variable $pid
$pid = wp_insert_post($new_post);


dump(get_current_user_id());

$order_id = time().'-'.$pid.'-'.  intval(get_current_user_id());
dump($order_id);

$my_post = array(
      'ID'           => $pid,
      'post_title' => $order_id
  );
 wp_update_post( $my_post );


//we now use $pid (post id) to help add out post meta data
add_post_meta($pid, 'order_id', $order_id, true);
add_post_meta($pid, 'order_item', $this->items, true);

$this->items = null;
unset($_SESSION['cart']);
    }
    
    public function unregistered_pay_content(){

	dump(__METHOD__);


	$this->page->title = "Potwierdzone";
       	$this->page->template = 'page'; // optional
	$this->page->subtemplate = 'billing'; // optional


	$this->page->body .= '<a href="'.esc_url( home_url( '/cart/?cart=accept' ) ).'">Zrealizuj</a>';


	
    }


    function pay_content($v, $url){

	array_walk( $this->items, array( $this,'show_title' ) );
	$this->page->body = 'koszyk' . $this->it;
	$this->page->title = "Zawartosc koszykna";
       	$this->page->template = 'page'; // optional
	$this->page->subtemplate = 'billing'; // optional


	$this->page->body .= '';

$this->page->body .= '<a href="'.esc_url( home_url( '/cart/?cart=accept' ) ).'">OKOKOKOKOK</a>';


//$this->page->body .='<form method="post" action="https://secure.payu.com/api/v2_1/orders">
//    <input type="hidden" name="continueUrl" value="http://localhost/continue" />
//    <input type="hidden" name="currencyCode" value="PLN" />
//    <input type="hidden" name="customerIp" value="123.123.123.123" />
//    <input type="hidden" name="description" value="Order description" />
//    <input type="hidden" name="merchantPosId" value="145227" />
//    <input type="hidden" name="notifyUrl" value="http://shop.url/notify.json" />
//    <input type="hidden" name="products[0].name" value="Product 1" />
//    <input type="hidden" name="products[0].quantity" value="1" />
//    <input type="hidden" name="products[0].unitPrice" value="1000" />
//    <input type="hidden" name="totalAmount" value="1000" />
//    <input type="hidden" name="OpenPayu-Signature" value="sender=145227;algorithm=MD5;signature=0fcbdfd920b218edd56366966bef2dcc" />
//    <button type="submit" formtarget="_blank" />
//</form>
//
//
//';
	

	



    }





  public function show_title($item){
      //dump($item);
      $this->it .=  $item->post_title .'<br>';

      

  }


}