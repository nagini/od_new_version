<?php

namespace dlaser\AgendaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CupoType extends AbstractType
{           
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $id = $options['userId'];
        
        $builder        
        ->add('sede', 'entity', array(
                'class' => 'dlaser\\ParametrizarBundle\\Entity\\Sede',
                'mapped' => false,
                'query_builder' => function(EntityRepository $er) use ($id) {
                        return $er->createQueryBuilder('s','u')
                        ->leftJoin("s.usuario", "u")
                        ->where("u.id = :id")
                        ->setParameter('id', $id);
                        }
        ))
        ->add('paciente', 'integer', array('label' => 'Cedula Paciente:','required' => true, 'attr' => array('placeholder' => 'Ingrese la identificacion del paciente')))
        ->add('cliente', 'choice', array('label' => 'Cliente:', 'choices' => array('' => '--')))
        ->add('cargo', 'choice', array('label' => 'Procedimiento:','choices' => array('' => '--')))
        ->add('agenda', 'choice', array('label' => 'Asignar agenda:','choices' => array('' => '--')))
        ->add('hora', 'choice', array('label' => 'Hora agenda:','choices' => array('' => '--')))        
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array(
                            'data_class' => 'dlaser\AgendaBundle\Entity\Cupo',
                            'userId' => ''
                    ));
    }

    public function getName()
    {
        return 'Cupo';
    }
}