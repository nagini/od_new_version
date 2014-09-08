<?php
namespace dlaser\AgendaBundle\Controller;

use dlaser\ParametrizarBundle\Entity\Cargo;
use dlaser\ParametrizarBundle\Entity\Paciente;
use Doctrine\Tests\Common\Annotations\Null;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class InformeCitaController extends Controller
{
	public function informeTipoAction()
	{
            $user = $this->get('security.context')->getToken()->getUser();
            $sedes = $user->getSede();                       

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
            $breadcrumbs->addItem("Informe Cupos");

            return $this->render('AgendaBundle:InformeCita:informe_tipo.html.twig', array(
                            'sedes' => $sedes,

            ));
	}
	
	public function informePacienteAction($paciente)
	{
            $em = $this->getDoctrine()->getManager();    
            $paciente = $em->getRepository('ParametrizarBundle:Paciente')->find($paciente);        
            return $this->getPrintPaciente($paciente, "html");          
	}
        
        public function informePacientePrintAction($paciente)
        {
            $em = $this->getDoctrine()->getManager();    
            $paciente = $em->getRepository('ParametrizarBundle:Paciente')->find($paciente);        
            $html = $this->getPrintPaciente($paciente, "pdf");
            
            $pdf = $this->instanciarImpreso("Informe del paciente");
            $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

            $response = new Response($pdf->Output('Informe_por_paciente.pdf', 'I'));
            $response->headers->set('Content-Type', 'application/pdf');
        }
        
        public function getPrintPaciente($paciente, $tiwg)
        {
            $em = $this->getDoctrine()->getManager();    
            $html = '';
            
            if (!$paciente) {
                throw $this->createNotFoundException('El paciente solicitado no existe.');
            }

            $entity = $em->getRepository("AgendaBundle:Cupo")->findInformePaciente($paciente->getId());
           
            if($tiwg == "html")            
            {
                $breadcrumbs = $this->get("white_october_breadcrumbs");
                $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
                $breadcrumbs->addItem("Informe de pacientes");
                
                $paginator = $this->get('knp_paginator');
                $entity = $paginator->paginate($entity, $this->getRequest()->query->get('page', 1),25);
            
                $html = $this->render('AgendaBundle:InformeCita:informe_personal_pacientes.html.twig', array(
                            'cupos' => $entity,
                            'paciente' => $paciente
                        ));       
            }                
            else{
                $html = $this->renderView('AgendaBundle:InformeCita:informe_personal_paciente_print.html.twig', array(
                            'cupos' => $entity,
                            'paciente' => $paciente
                        ));                          
            }                
            
            return $html;
        }
	                        
	public function informeCitasAction()
	{	
            $request = $this->get('request');
            
            $sede = $request->request->get('sede');
            $option = $request->request->get('tipo');		
            $dateStart = date_create_from_format('d/m/Y H:i:s',$request->request->get('f_inicio').' 00:00:00');
            $dateEnd   = date_create_from_format('d/m/Y H:i:s',$request->request->get('f_fin').' 23:59:00');

            return $this->generarInformeVistas($sede, $option, $dateStart, $dateEnd, 'html');
            
	}
        
        public function printInfoGeneralAction()
        {
            $request = $this->get('request');
            
            $sede = $request->request->get('sede_id');
            $option = $request->request->get('tipo_id');		
            $dateStart = date_create_from_format('d/m/Y H:i:s',$request->request->get('dateStart').' 00:00:00');
            $dateEnd   = date_create_from_format('d/m/Y H:i:s',$request->request->get('dateEnd').' 23:59:00');
            
            $view = $this->generarInformeVistas($sede, $option, $dateStart, $dateEnd, 'pdf');
            $pdf = $this->instanciarImpreso("Informe");
            $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $view, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

            $response = new Response($pdf->Output('Informes_de_citas.pdf', 'I'));
            $response->headers->set('Content-Type', 'application/pdf');
        }
        
        
        public function generarInformeVistas($sede, $option, $dateStart, $dateEnd, $tiwg)
        {
            if($dateStart > $dateEnd )
            {
                    $this->get('session')->getFlashBag()->add('info', 'Las Fechas No Son Correctas, Vuelva A Ingresar La Información.');
                    return $this->redirect($this->generateUrl('factura_reporte_medico'));
            }	

            $em = $this->getDoctrine()->getManager();

            if(is_numeric(trim($sede))){
                    $obj_sede = $em->getRepository("ParametrizarBundle:Sede")->find($sede);
            }else{
                    $obj_sede['nombre'] = 'Todas las sedes.';
                    $obj_sede['id'] = '';
            }		

            if(is_object($obj_sede)){
                    $con_sede = "AND a.sede =".$sede;
            }else{
                    $con_sede = "";
            }			

            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
            $breadcrumbs->addItem("Informe Cupos");

            $entity = $em->getRepository('AgendaBundle:Cupo')->findInformeGeneral($con_sede,$dateStart,$dateEnd, $option);                
            
            if($tiwg == 'html')                
                return $this->render('AgendaBundle:InformeCita:informe_general.html.twig',array('cupos' =>  $entity,'sede' => $sede,'estado'=> $option,'dateStart' => $dateStart,'dateEnd' => $dateEnd,));            
            else                
                return $this->renderView('AgendaBundle:InformeCita:informe_general_print.html.twig',array('cupos' =>  $entity,'sede' => $sede,'estado'=> $option,'dateStart' => $dateStart,'dateEnd' => $dateEnd,));            
        }
        
        private function instanciarImpreso($title)
        {
            // se instancia el objeto del tcpdf
            $pdf = $this->get('white_october.tcpdf')->create();

            $pdf->setFontSubsetting(true);
            $pdf->SetFont('dejavusans', '', 8, '', true);

            // Header and footer
            //$pdf->SetHeaderData('logo.jpg', 20, 'Hospital San Agustin', $title);
            $pdf->setFooterData(array(0,64,0), array(0,64,128));

            // set header and footer fonts
            $pdf->setHeaderFont(Array('dejavusans', '', 8));
            $pdf->setFooterFont(Array('dejavusans', '', 8));

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(1);
            $pdf->SetFooterMargin(10);

            // set image scale factor
            $pdf->setImageScale(5);
            $pdf->AddPage();

            return $pdf;
         }
}