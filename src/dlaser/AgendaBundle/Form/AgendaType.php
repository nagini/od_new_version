<?php

namespace dlaser\AgendaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AgendaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['userId'];
        
        $builder
        ->add('fecha_inicio', 'datetime', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy H:m','label' => 'Fecha de inicio',  'required' => true,'attr' => array('placeholder' => 'DIA/MES/AÑO HORA:MINUTO')))
        ->add('fecha_fin', 'datetime', array('widget' => 'single_text', 'format' => 'dd/MM/yyyy H:m', 'label' => 'Fecha de fin', 'required' => true, 'attr' => array('placeholder' => 'DIA/MES/AÑO HORA:MINUTO')))
        ->add('intervalo', 'integer', array('attr' => array('placeholder' => 'Ingrese el tiempo de atención'), 'required' => true))
        ->add('estado', 'choice', array('choices' => array('A' => 'Activa', 'I' => 'Inactiva'), 'required' => true))
        ->add('nota', 'text', array('label' => 'Nombre Agenda','attr' => array('placeholder' => 'Ingrese su nota'), 'required' => false))
                
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
        ->add('usuario', 'entity', array(
                'class' => 'dlaser\\UsuarioBundle\\Entity\\Usuario',
        		'label' => 'Profesional',
                'required' => true,
                'empty_value' => 'Selecciona un usuario',
                'query_builder' => function(EntityRepository $repositorio) {
                return $repositorio->createQueryBuilder('u')
                ->where('u.perfil = :role')
                ->setParameter('role', 'ROLE_MEDICO')
                ->orderBy('u.nombre', 'ASC');
        }
        ))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array(
                            'data_class' => 'dlaser\AgendaBundle\Entity\Agenda',
                            'userId' => ''
            ));
    }

    public function getName()
    {
        return 'Agenda';
    }
}