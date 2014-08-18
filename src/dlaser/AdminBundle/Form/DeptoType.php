<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class DeptoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Depto',
                'query_builder' => function(EntityRepository $repositorio) {
                    return $repositorio->createQueryBuilder('d')
                        ->orderBy('d.nombre', 'ASC');
                },
                'preferred_choices' => array(24)                
            ))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Depto'));
    }
    
    public function getDefaultOptions(array $opciones)
    {
        return array('data_class' => 'dlaser\ParametrizarBundle\Entity\Depto');
    }    

    public function getName()
    {
        return 'Departamento';
    }
}