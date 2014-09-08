<?php

namespace dlaser\AdminBundle\Controller;;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use dlaser\ParametrizarBundle\Entity\Cliente;
use dlaser\ParametrizarBundle\Entity\Contrato;
use dlaser\AdminBundle\Form\ContratoType;

class ContratoController extends Controller
{
    
    public function newAction($id)
    {
        $user = $this->get('security.context')->getToken()->getUser(); 
        $user = $user->getId();        
        
        $entity = new Contrato();
        $form   = $this->createForm(new ContratoType(), $entity, array('userId' => $user));
        
        
        $em = $this->getDoctrine()->getManager();
        $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($id);
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));        
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Nuevo contrato");
        
    
        return $this->render('AdminBundle:Contrato:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
        		'cliente' => $cliente,
                'form'   => $form->createView()
        ));
    }
    
    public function saveAction($id)
    {
        $em = $this->getDoctrine()->getManager();        
        $cliente = $em->getRepository('ParametrizarBundle:Cliente')->find($id);
        
        if (!$cliente) {
            throw $this->createNotFoundException('El cliente solicitado no existe.');
        }
        $user = $this->get('security.context')->getToken()->getUser(); 
        $user = $user->getId();  
        
        $entity  = new Contrato();
        
        $request = $this->getRequest();
        $form    = $this->createForm(new ContratoType(), $entity, array('userId' => $user));
        $form->bind($request);
    
        if ($form->isValid()) {
            
            $contrato = $em->getRepository('ParametrizarBundle:Contrato')->findBy(array('cliente' => $cliente->getId(), 'sede' => $entity->getSede()->getId()));
            
            if($contrato){
            	
                $this->get('session')->getFlashBag()->add('error', 'Ya existe un contrato con la sede seleccionada.');            
                return $this->render('AdminBundle:Contrato:new.html.twig', array(
                        'entity' => $entity,
                        'id'    => $id,
                		'cliente' => $cliente,
                        'form'   => $form->createView()
                ));
            }                        
            $entity->setCliente($cliente);            
            $em->persist($entity);
            $em->flush();
    
            $this->get('session')->getFlashBag()->add('ok', 'El contrato ha sido creado éxitosamente.');    
            return $this->redirect($this->generateUrl('contrato_show', array("id" => $entity->getId())));    
        }    
        return $this->render('AdminBundle:Contrato:new.html.twig', array(
                'entity' => $entity,
                'id'    => $id,
        		'cliente' => $cliente,
                'form'   => $form->createView()
        ));
    
    }
    
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
        
        $cliente = $contrato->getCliente();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));        
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ".$contrato->getContacto());
        
        $actividades = $em->getRepository('ParametrizarBundle:Actividad')->findBy(array('contrato' => $id));        
        $paginator = $this->get('knp_paginator');
        $actividades = $paginator->paginate($actividades, $this->getRequest()->query->get('page', 1),15);        
    
        return $this->render('AdminBundle:Contrato:show.html.twig', array(
                'contrato'    => $contrato,
                'actividades'    => $actividades,
        ));
    }
    
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();    
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe.');
        }
        
        $user = $this->get('security.context')->getToken()->getUser(); 
        $user = $user->getId();  
        
        $cliente = $contrato->getCliente();
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Inicio", $this->get("router")->generate("empresa_list"));
        $breadcrumbs->addItem("Cliente Listar",$this->get("router")->generate("cliente_list"));
        $breadcrumbs->addItem("Detalle ".$cliente->getNombre(),$this->get("router")->generate("cliente_show",array("id" => $cliente->getId())));
        $breadcrumbs->addItem("Detalle ",$this->get("router")->generate("contrato_show",array("id" => $contrato->getId())));
        $breadcrumbs->addItem("Modificar ".$contrato->getContacto());
    
        $editForm = $this->createForm(new ContratoType(), $contrato, array('userId' => $user));
    
        return $this->render('AdminBundle:Contrato:edit.html.twig', array(
                'entity'      => $contrato,
                'edit_form'   => $editForm->createView(),
        ));
    }
    
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();    
        $contrato = $em->getRepository('ParametrizarBundle:Contrato')->find(array('id' => $id));
    
        if (!$contrato) {
            throw $this->createNotFoundException('El contrato solicitado no existe');
        }
    
        $user = $this->get('security.context')->getToken()->getUser(); 
        $user = $user->getId();  
        
        $editForm   = $this->createForm(new ContratoType(), $contrato,  array('userId' => $user));    
        $request = $this->getRequest();    
        $editForm->bind($request);
    
        if ($editForm->isValid()) {
    
            $em->persist($contrato);
            $em->flush();
    
            $this->get('session')->getFlashBag()->add('ok', 'La información del contrato ha sido modificada éxitosamente.');    
            return $this->redirect($this->generateUrl('contrato_edit', array('id' => $id)));
        }
    
        return $this->render('AdminBundle:Contrato:edit.html.twig', array(
                'entity'      => $contrato,
                'edit_form'   => $editForm->createView(),
        ));
    }

}