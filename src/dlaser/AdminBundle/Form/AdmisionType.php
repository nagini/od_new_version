<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdmisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder 
        ->add('fecha', 'datetime',  array('label' => 'Fecha:', 'required' => true))
        ->add('autorizacion', 'text', array('required' => true, 'attr' => array('placeholder' => 'Número de autorización', 'autofocus' => 'autofocus')))
        ->add('valor', 'integer', array('required' => true))
        ->add('copago', 'integer', array('required' => true))
        ->add('descuento', 'integer', array('required' => false))
        ->add('cliente', 'entity', array('required' => true,'class' => 'dlaser\ParametrizarBundle\Entity\Cliente', 'empty_value' => 'Elige una aseguradora'))
        ->add('cargo', 'entity', array('required' => true, 'class' => 'dlaser\ParametrizarBundle\Entity\Cargo', 'empty_value' => 'Elige una sede'))
        ->add('sede', 'entity', array('required' => true, 'class' => 'dlaser\ParametrizarBundle\Entity\Sede', 'empty_value' => 'Elige una sede'))
        ->add('estado', 'choice', array('choices' => array('I' => 'Informado', 'P' => 'Pendiente', 'X' => 'Anulado'), 'required' => true))
        ->add('observacion', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese una observación')))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Factura'));
    }

    public function getName()
    {
        return 'editAdmision';
    }
}