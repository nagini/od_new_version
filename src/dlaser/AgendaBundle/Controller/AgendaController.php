<?php

namespace dlaser\AgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use dlaser\AgendaBundle\Entity\Restriccion;
use dlaser\AgendaBundle\Entity\Agenda;
use dlaser\AgendaBundle\Entity\Cupo;
use dlaser\AgendaBundle\Form\RestriccionType;
use dlaser\AgendaBundle\Form\AgendaType;
use dlaser\AgendaBundle\Form\AgendaMedicaType;
use dlaser\AgendaBundle\Form\AsignarCitaType;


class AgendaController extends Controller
{
    /* se instancia una variable privada para lamacenar la informacion de las agendas que se encuentran disponibles en las sedes 
     relacionadas con el usuario.*/
    private $array = array();
    
    public function recursion($agenda, $int, $sede)
    {        
        if( $int >  (count($agenda)-1))
            $this->array;        
        elseif($agenda[$int]['sedeid'] != $sede)        
            $this->recursion ($agenda, ($int+1), $sede);                        
        else{                        
            $this->array[$int] = $agenda[$int];                        
            $this->recursion ($agenda, ($int+1), $sede);
        }
        return $this->array;
    }
    
    public function listAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();  
        $userSedes = $user->getSede();        
        
        $em = $this->getDoctrine()->getManager();    
        $agenda = $em->getRepository('AgendaBundle:Agenda')->findAgendasActivas();
        
        $lista = array();
        /* se realiza una recursion para optimizar el proceso de seleccion
         * de todas las agendas relacionadaas con las sedes del usuario.
         * se envia las agendas, un contador inicial en 0 y el id de cada sede
         */
        foreach ($userSedes as $key => $value)    
           $lista = $this->recursion($agenda, 0, $value->getId());        
        $agenda = $lista;

        $paginator = $this->get('knp_paginator');
        $agenda = $paginator->paginate($agenda, $this->getRequest()->query->get('page', 1),20);  
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Listar");
    
        return $this->render('AgendaBundle:Agenda:list.html.twig', array(
                'agendas'  => $agenda
        ));        
    }

    public function newAction()
    {
        $entity = new Agenda();
        $entity->setFechaInicio(new \DateTime('now'));
        $entity->setFechaFin(new \DateTime('now'));
        
        // se optiene el usuario que se encuentra activo en el sistema para listar las sedes relacionadas.
        $user = $this->get('security.context')->getToken()->getUser();                
        // se envia la informacion por medio de un array al FORMTYPE y estos valores se optienen con el $option en el type
        $form   = $this->createForm(new AgendaType(), $entity, array('userId' => $user->getId()));
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Nuevo");
                   
        return $this->render('AgendaBundle:Agenda:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    }


    public function saveAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        // se optiene el usuario que se encuentra activo en el sistema para listar las sedes relacionadas.
        $user = $this->get('security.context')->getToken()->getUser();                  
                
        $entity  = new Agenda();                
        $form    = $this->createForm(new AgendaType(), $entity, array('userId' => $user->getId()));
        $form->handleRequest($this->getRequest());   
                       
        if ($form->isValid()) 
        {      	
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->getFlashBag()->add('ok', 'La agenda ha sido creada éxitosamente.');    
            return $this->redirect($this->generateUrl('agenda_show', array("id" => $entity->getId())));    
        }
    
        return $this->render('AgendaBundle:Agenda:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView()
        ));
    }
    
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();    
        $agenda = $em->getRepository('AgendaBundle:Agenda')->find($id);
    
        if (!$agenda) {
            throw $this->createNotFoundException('La agenda solicitada no existe.');
        }        
        $firewalls = $em->getRepository('AgendaBundle:Restriccion')->findByAgenda($id);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));        
        $breadcrumbs->addItem("Detalle");
    
        return $this->render('AgendaBundle:Agenda:show.html.twig', array(
                'agenda'  => $agenda,
                'restricciones' => $firewalls
        ));
    }
    
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();    
        $entity = $em->getRepository('AgendaBundle:Agenda')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La agenda solicitada no existe');
        }
    
        $user = $this->get('security.context')->getToken()->getUser();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));                
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $id)));
        $breadcrumbs->addItem("Modificar");
        
        $editForm = $this->createForm(new AgendaType(), $entity, array('userId' => $user->getId()));
    
        return $this->render('AgendaBundle:Agenda:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
    
        $entity = $em->getRepository('AgendaBundle:Agenda')->find($id);
    
        if (!$entity) {
            throw $this->createNotFoundException('La agenda solicitada no existe.');
        }
    
        $user = $this->get('security.context')->getToken()->getUser();
        
        $editForm   = $this->createForm(new AgendaType(), $entity, array('userId' => $user->getId()));         
        $editForm->handleRequest($this->getRequest());
    
        if ($editForm->isValid()) {
    
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->getFlashBag()->add('ok', 'La agenda ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('agenda_edit', array('id' => $id)));
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_list"));                
        $breadcrumbs->addItem("Detalle",$this->get("router")->generate("agenda_show",array("id" => $id)));
        $breadcrumbs->addItem("Modificar");
    
        return $this->render('AgendaBundle:Agenda:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView()
        ));
    }
    
    
    
    public function ajaxBuscarAction(){
    
        $request = $this->get('request');
        $cliente=$request->request->get('cliente');
        $sede=$request->request->get('sede');
        $cargo=$request->request->get('cargo');
    
        if(is_numeric($cliente) && is_numeric($sede) && is_numeric($cargo)){
    
            $em = $this->getDoctrine()->getManager();
            
            $query = $em->createQuery(' SELECT 
                                            a 
                                        FROM 
                                            AgendaBundle:Agenda a
                                        WHERE 
                                            a.fechaFin > :fecha AND
                                            a.sede = :sede
                                        ORDER BY 
                                            a.fechaInicio ASC');
            
            
            $query->setParameter('fecha', new \DateTime('now'));
            $query->setParameter('sede', $sede);
            
            $agendas = $query->getResult();
          
            if($agendas)
            {    
                $response=array("responseCode"=>200);    
              foreach($agendas as $value)
              {
                    $response['agenda'][$value->getId()] = $value->getFechaInicio()->format('d-m-Y H:i')." - ".$value->getFechaFin()->format('H:i')." - ".$value->getNota();             
              }
            }else{
                    $response=array("responseCode"=>400, "msg"=>"No hay agendas disponibles para la actividad seleccionada en la sede");
            }
        
        $return=json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));
        
       }
    }
    
    public function ajaxListaAction(){
    
        $request = $this->get('request');        
        $sede=$request->request->get('sede');
            
        if(is_numeric($sede)){
        	
        	$fecha = new \DateTime('now');
    
            $em = $this->getDoctrine()->getManager();
    
            $query = $em->createQuery(' SELECT a
                    FROM AgendaBundle:Agenda a
            		LEFT JOIN a.usuario u
                    WHERE a.fechaInicio > :fi
                    AND a.sede = :sede
                    ORDER BY a.fechaInicio ASC');
    
            $query->setParameter('fi', $fecha->format('Y-m-d 00:00:00'));
            $query->setParameter('sede', $sede);
    
            $agendas = $query->getArrayResult();
    
            if($agendas)
            {
                $response=array("responseCode"=>200);
                
    
                foreach($agendas as $key => $value)
                {
                    $response['agenda'][$key] = $value;
                }                
                
                $int = 0;
                foreach($response['agenda'] as $mi_agenda)
                {
                	$response['agenda'][$int]['fechaInicio'] = $mi_agenda['fechaInicio']->format('d/m/Y H:i');
                	$response['agenda'][$int]['fechaFin'] = $mi_agenda['fechaFin']->format('d/m/Y H:i');                	
                	$int ++;
                }                
                
            }else{
                $response=array("responseCode"=>400, "msg"=>"No hay agendas disponibles para la sede seleccionada");
            }
    
            $return=json_encode($response);
            return new Response($return,200,array('Content-Type'=>'application/json'));
    
        }
    }
    
    public function agendaMedicaAction(){
        
        $user = $this->get('security.context')->getToken()->getUser();
        $id=$user->getId();
                
        $form   = $this->createForm(new AgendaMedicaType(array('user' => $id)));
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Agenda", $this->get("router")->generate("agenda_medica_list"));
        $breadcrumbs->addItem("Medica");
        
        return $this->render('AgendaBundle:Agenda:agenda_medica.html.twig', array(
                'form' => $form->createView(),
        ));
    }

    public function agendaAuxiliarAction(){
    
    	$user = $this->get('security.context')->getToken()->getUser();
    	$id=$user->getId();
    
    	$form   = $this->createForm(new AgendaMedicaType(array('user' => $id)));
    
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Agenda actividades", $this->get("router")->generate("agenda_aux_list"));
    	$breadcrumbs->addItem("Preinformar");
    	
    	return $this->render('AgendaBundle:Agenda:agenda_auxiliar.html.twig', array(
    			'form' => $form->createView(),
    	));
    }

    public function ajaxAgendaMedicaBuscarAction(){
    
        $request = $this->get('request');
        $sede=$request->request->get('sede');
    
        if(is_numeric($sede)){
    
            $em = $this->getDoctrine()->getManager();
            
            $user = $this->get('security.context')->getToken()->getUser();
            $usuario = $user->getId();
    
            $agendas = $em->getRepository('AgendaBundle:Agenda')->findAgendaDelMedico($usuario,$sede);         

            if($agendas){

                $response=array("responseCode"=>200);

                foreach($agendas as $key => $value)
                {
                    $response['agenda'][$key] = $value;
                }
                $int = 0;
                foreach($response['agenda'] as $mi_agenda)
                {
                	$response['agenda'][$int]['hora'] = $mi_agenda['hora']->format('d/m/Y H:i');
                	$response['agenda'][$int]['fecha'] = $mi_agenda['fecha']->format('d/m/Y H:i');
                	$response['agenda'][$int]['estado'] = 'Pendiente';
                	$int ++;
                }
            }else{
                $response=array("responseCode"=>400, "msg"=>"No hay actividades pendientes para la sede seleccionada");
            }

            $return=json_encode($response);
            return new Response($return,200,array('Content-Type'=>'application/json'));
    
        }
    }
    
    public function ajaxAgendaAuxiliarBuscarAction(){
    
    	$request = $this->get('request');
    	$sede=$request->request->get('sede');
    
    	if(is_numeric($sede)){
    
    		$em = $this->getDoctrine()->getManager();    
    
    		$query = $em->createQuery(' SELECT f.id,
    				c.hora,
    				f.fecha,
    				p.identificacion,
    				p.priNombre,
    				p.segNombre,
    				p.priApellido,
    				p.segApellido,
    				cli.nombre as cliente,
    				car.nombre as cargo,
    				car.cups,
    				f.observacion,
    				f.estado
    				FROM ParametrizarBundle:Factura f
    				LEFT JOIN f.cupo c
    				LEFT JOIN f.cliente cli
    				LEFT JOIN f.paciente p
    				LEFT JOIN f.cargo car
    				WHERE f.sede = :sede
    				AND f.estado = :estado
    				AND car.cups IN (896100, 894102, 895001, 920407, 920408, 881234, 881236, 890202)
    				ORDER BY c.hora ASC');
    		
    
    		$fecha = new \DateTime('now');
    
    		$query->setParameter('sede', $sede);
    		$query->setParameter('estado', 'P');
    
    		$agendas = $query->getArrayResult();
    
    		if($agendas){
    
    			$response=array("responseCode"=>200);
    
    			foreach($agendas as $key => $value)
    			{
    				$response['agenda'][$key] = $value;
    			}
    		}else{
    			$response=array("responseCode"=>400, "msg"=>"No hay actividades pendientes para la sede seleccionada");
    		}
    
    		$return=json_encode($response);
    		return new Response($return,200,array('Content-Type'=>'application/json'));
    
    	}
    }
    
    public function listNewProgrammerAction()
    {
    	$user = $this->get('security.context')->getToken()->getUser();    	
    	$sedes = $user->getSede();    	
    	
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("List programar citas", $this->get("router")->generate("agenda_list_new_citas"));
    	
    	return $this->render('AgendaBundle:Agenda:list_appointments.html.twig', array(
    			'sedes' => $sedes,
    			'cupos'	=> '',
    			'formulario'	=> '',
    	));   	
    } 
    
    public function appointmentsAction($sede)
    {
    	$user = $this->get('security.context')->getToken()->getUser();
    	$sedes = $user->getSede();
    	
    	$em = $this->getDoctrine()->getManager();
    	$cupos = $em->getRepository('AgendaBundle:Cupo')->findAppointments($sede);
    	
    	
    	if (!$cupos) {
    		$this->get('session')->getFlashBag()->add('error', 'No hay citas a programar para la sede seleccionada.');
    		
    		return $this->render('AgendaBundle:Agenda:list_appointments.html.twig', array(
    				'sedes' => $sedes,
    				'cupos'	=> '',
    				'formulario'	=> '',
    		));
    	} 
    	
    	$this->get('session')->getFlashBag()->add('ok', 'Listado de las citas a programar solicitadas por el medico.');
    	
    	$cupo = new Cupo();
    	$user = $this->get('security.context')->getToken()->getUser();  	
    	$form = $this->createForm(new AsignarCitaType(array('user' => $user->getId())),  $cupo);
    	
    	return $this->render('AgendaBundle:Agenda:list_appointments.html.twig', array(
    			'sedes' => $sedes,
    			'cupos'	=> $cupos,
    			'formulario' => $form->createView()
    	));
    }
    
    public function checkAppointmentAction()
    {
    	$request = $this->get('request');
    	$cupo = $request->request->get('cupo');
    	
    	$em = $this->getDoctrine()->getManager();
    	$cupo = $em->getRepository('AgendaBundle:Cupo')->find($cupo);
    	
    	if (!$cupo) {
    		    	
    		$response = array("responseCode"=>400, "msg"=>"La cita solicitada no existe.");
			
			$return=json_encode($response);
			return new Response($return,200,array('Content-Type'=>'application/json'));
    	}
    	
    	$cupo->setEstado('PD');
    	$em->persist($cupo);
    	$em->flush();
    	
    	$response=array("responseCode"=>200, "msg"=>"La Actividad se registro éxitosamente.");
		
		$return = json_encode($response);
		return new Response($return,200,array('Content-Type'=>'application/json'));	
    	
    }
    
    
}