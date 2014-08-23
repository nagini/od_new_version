<?php
namespace dlaser\AgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use dlaser\AgendaBundle\Entity\Cupo;
use dlaser\ParametrizarBundle\Entity\Afiliacion;
use dlaser\AgendaBundle\Form\AsignarCitaType;

class AgendaMedicaController extends Controller
{
	public function listAction()
	{
		$em = $this->getDoctrine()->getManager();
		$medicos = $em->getRepository('AgendaBundle:Agenda')->findMedicos();

		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
		$breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
		$breadcrumbs->addItem("Medicos");
	
		return $this->render('AgendaBundle:AgendaMedica:list.html.twig', array(
				'medicos'  => $medicos
		));
	}
	
	public function agendaSedesAction($medico)
	{
		$em = $this->getDoctrine()->getManager();
		$medico = $em->getRepository('UsuarioBundle:Usuario')->find($medico);
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
		$breadcrumbs->addItem("Medicos", $this->get("router")->generate("medico_list"));
		$breadcrumbs->addItem("Agendas");
		
		if(!$medico)
		{
			$this->get('session')->getFlashBag()->add('error', 'El medico solicitado no existe.');			
			return $this->redirect($this->generateUrl('medico_list'));		
		}
		$sedes = $medico->getSede();
		
		$this->get('session')->getFlashBag()->add('ok', 'Listado de las sedes donde labora el medico.');
			
		return $this->render('AgendaBundle:AgendaMedica:medico_list_agenda.html.twig', array(
				'medico'  => $medico,
				'sedes'  => $sedes,
				'facturas' => '',
				'formulario' => ''
		));
	}
	
	public function agendaCitasAction($medico, $sede)
	{
		$user = $this->get('security.context')->getToken()->getUser();
		$id=$user->getId();
		
		$em = $this->getDoctrine()->getManager();
		$medico = $em->getRepository('UsuarioBundle:Usuario')->find($medico);
		$sede = $em->getRepository('ParametrizarBundle:Sede')->find($sede);
		
		$breadcrumbs = $this->get("white_october_breadcrumbs");
		$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
		$breadcrumbs->addItem("Medicos", $this->get("router")->generate("medico_list"));
		$breadcrumbs->addItem("Agendas");
		
		if(!$medico || !$sede)
		{
			$this->get('session')->getFlashBag()->add('error', 'La consulta realizada no tiene coherencia con el medico y la sede.');
			return $this->redirect($this->generateUrl('medico_list'));
		}
		$sedes = $medico->getSede();
		$facturas = $em->getRepository('AgendaBundle:Agenda')->findAgendaDelMedico($medico->getId(),$sede->getId());	
		
		$cupo = new Cupo();		
		$form = $this->createForm(new AsignarCitaType(array('user' => $id)),  $cupo);
			
		return $this->render('AgendaBundle:AgendaMedica:medico_list_agenda.html.twig', array(
				'medico'  => $medico,
				'sedes'  => $sedes,
				'facturas' => $facturas,
				'formulario' => $form->createView()
		));
	}
	
	public function saveNewCitaAction()
	{
		$cupo = new Cupo();
		
		$request = $this->get('request');
		$paciente = $request->request->get('paciente');
	    $sede = $request->request->get('sede');
	    $cliente = $request->request->get('cliente');
	    $cargo = $request->request->get('cargo');
	    $agenda = $request->request->get('agenda');
	    $hora  = $request->request->get('hora');	    
		
		
		$em = $this->getDoctrine()->getManager();
		
		$agenda = $em->getRepository('AgendaBundle:Agenda')->find($agenda);
		$paciente = $em->getRepository('ParametrizarBundle:Paciente')->find($paciente);
		$cargo = $em->getRepository('ParametrizarBundle:Cargo')->find($cargo);
		$user = $this->get('security.context')->getToken()->getUser();
		
		
		if($agenda)
		{
			$hora = new \DateTime($hora);
			 
			$cupo->setHora($hora);
			$cupo->setRegistra($user->getId());
			$cupo->setPaciente($paciente);
			$cupo->setCargo($cargo);
			$cupo->setEstado('A');
			$cupo->setNota('');
			$cupo->setAgenda($agenda);
			$cupo->setCliente($cliente);

			
			$em->persist($cupo);
			$em->flush();
			
			$response=array("responseCode"=>200, "msg"=>"La reserva ha sido creada éxitosamente.");
		}else{			
			$response=array("responseCode"=>400, "msg"=>"Hay informacion incompleta para la reserva del cupo.");
		}		
    		
    	$return=json_encode($response);
    	return new Response($return,200,array('Content-Type'=>'application/json'));
	}
	
	
	public function printMedicoAgendaAction($agenda)
	{
		
			$em = $this->getDoctrine()->getManager();
			$agenda = $em->getRepository('AgendaBundle:Agenda')->find($agenda);
			
			if (!$agenda){				
			
				$this->get('session')->setFlash('error', 'La agenda seleccionada no existe.');
				return $this->redirect($this->generateUrl('cupo_list'));
			}
			$profesional = $agenda->getUsuario();
			
		
			$query = $em->createQuery(" SELECT 
					c.id,
                    c.hora,
                    c.estado,                                    
                    p.id AS paciente,
            		p.priNombre,
                    p.segNombre,
                    p.priApellido,
                    p.segApellido,
					p.identificacion,
            		p.movil,
                    car.nombre as cargo
                    FROM AgendaBundle:Cupo c
                    LEFT JOIN c.paciente p
                    LEFT JOIN c.cargo car
					LEFT JOIN c.agenda a					
					WHERE a.id = :agenda 
					AND c.estado = 'CO'
                    ORDER BY c.hora ASC");		
		
			$query->setParameter('agenda', $agenda->getId());
			$cupo = $query->getResult();	
		
		
			if (!$cupo){				
				
				$this->get('session')->getFlashBag()->add('error', 'La agenda no tiene cupos definidos.');
				return $this->redirect($this->generateUrl('cupo_list'));
			}				
					
				$pdf = $this->instanciarImpreso("Listado de agendas del medico");
				$view = $this->renderView('AgendaBundle:Impresos:Agenda_medica_print.html.twig',
						array(
								'profesional' => $profesional,
								'agenda' => $agenda,
								'cupos' => 	$cupo,
						));
					
				$pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $view, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
					
				$response = new Response($pdf->Output('Agenda_medica.pdf', 'I'));
				$response->headers->set('Content-Type', 'application/pdf');
					
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
