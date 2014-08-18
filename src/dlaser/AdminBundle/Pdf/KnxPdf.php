<?php

namespace knx\FacturacionBundle\Pdf;

use Symfony\Component\HttpFoundation\Response;

class KnxPdf extends \TCPDF
{
	
	//Page header
	public function Header() {
		
		$y = 10;

		$this->SetFont('helvetica', '', 7);
		
		$this->SetY($y);
		$this->Cell(0, 0, 'HOSPITAL SAN AGUSTIN E.S.E', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		
		$this->SetY($y+4);
		$this->Cell(0, 0, 'EMPRESA SOCIAL DEL ESTADO', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		
		$this->SetY($y+8);
		$this->Cell(0, 0, 'PUERTO MERIZALDE - BUENAVENTURA - VALLE DEL CAUCA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		
		$this->SetY($y+12);
		$this->Cell(0, 0, 'NIT. 800.155.000-8', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		
		
	}
	
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		// Set font
		$this->SetFont('helvetica', '', 7);
		
		$this->Cell(0, 0, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 0, 'R', 0, '', 0, false, 'T', 'M');
		
		
		//$this->Line(15,282,195,282);
		
		
	}

    public function init()
    {
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('CCV');
        $this->SetTitle('Informe médico');
        $this->SetKeywords('Cardiología, Procedimientos');

        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------

        // set default font subsetting mode
        $this->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        $this->SetFont('dejavusans', '', 8, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $this->AddPage();

    }
    /**
     */
    public function quick_pdf($html, $file = "html.pdf", $format = "S")
    {
      $this->init();

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $this->writeHTML($html, true, false, true, false, '');

        $response =  new Response($this->Output($file, $format));
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;

    }
	
}
