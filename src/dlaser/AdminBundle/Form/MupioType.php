<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MupioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('municipio', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Mupio',
                'query_builder' => function(EntityRepository $repositorio) {
                    return $repositorio->createQueryBuilder('m')
                        ->orderBy('m.nombre', 'ASC');
                }                
            ))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\ParametrizarBundle\Entity\Mupio'));
    }
    
    public function getDefaultOptions(array $opciones)
    {
        return array('data_class' => 'dlaser\ParametrizarBundle\Entity\Mupio');
    }
    

    public function getName()
    {
        return 'Municipio';
    }
}