<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SedeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nombre', 'autofocus'=>'autofocus'))) 
        ->add('ciudad', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese la ciudad')))
        ->add('direccion', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese la dirección')))
        ->add('telefono', 'integer', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el teléfono')))
        ->add('movil', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese el movil')))
        ->add('email', 'email', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el email')))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Sede'));
    }

    public function getName()
    {
        return 'newSede';
    }
}