<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nit', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nit', 'autofocus'=>'autofocus',)))
        ->add('cod_eps', 'text', array('required' => true, 'label' => 'Código de la eps', 'attr' => array('placeholder' => 'Ingrese el código de la eps')))
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese nombre')))
        ->add('razon', 'text', array('required' => false, 'label' => 'Razón social', 'attr' => array('placeholder' => 'razón social')))
        ->add('direccion', 'text', array('required' => false, 'attr' => array('placeholder' => 'Dirección')))
        ->add('telefono', 'text', array('required' => false, 'attr' => array('placeholder' => 'Teléfono')))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Cliente'));
    }

    public function getName()
    {
        return 'newCliente';
    }
}