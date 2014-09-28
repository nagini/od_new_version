<?php

namespace dlaser\AgendaBundle\Controller;

use dlaser\ParametrizarBundle\Entity\Cargo;

use dlaser\ParametrizarBundle\Entity\Paciente;

use Doctrine\Tests\Common\Annotations\Null;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use dlaser\AgendaBundle\Entity\Cupo;
use dlaser\ParametrizarBundle\Entity\Afiliacion;
use dlaser\AgendaBundle\Form\CupoType;
use dlaser\AdminBundle\Form\AfiliacionType;


class CupoController extends Controller
{

    public function listAction()
    {        
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Citas", $this->get("router")->generate("cupo_list"));
    	$breadcrumbs->addItem("Reserva");
    	
        return $this->render('AgendaBundle:Cupo:list.html.twig');        
    }

    public function newAction()
    {
        $entity = new Cupo();
        
        $user = $this->get('security.context')->getToken()->getUser();        
        $id = $user->getId();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));       
        $breadcrumbs->addItem("Citas", $this->get("router")->generate("cupo_list"));
        $breadcrumbs->addItem("Nueva reserva");
        
        $form   = $this->createForm(new CupoType(), $entity, $options = array('userId' => $id));       
        $afiliacion = new Afiliacion();
        
        $form_afil = $this->createForm(new AfiliacionType(), $afiliacion);
    
        return $this->render('AgendaBundle:Cupo:new.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
        		'form_afil' => $form_afil->createView()
        ));
    }    

    public function saveAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();        
        $id = $user->getId();
        
        $cupo = new Cupo();
                
        $form = $this->createForm(new CupoType(), $cupo, array('userId' => $id));
        $request = $this->getRequest();
        $entity = $request->get($form->getName());
        
        $em = $this->getDoctrine()->getManager();

        $agenda = $em->getRepository('AgendaBundle:Agenda')->find($entity['agenda']);
        $paciente = $em->getRepository('ParametrizarBundle:Paciente')->findOneBy(array('identificacion' => $entity['paciente']));
        $cargo = $em->getRepository('ParametrizarBundle:Cargo')->find($entity['cargo']);        
        $user = $this->get('security.context')->getToken()->getUser();
        
                
        if($agenda && $paciente && $cargo)
        {
            // se valida la informacion de la cita que el paciente no cuente con una cita ya asignada en la misma agenda
            $validarCita = $em->getRepository('AgendaBundle:Cupo')->findBy(array('cargo'=>$cargo->getId(),'agenda'=> $agenda->getId(),'paciente'=>$paciente->getId()));
            
            if($validarCita)
            {
                $this->get('session')->getFlashBag()->add('error', 'El paciente cuenta con una cita asignada en esta agenda con este mismo procedimiento.');        		
        	return $this->redirect($this->generateUrl('cupo_new'));
            }
            
            $hora = new \DateTime($entity['hora']);

            $cupo->setHora($hora);
            $cupo->setRegistra($user->getId());
            $cupo->setPaciente($paciente);
            $cupo->setCargo($cargo);
            $cupo->setEstado('A');
            $cupo->setNota('');
            $cupo->setAgenda($agenda);        	
            $cupo->setCliente($entity['cliente']);

            $em->persist($cupo);
            $em->flush();

            $this->get('session')->getFlashBag()->add('ok', 'La reserva ha sido creada éxitosamente.');

            return $this->redirect($this->generateUrl('cupo_show', array('id' => $cupo->getId())));
        }else{
        	$this->get('session')->getFlashBag()->add('info', 'Hay informacion incompleta para la reserva del cupo.');        		
        	return $this->redirect($this->generateUrl('cupo_new'));
        }
        
        
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();    
        $cupo = $em->getRepository('AgendaBundle:Cupo')->find($id);
    
        if (!$cupo) {
            throw $this->createNotFoundException('La reserva solicitada no existe.');
        }        
        $usuario = $em->getRepository('UsuarioBundle:Usuario')->find($cupo->getRegistra());
        
        if (!$usuario) {
        	throw $this->createNotFoundException('El usuario relacionado con el cupo no existe.');
        }
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Citas", $this->get("router")->generate("cupo_list"));
        $breadcrumbs->addItem("Detalle reserva");
                    
        return $this->render('AgendaBundle:Cupo:show.html.twig', array(
                'cupo'  => $cupo,
        		'usuario' => $usuario
        ));
    }

    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();    
        $entity = $em->getRepository('AgendaBundle:Cupo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('La reserva solicitada no existe');
        }
        
        $user = $this->get('security.context')->getToken()->getUser();        
        $id = $user->getId();
                
        $editForm = $this->createForm(new CupoType(), NULL, array('userId' => $id));
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
        $breadcrumbs->addItem("Citas", $this->get("router")->generate("cupo_list"));
        $breadcrumbs->addItem("Detalle ",$this->get("router")->generate("cupo_show",array("id" => $entity->getId())));
        $breadcrumbs->addItem("Modificar reserva");

        return $this->render('AgendaBundle:Cupo:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
        ));
    }

    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CupoType());
        $request = $this->getRequest();
        $entity = $request->get($form->getName());

        if ($id != $entity['hora']) 
        {

            $cupo = $em->getRepository('AgendaBundle:Cupo')->find($id);

            if ($cupo) 
            {
                $paciente = $em->getRepository('ParametrizarBundle:Paciente')->findOneBy(array('identificacion' => $entity['paciente']));
                $cargo = $em->getRepository('ParametrizarBundle:Cargo')->find($entity['cargo']);
                $agenda = $em->getRepository('AgendaBundle:Agenda')->find($entity['agenda']);

                $user = $this->get('security.context')->getToken()->getUser();

                $hora = new \DateTime($entity['hora']);
                $cupo->setRegistra($user->getId());
                $cupo->setPaciente($paciente);
                $cupo->setCargo($cargo);
                $cupo->setAgenda($agenda);
                $cupo->setEstado('A');				
                $cupo->setCliente($entity['cliente']);
                $cupo->setHora($hora);

                $em->persist($cupo);
                $em->flush();
            }
        } else {

                $cupo = $em->getRepository('AgendaBundle:Cupo')->find($id);
                $paciente = $em->getRepository('ParametrizarBundle:Paciente')->findOneBy(array('identificacion' => $entity['paciente']));
                $cargo = $em->getRepository('ParametrizarBundle:Cargo')->find($entity['cargo']);

                $user = $this->get('security.context')->getToken()->getUser();

                $cupo->setRegistra($user->getId());
                $cupo->setPaciente($paciente);
                $cupo->setCargo($cargo);
                $cupo->setEstado('A');			
                $cupo->setCliente($entity['cliente']);

                $em->persist($cupo);
                $em->flush();
        }
        $this->get('session')->getFlashBag()->add('ok', 'La información de la reserva ha sido modificada éxitosamente.');
        return $this->redirect($this->generateUrl('cupo_show', array('id' => $cupo->getId())));
    }
    
    public function deleteAction()// pendiente por eliminar
    {    	
    	$request = $this->get('request');
    	$cupo=$request->request->get('cupo');    	    	    	
    	$em = $this->getDoctrine()->getManager();    
    	$cupo = $em->getRepository('AgendaBundle:Cupo')->find($cupo);
    
    	if (!$cupo) {
        	$response=array("responseCode"=>400, "msg"=>"El cupo solicitado es incorrecto.");
    		
    		$return=json_encode($response);
    		return new Response($return,200,array('Content-Type'=>'application/json'));
        }
        
        // cancelar el cupo se asigana la C y no se modifica ninguna otra informacion para tener control sobre las citas
        $cupo->setEstado('C');
        
        $em->persist($cupo);
        $em->flush();
    
    	$response=array("responseCode"=>200);
    
    	$return = json_encode($response);
        return new Response($return,200,array('Content-Type'=>'application/json'));
    }

    public function searchAction()
    {
    	$breadcrumbs = $this->get("white_october_breadcrumbs");
    	$breadcrumbs->addItem("Inicio", $this->get("router")->generate("agenda_list"));
    	$breadcrumbs->addItem("Citas", $this->get("router")->generate("cupo_list"));
    	$breadcrumbs->addItem("Buscar");
    
    	return $this->render('AgendaBundle:Cupo:search.html.twig');
    }

    public function ajaxBuscarAction()
    {    
        $request = $this->get('request');

        $paciente = $request->request->get('paciente');
        $agenda = $request->request->get('agenda');
        $cargo = $request->request->get('cargo');
        $reserva = $request->request->get('cupo');

        if (is_numeric($paciente) && is_numeric($agenda) && is_numeric($cargo)) {

                $em = $this->getDoctrine()->getManager();
                $entity = $em->getRepository('AgendaBundle:Agenda')->find($agenda);
                $ncupos = ((($entity->getFechaFin()->getTimestamp() - $entity->getFechaInicio()->getTimestamp()) / 60) / $entity->getIntervalo());
                $turno = $entity->getFechaInicio();

                $response = array("fecha" => $entity->getFechaInicio()->format('Y-m-d'));

                for ($i = 0; $i < $ncupos; $i++) {

                        $cupos[] = $turno->format('H:i');
                        $turno->add(new \DateInterval('PT' . $entity->getIntervalo() . 'M'));
                }

                $query = $em->createQuery(' select c
                                from
                                     dlaser\AgendaBundle\Entity\Cupo c
                                where
                                     c.agenda = :agenda');

                $query->setParameter('agenda', $agenda);
                $listCupos = $query->getArrayResult();                
                $iterableResult = $query->iterate();

                foreach ($iterableResult AS $row) {
                        $cupo = $row[0];
                        $cupillos[] = $cupo->getHora()->format('H:i');
                }

                if (isset($cupillos)) {
                        $result = array_diff($cupos, $cupillos);
                } else {
                        $result = array_diff($cupos, array());
                }

                if (isset($result))
                {
                    $response['responseCode'] = 200;
                    $response['cupo'] = $result;

                    // cargando informacion para los cupos asignados
                    foreach($listCupos as $key => $value)
                    {
                        $response['cuposList'][$key] = $value;
                    }
                    // se iteran los campos de los cupos para asignar el nombre completo correspondiente a su estado 
                    // las fechas de igual forma se iteran para generar un formato y dar salida como string
                    $int = 0;          $estado = $this->getEstadosDeCupos();
                    foreach($response['cuposList'] as $mi_cupo)
                    {                	
                        $response['cuposList'][$int]['estado'] = $estado[$mi_cupo['estado']];
                        $response['cuposList'][$int]['hora'] = $mi_cupo['hora']->format('d/m/Y H:i');
                        $int ++;                		
                    }
                    // end objeto para listar los cupos asignados
                } else {
                        $response['responseCode'] = 400;
                        $response['msg'] = "La agenda no tiene cupos definidos.";
                }

                $return = json_encode($response);
                return new Response($return, 200,
                                array('Content-Type' => 'application/json'));
        }
    }

    public function ajaxListarAction(){
    
        $request = $this->get('request');        
        $agenda=$request->request->get('agenda');
    
        if(is_numeric($agenda)){
            
            $em = $this->getDoctrine()->getManager();
    
            $query = $em->createQuery(' SELECT c.id,
                    c.hora,
                    c.estado,
                    c.nota,
                    c.registra,
                    c.verificacion,                    
                    p.id AS paciente,
            		p.priNombre, 
                    p.segNombre, 
                    p.priApellido, 
                    p.segApellido, 
            		p.movil,
                    car.nombre
                    FROM AgendaBundle:Cupo c
                    LEFT JOIN c.paciente p
                    LEFT JOIN c.cargo car
                    WHERE c.agenda = :agenda
                    ORDER BY c.hora ASC');
            
            
            $query->setParameter('agenda', $agenda);            
            $cupo = $query->getArrayResult();
            
            $estado = $this->getEstadosDeCupos();            
            
            
            if (!$cupo){
                $response=array("responseCode"=>400, "msg"=>"La agenda no tiene cupos definidos.");
            }else{           
    
                $response=array("responseCode"=>200);
    
                foreach($cupo as $key => $value)
                {
                    $response['cupo'][$key] = $value;
                }
                
                // se iteran los campos de los cupos para asignar el nombre completo correspondiente a su estado 
                // las fechas de igual forma se iteran para generar un formato y dar salida como string
                $int = 0;          
                foreach($response['cupo'] as $mi_cupo)
                {                	
                	$response['cupo'][$int]['estado'] = $estado[$mi_cupo['estado']];
                	$response['cupo'][$int]['hora'] = $mi_cupo['hora']->format('d/m/Y H:i');
                	$int ++;                		
                }
            }
    
            $return=json_encode($response);
            return new Response($return,200,array('Content-Type'=>'application/json'));
        }
    }

    /**
     * @uses Función que consulta un cupo por un parametro definido.
     *
     * @param ninguno
     */
public function ajaxBuscarCupoAction() {

		$request = $this->get('request');		
		$valor = $request->request->get('valor');

		$em = $this->getDoctrine()->getManager();
		$reserva = $em->getRepository('AgendaBundle:Cupo')->findAjaxBuscarCupo($valor);
		

		if (!$reserva) {
			$response = array("responseCode" => 400, "msg" => "No existen reservas para los parametros de consulta ingrasados.");
		} else {
                        $estado = $this->getEstadosDeCupos();

                        $response = array("responseCode" => 200);

                        foreach ($reserva as $key => $value) {
                                $response['cupo'][$key] = $value;
                        }			
                        // se iteran los campos de los cupos para asignar el nombre completo correspondiente a su estado
                        // las fechas de igual forma se iteran para generar un formato y dar salida como string
                        $int = 0;
                        foreach($response['cupo'] as $mi_cupo)
                        {
                                $response['cupo'][$int]['estado'] = $estado[$mi_cupo['estado']];
                                $response['cupo'][$int]['hora'] = $mi_cupo['hora']->format('d/m/Y H:i');
                                $int ++;
                        }
		}

		$return = json_encode($response);
		return new Response($return, 200, array('Content-Type' => 'application/json'));

	}
	
	public function doActivityAction($factura)
	{
		$request = $this->get('request');
		$estado = $request->request->get('estado');
		$textarea = $request->request->get('textarea');
		
		$em = $this->getDoctrine()->getManager();
		$factura = $em->getRepository('ParametrizarBundle:Factura')->find($factura);
		
		if (!$factura) {			
			
			$response = array("responseCode"=>400, "msg"=>"La factura solicitada no existe");
			
			$return=json_encode($response);
			return new Response($return,200,array('Content-Type'=>'application/json'));
		}
		
		$cupo = $factura->getCupo();	
		$cupo->setEstado($estado);
		$cupo->setVerificacion($textarea);		
		$factura->setEstado('I');
		
		$em->persist($factura);
		$em->persist($cupo);
		$em->flush();
		
		$this->get('session')->getFlashBag()->add('ok', 'La Actividad se registro éxitosamente.');		
		$response=array("responseCode"=>200, "msg"=>"La Actividad se registro éxitosamente.");
		
		$return = json_encode($response);
		return new Response($return,200,array('Content-Type'=>'application/json'));
	} 
	
	public function ajaxEstadoCupoAction()
	{
		$request = $this->get('request');
		$estado = $request->request->get('estado');
		$textarea = $request->request->get('textarea');
		$cupo = $request->request->get('cupo');
		$fecha = new \DateTime('now');
						
		$em = $this->getDoctrine()->getManager();
		$cupo = $em->getRepository('AgendaBundle:Cupo')->find($cupo);
		
		if (!$cupo) {
			$response = array("responseCode"=>400, "msg"=>"El cupo solicitado es incorrecto.");
		
			$return=json_encode($response);
			return new Response($return,200,array('Content-Type'=>'application/json'));
		}		
		
		$time_cupo = $cupo->getHora()->format('Y-m-d H:i:s');
		$time_now = $fecha->format('Y-m-d 23:59:00');
		
		if( $estado == 'CU' && ($time_cupo >= $time_now))
		{
			$response = array("responseCode"=>400, "msg"=>"El cupo solicitado no cumple con la fecha adecuada para ser registrada.");
			
			$return=json_encode($response);
			return new Response($return,200,array('Content-Type'=>'application/json'));
			
		}else if($estado == 'CU')
		{			
			$response=array("responseCode"=>210);
			
			$return = json_encode($response);
			return new Response($return,200,array('Content-Type'=>'application/json'));
		}
		
		// cancelar el cupo se asigana la C y no se modifica ninguna otra informacion para tener control sobre las citas
		
		$cupo->setNota($textarea);
		$cupo->setEstado($estado);
		$em->persist($cupo);
		$em->flush();
		
		$response=array("responseCode"=>200, "msg"=>"Informacion registrada con exito.");
		
		$return = json_encode($response);
		return new Response($return,200,array('Content-Type'=>'application/json'));		
	}
        
        public function getEstadosDeCupos()
        {
            return array(
            		'A'=> '<span class="label label-info">Asignado</span>',
                        'CA'=>'<span class="label label-important">Cancelado</span>',
                        'RE'=> 'Reprogramado',
                        'CO'=> '<span class="label label-warning">Confirmado</span>',
                        'CU'=> '<span class="label label-success">Cumplida</span>',
                        'IN'=> 'Incumplida',
                        'PN'=> '<span class="label label-inverse">Sin Cita x</span>',
                        'DE'=> 'Desertor',
                        'NO'=> 'NO Inicia', 'FI' => 'Finalizado',
                        'PD'=> '<span class="label">Con Cita</span>'
            		);
        }
}