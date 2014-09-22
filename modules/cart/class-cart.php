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

	$this->action_slug = 'cart';

	dump($this->actions);
	if( !session_id() ) {
	    Pwp::load_module( 'session' );
	}

	if(isset($_SESSION['cart'])){
	    $this->items = $_SESSION['cart'];
	}
$this->page =  new Virtualpage();

	$wp_error = $this->route();
       

	//$this->cart

	wp_enqueue_style( 'dashicons' );
	add_action( 'the_content', array( $this, 'add_cart_button' ) );

	//add_action( 'get_template_part_content', array( $this, 'add_cart_panel' ) );
	
dump(count($this->items));


	register_widget( 'Cart_Widget' );
//$this->update_cart();

    

    }


    private function update_cart(){
	$_SESSION['cart'] = $this->items;
    }

   

    private function add_to_cart( $object){

	//dump($this->items);

	$this->items[] = $object;
	//dump($this->items);


	//dump($_SESSION['cart']);


	//$_SESSION['cart'][] = $object;

	

$this->update_cart();
	//$_SESSION['cart'] = $this->items;
	//dump(count($_SESSION['cart']));
    }

    public function add_Action(){

	if ( wp_verify_nonce( $_POST['object_nonce'], 'object_'.$_POST['object_id'] ) ) {

	dump($_POST);

	$object = get_post(intval($_POST['object_id']));

	//dump($object);

	//$this->items[] = $object;

	$this->add_to_cart( $object );
	}
	
    }



    public function add_cart_button( $content ) {

	
	$object_nonce = wp_create_nonce('object_'.get_the_ID());
	$content .= '<form action="?cart=add" method="post">';
	$content .= '<input type="text" name="object_id" value="'.  get_the_ID() . '">';
	$content .= '<input type="text" name="object_nonce" value="'.$object_nonce.'">';

	$content .= '<button class="btn">Dodaj do koszyka <div class="dashicons dashicons-cart"></div></button>';
	$content .= '</form>';
	return $content;
    }

    public function cart_panel () {
	
        //dump($_SESSION);
	dump(count($_SESSION['cart']));

        dump(count($this->items));

	


	echo '<a href="'.esc_url( home_url( '/cart/?cart=show' ) ).'">zobacz koszyk</a>';




    }

    public function index_Action(){

        dump($this->page);
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
	$this->page->title = "Zawartosc koszykna";
       	$this->page->template = 'page'; // optional
	$this->page->subtemplate = 'billing'; // optional

	//dump($v);

    }

  public function show_title($item){
      //dump($item);
      $this->it .=  $item->post_title .'<br>';

      

  }


}