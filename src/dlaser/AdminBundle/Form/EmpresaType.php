<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nit', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nit', 'autofocus'=>'autofocus'))) 
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese nombre')))
        ;
    }
    
	/*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Empresa'));
    }

    public function getName()
    {
        return 'newEmpresa';
    }
}