<?php

namespace dlaser\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContratoType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        $user = $options['userId'];
    
        $builder
        ->add('contacto', 'text', array('required' => true, 'attr' => array('placeholder' => 'Ingrese el nombre del contacto', 'autofocus'=>'autofocus')))
        ->add('cargo', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese el cargo del contacto')))
        ->add('telefono', 'integer', array('required' => false, 'label' => 'Teléfono', 'attr' => array('placeholder' => 'Número telefonico')))
        ->add('celular', 'text', array('required' => false, 'attr' => array('placeholder' => 'Número movil')))
        ->add('email', 'text', array('attr' => array('placeholder' => 'Correo electrónico')))
        ->add('estado', 'choice', array('required' => true, 'choices' => array('A' => 'Activo', 'I' => 'Inactivo')))
        ->add('porcentaje', 'percent', array('required' => true, 'precision' => 0, 'attr' => array('placeholder' => 'Porcentaje pactado')))
        ->add('observacion', 'text', array('required' => false, 'attr' => array('placeholder' => 'Ingrese observación del contacto')))
         
        ->add('sede', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Sede',
                'mapped' => true,
                'empty_value' => 'Elige una sede',
                'query_builder' => function(EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('s','u')
                        ->leftJoin("s.usuario", "u")
                        ->where("u.id = :id")
                        ->setParameter('id', $user);
                        }
        ))        
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(
                array('data_class' => 'dlaser\ParametrizarBundle\Entity\Contrato',
                      'userId' => '')
                
                );
    }

    public function getName()
    {
        return 'newContrato';
    }
}