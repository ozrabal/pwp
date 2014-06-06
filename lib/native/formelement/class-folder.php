<?php



class Formelement_Folder extends Formelement {
    protected $type = 'text';

    
    public function __construct( $form, $name ) {
        
        add_action( 'wp_ajax_file_manager', array($this,'file_manager'));
        add_action( 'wp_ajax_nopriv_file_manager', array($this,'file_manager'));
        parent::__construct( $form, $name );
    }
    
    
    public function render(){
        parent::render();

	wp_enqueue_media();
	$this->set_class('field-box');
        $body =  $this->get_before() . $this->get_label();
        $body .= '<div '.$this->cssclass().'>';
	
        $body .= '<a class="button button-secondary thickbox" id="open-media-modal'.$this->get_id().'" href="/wp-admin/admin-ajax.php?&action=file_manager&width=650&height=200&TB_iframe=true"><span class="pwp-icon dashicons dashicons-admin-media"></span> '.__( 'Select folder', 'pwp' ).'</a>';
        $body .= '<a class="button button-secondary remove-media-button" id="remove-media'.$this->get_id().'" ><span class="pwp-icon dashicons dashicons-dismiss"></span> '.__( 'Clear field', 'pwp' ).'</a>';
        $body .= '<input '.$this->id().$this->type().$this->name().$this->value().$this->cssclass().'/>';
        $body .= $this->get_message();
        $body .= '</div>';
        $body .= $this->get_comment('<p class="description">%s</p>');
        $body .= $this->get_after();
        return $body;
        

       
    
          
    }
    
    
    function file_manager() {
       // wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
   
    //add_thickbox();
    $context = '<a class="thickbox button " title="'.__('Add form element', 'pwp').'" href="/wp-admin/admin-ajax.php#?action=choice&width=150&height=100&TB_iframe=true">
    <span class="wp-media-buttons-icon dashicons dashicons-exerpt-view"></span>'.__('Add form element', 'pwp').'</a>';


?>


<?php


set_current_screen( 'file_manager');
$current_screen = get_current_screen();

iframe_header($title = 'aaa', $limit_styles = false );

?>




	 <table class="form-table pwp-metabox">
<tbody>
<tr>
<td>
<div class="large-text field-box">
<a id="open-media-modal" class="button button-secondary thickbox" href="/wp-admin/admin-ajax.php?&action=file_manager&TB_iframe=true&width=640&height=716">
</a>  <a id="remove-media" class="button button-secondary remove-media-button"></a>
<input class="large-text field-box" type="text" name="resource_path[folder]">
</div>
<p class="description">lików oraz fo</p>
</td>
</tr>
<tr>
</tbody>
</table>

<div class="browser">
	    <?php echo $context; 
            
            
             
            
            $dir =  get_home_path()."wp-content/uploads";
            echo $dir;
            //die();
$dh  = opendir($dir);
dump($dh);

while (false !== ($filename = readdir($dh))) {
    $files[] = $filename;
}

sort($files);

dump($files);

            
            
            ?>

<div id="__wp-uploader-id-0" class="media-frame wp-core-ui">

	<div class="media-frame-toolbar">
<div class="media-toolbar">
<div class="media-toolbar-secondary">
<div class="media-toolbar-primary">
<a class="button media-button button-primary button-large media-button-insert" href="#"  onclick="self.parent.tb_remove();return false">Insert into post</a>
</div>
</div>
</div>
        </div>
	
</div>

</div>


<?php
iframe_footer() ;
?>

    <?php
  die();
}
    
    
    
    
    
}

