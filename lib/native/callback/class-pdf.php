<?php

class Callback_Pdf implements Interface_Callback {


    
    public function do_callback( $params ) {
	
	$this->params = $params;

	$this->params->admin_attachment = $this->create();


	

	return $this->params;
    }

    private function get_form_name( $a, $b ) {
	if( substr( $b, -5, strlen( $b ) ) == '_name' ) {
	    $this->params->pdf_filename = $a;
	}
    }



    public function create(){
	/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: XHTML + CSS
 * @author Nicola Asuni
 * @since 2010-05-25
 */

// Include the main TCPDF library (search for installation path).
require_once(PWP_ROOT.'lib/external/tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 061');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
/*if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}
*/
// ---------------------------------------------------------

// set font
$pdf->SetFont('DejaVuSans', '', 10);

// add a page
$pdf->AddPage();

/* NOTE:
 * *********************************************************
 * You can load external XHTML using :
 *
 * $html = file_get_contents('/path/to/your/file.html');
 *
 * External CSS files will be automatically loaded.
 * Sometimes you need to fix the path of the external CSS.
 * *********************************************************
 */



if( !isset( $this->params->pdf_filename ) ) {
    if( isset( $this->params->request ) ) {
	$pp = $this->params->request;
	array_walk( $pp, array( $this, 'get_form_name' ) );
    } else {
	$this->params->pdf_filename = 'file';
    }
}
$html = null;

if(file_exists(get_template_directory().'/'.$this->params->pdf_filename.'.html')){
$html = file_get_contents(get_template_directory().'/'.$this->params->pdf_filename.'.html');
}
dump($html);

	if( $html  ) {
            foreach( $this->params->request as $k => $v ) {
                if( !is_array( $this->params->request[$k] ) ) {
                    //$user_body = str_ireplace( '['.$k.']',  $this->get_request( $k ), $user_body );
                    $html = str_ireplace( '['.$k.']',  $this->params->request[$k], $html );



		} else {
                    //@todo polapowtarzalne do szablonow email
                }
            }
        } else {
            foreach( $this->params->request as $k => $v ) {
                if( !is_array( $this->params->request[$k] ) ) {
                    $html .= $k . ' : ' . $v . '<br>';
                }
            }
        }


// define some HTML content with style
//


//reset ($this->params['request'] );
//$pp = $this->params['request'];


//$p = array_walk( $pp, array( $this, 'unset_var' ) );


//foreach($this->params['request'] as $a => $b){
//    $this->unset_var($a, $b);
//
//dump(substr( $a, 0, 1 ));
//
//
//}

 
//dump($this->params['request']);

//$html .= implode(', ', $this->params['request']);

//$html = $this->params->admin_body;


// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
$upload_dir = wp_upload_dir();






$this->params->pdf_filename = apply_filters('callback_pdf_filename', $this->params->pdf_filename);




$file = $upload_dir['path'].'/'.$this->params->pdf_filename.'-'.time().'.pdf';


//Close and output PDF document
$pdf->Output($file, 'F');
return $file;
//============================================================+
// END OF FILE
//============================================================+
    }




       public function unset_var($a, $b){
	 



       if(substr( $b, 0, 1 ) == '_' ){
	  
	   unset($this->params['request'][$b]);
       }
   }


}
