<?php
namespace dlaser\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class pacienteSearchType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add('nombre','text',array('label'=> 'Busqueda rapida:','mapped' => false,
				'attr' => array('placeholder' => 'Ingrese el nombre',
						'autofocus'=>'autofocus')))
				
		->add('option', 'choice', array(
				'label'=> 'Opcion de busqueda:',
				'mapped' => false,
				 'choices' => array(
				 		'cedula'=> 'Cedula',
				 		'nombre'=> 'Nombre',
				 		'apellido'=> 'Apellido',),
				'multiple'=>false,
				))
						;
	}

	public function getName()
	{
		return 'searchPaciente';
	}
}
