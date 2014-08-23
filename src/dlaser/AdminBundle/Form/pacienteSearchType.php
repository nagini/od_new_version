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
				'attr' => array( 'class' => 'span4 search-query', 'placeholder' => 'Ingrese el parametro de busqueda',
						'autofocus'=>'autofocus')))
				
		->add('option', 'choice', array(
				'label'=> 'Opcion:',
				'mapped' => false,
				'attr' => array( 'class' => 'span2'),
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
