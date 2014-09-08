<?php

namespace dlaser\AgendaBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class CupoRepository extends EntityRepository
{
	
	public function findInformeGeneral($con_sede,$desde,$hasta, $estado)
	{
		$em = $this->getEntityManager();
		$dql= " SELECT			
                            c.hora as horaCupo,
                            c.nota,											
                            c.verificacion,
                            a.fechaInicio as fechaAgenda,
                            a.nota as agenda,
                            p.priNombre,
                            p.segNombre,
                            p.priApellido,
                            p.segApellido,
                            p.identificacion,
                            p.movil,
                            car.nombre as cargo	
    			FROM AgendaBundle:Cupo c
                            LEFT JOIN c.paciente p
                            LEFT JOIN c.cargo car
                            LEFT JOIN c.agenda a
                            LEFT JOIN a.sede s
    			WHERE
                            c.hora > :inicio AND
                            c.hora <= :fin AND
                            c.estado = :estado
                            ".$con_sede."
                        ORDER BY p.priNombre ASC";
	
		$query = $em->createQuery($dql);
	
		$query->setParameter('inicio', $desde);
		$query->setParameter('fin', $hasta);
		$query->setParameter('estado', $estado);
			
		return $query->getResult();
	}
	
	public function findInformePaciente($paciente)
	{
		$em = $this->getEntityManager();
		$dql= " SELECT
			
					c.hora as horaCupo,
					c.nota,
					c.verificacion,
					c.estado,
					a.fechaInicio as fechaAgenda,
					a.nota as agenda,
	    			car.nombre as cargo
    			FROM AgendaBundle:Cupo c
    				LEFT JOIN c.paciente p
    				LEFT JOIN c.cargo car
					LEFT JOIN c.agenda a
    				LEFT JOIN a.sede s
    			WHERE			    	
			    	c.hora < :fecha AND					
					p.id = :paciente			    	
			    ORDER BY c.estado, c.hora DESC";
	
		$query = $em->createQuery($dql);
	
		
        $fecha = new \DateTime('now');
        $query->setParameter('paciente', $paciente);
        $query->setParameter('fecha', $fecha->format('Y-m-d 23:59:00'));
		
			
		return $query->getResult();
	}
	
	
	public function findAjaxBuscarCupo($valor)
	{
		$em = $this->getEntityManager();
		$dql= 		"SELECT c.id,
	    				c.hora,   				
						c.estado,
						c.nota,
	    				p.priNombre,
	    				p.segNombre,
	    				p.priApellido,
	    				p.segApellido,
						p.movil,
	    				car.nombre as cargo,						
	    				s.nombre as sede
    				FROM AgendaBundle:Cupo c
    				LEFT JOIN c.paciente p
    				LEFT JOIN c.cargo car
					LEFT JOIN c.agenda a					    				
    				LEFT JOIN a.sede s
    				WHERE 
    					p.identificacion = :identificacion AND    					
    					c.hora >= :fechaI						
    				ORDER BY c.hora ASC";
		
		$query = $em->createQuery($dql);
		$fecha = new \DateTime('now');
		$query->setParameter('fechaI', $fecha->format('Y-m-d 00:00:00'));
		$query->setParameter('identificacion', $valor);
		
		return  $query->getArrayResult();
	}
	
	public function findAppointments($sede)
	{
		$em = $this->getEntityManager();
		$dql= 		"SELECT 
						c.id,	
						c.cliente,						
						p.id as paciente,    				
						c.estado,						
						c.verificacion,
	    				p.priNombre,
	    				p.segNombre,
	    				p.priApellido,
	    				p.segApellido,
						p.identificacion,
						p.movil,
	    				car.nombre as cargo	    				
    				FROM AgendaBundle:Cupo c
    				LEFT JOIN c.paciente p
    				LEFT JOIN c.cargo car
					LEFT JOIN c.agenda a
    				LEFT JOIN a.sede s
    				WHERE
    					s.id = :sede AND
    					c.estado = 'PN'
    				ORDER BY c.hora ASC";
		
		$query = $em->createQuery($dql);	
		$query->setParameter('sede', $sede);
		
		return  $query->getArrayResult();
	}

}
