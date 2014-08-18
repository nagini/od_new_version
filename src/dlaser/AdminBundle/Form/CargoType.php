<?php
namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CargoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('cups', 'text', array('label' => 'Código', 'required' => true, 'attr' => array('placeholder' => 'Código cups de la actividad', 'autofocus'=>'autofocus')))
        ->add('nombre', 'text', array('required' => true, 'attr' => array('placeholder' => 'Nombre de la actividad')))
        ->add('indicacion', 'textarea', array('required' => false, 'label' => 'Indicación', 'attr' => array('placeholder' => 'Indicación de la actividad')))
        ->add('valor', 'integer', array('required' => false, 'attr' => array('placeholder' => 'Valor de la actividad')))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Cargo'));
    }

    public function getName()
    {
        return 'newCargo';
    }
}