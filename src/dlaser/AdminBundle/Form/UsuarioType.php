<?php

namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre', 	'text', array('label'=>'Nombre*','attr' => array('placeholder' => 'Primer nombre...','autofocus'=>'autofocus'))) 
        ->add('apellido', 	'text', array('label'=>'Apellido*'))
        ->add('cc', 		'integer',array('label'=>'CC*','attr' => array('placeholder' => 'Numero de cedula')))
        ->add('perfil', 	'choice', array('label'=>'Perfil*',
				  'choices' => array(
				  		'ROLE_ADMIN'=> 'Admin',
				  		'ROLE_MEDICO'=> 'Medico',
				  		'ROLE_AUX'=> 'Auxiliar',),
					'multiple'=>false,
					))
        
        ->add('telefono', 	'text',array('label'=>'Telefono*','attr' => array('placeholder' => 'Movil o Fijo')))
        ->add('direccion', 	'text', array('required' => false))
        ->add('tp', 		'text', array('required' => false,'attr' => array('placeholder' => 'Numero tarjeta profesional')))
        ->add('especialidad', 	'text', array('required' => false))
        ->add('password',  	'repeated', array('type'=>'password',
						       'invalid_message'=>'Las contraseñas deben coincidir',
						       'options'=>array('label'=>'Contraseña*'),'required' => false))
        ->add('email', 		'email',array('label'=>'Email*','attr' => array('placeholder' => 'Ejemplo@Ejemplo.com')))
        ;
    }
    
    /*setDefaultOptions() se indica el namespace de la entidad cuyos datos modifica este formulario.*/
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
    	$resolver
    	->setDefaults(array('data_class' => 'dlaser\UsuarioBundle\Entity\Usuario'));
    }

    public function getName()
    {
        return 'newUsuario';
    }
}
