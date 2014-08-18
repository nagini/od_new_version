<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FacturacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('inicio', 'hidden', array('required' => true))
        ->add('fin', 'hidden', array('required' => true))
        ->add('sedes', 'hidden', array('required' => true))
        ->add('concepto', 'textarea', array('label' => 'Concepto', 'required' => true, 'attr' => array('autofocus'=>'autofocus')))
        ->add('nota', 'textarea', array('required' => false))
        ->add('subtotal', 'integer', array('required' => true))
        ->add('iva', 'integer', array('required' => true))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Facturacion'));
    }

    public function getName()
    {
        return 'newFacturaFinal';
    }
}