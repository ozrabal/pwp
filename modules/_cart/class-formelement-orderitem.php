<?php
/**
   * Formelement_Orderitem class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Orderitem extends Formelement {
    protected $type = 'orderitem';

    /**
     * renderuje komentarz
     * @return string
     */
    public function render() {

        return $this->get_before() . $this->get_label() . '<p ' . $this->cssclass() . $this->id() . '>' . $this->items() . '</p>' . $this->get_message() . $this->get_comment( '<p class="description">%s</p>' ) . $this->get_after();
    }


    private function items(){
	dump($this->get_value());

$product = '<table>';
	$items = $this->get_value();

	foreach( $items as $item ){
	    $product .= $this->create_row($item);
	   
	}
$product .= '</table>';
	return $product;

    }

    private function create_row($item){
	$post = get_post($item->ID);
$price = get_post_meta( $post->ID, 'price', true );
	$body = '<tr>';
$body .= '<td>'.$post->ID.'</td>';
$body .= '<td><a href="'.$post->guid.'" target="_blank">'.$post->post_title.'</a></td>';
$body .= '<td>'.$item->qty.'</td>';
$body .= '<td>'.$item->price.'</td>';
$body .= '<td>'.$item->subtotal.'</td>';
	$body .= '</tr>';
	return $body;
    }
}