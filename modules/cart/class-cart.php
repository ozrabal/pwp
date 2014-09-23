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

   

    private function add_to_cart( $object){

	

	$this->items[] = $object;
	

	

$this->update_cart();
	//$_SESSION['cart'] = $this->items;
	//dump(count($_SESSION['cart']));
    }

    public function add_Action(){

	if ( wp_verify_nonce( $_POST['object_nonce'], 'object_'.$_POST['object_id'] ) ) {

	//dump($_POST);

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
	
    

	


	echo '<a href="'.esc_url( home_url( '/cart' ) ).'">zobacz koszyk</a>';




    }

    public function index_Action(){

        dump(__METHOD__);
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
	$this->page->title = "Zawartosc koszykna";
       	$this->page->template = 'page'; // optional
	$this->page->subtemplate = 'billing'; // optional

	//dump($v);

    }


    public function pay_Action(){
	dump(__METHOD__);
    }

  public function show_title($item){
      //dump($item);
      $this->it .=  $item->post_title .'<br>';

      

  }


}