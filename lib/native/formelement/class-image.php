<?php
/**
   * Formelement_Image class
   *
   * @package    PWP
   * @subpackage Core
   * @author     Piotr Łepkowski <piotr@webkowski.com>
   */
class Formelement_Image extends Formelement {
    private $type = 'image';

    /**
     * dolacza skrypty js
     */
    public function enqueue_scripts() {

	wp_enqueue_script( 'field-image',  plugins_url( '/field-image.js', __FILE__ ), array( 'jquery' ), PWP_VERSION );
    }

    /**
     * pobiera sciezke miniatury na podstawie id obiektu
     * @param Int $id
     * @return String
     */
    private function get_thumbnail( $id = null ) {

	if( $id ) {
	    $current_img = wp_get_attachment_image_src( intval( $id ), 'thumbnail' );
	    if( $current_img ) {
                return $current_img[0];
            } else {
		if( has_post_thumbnail() ) {
		    $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		    if( $thumbnail_id ) {
			$thumbnail = wp_get_attachment_image_src( $thumbnail_id , 'thumbnail' );
			return $thumbnail[0];
		    }
		}
	    }
        }
        return PWP_ROOT_URL . 'images/image.png';
    }
    
    /**
     * renderuje pole obrazek
     * @return String
     */
    public function render() {

//	add_action( 'setup_theme', array( $this, 'enqueue_scripts' ) );
	$this->enqueue_scripts();
        parent::render();
        wp_enqueue_media();
	$this->set_class( 'field-box' );
        $body =  $this->get_before() . $this->get_label();
        $body .= '<div ' . $this->cssclass() . '>';
	$body .= '<a class="button button-secondary open-media-button" ' . $this->get_data() . ' id="open-media-modal' . $this->get_id() . '" ><span class="pwp-icon dashicons dashicons-admin-media"></span> ' . __( 'Add / Change image', 'pwp' ) . '</a>';
        $body .= '<a class="button button-secondary remove-media-button" id="remove-media' . $this->get_id() . '" ><span class="pwp-icon dashicons dashicons-dismiss"></span> ' . __( 'Remove image', 'pwp' ) . '</a>';
	$body .= '<div id="m_open-media-modal' . mt_rand() . $this->get_id() . '" class="attachment-fieldset open-media-modal' . $this->get_id() . '">';
        $body .= '<input type="hidden" ' . $this->set_id( 'attachment-id' )->id() . $this->name() . $this->value() . '>';
	$body .= '<div class="attachment-preview type-image"><div class="thumbnail"><div class="centered"><img class="slide-image" id="attachment-src" data-src-default="' . $this->get_thumbnail() . '" src="' . $this->get_thumbnail( $this->get_value() ) . '" />';
	$body .= '</div></div></div></div>';
        $body .= $this->get_message();
        $body .= '</div>';
        $body .= $this->get_comment( '<p class="description">%s</p>' );
        $body .= $this->get_after();
        return $body;
    }
}