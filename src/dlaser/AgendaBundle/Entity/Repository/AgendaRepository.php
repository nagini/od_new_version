<?php

namespace dlaser\AgendaBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class AgendaRepository extends EntityRepository
{
	public function findMedicos()
	{
		$em = $this->getEntityManager();
		$dql = "SELECT u 
				FROM  UsuarioBundle:Usuario u
				WHERE u.perfil = 'ROLE_MEDICO'
				ORDER BY u.nombre ASC";
		
		$query = $em->createQuery($dql);	
		
		return $query->getResult();
	}
	
	public function findAgendaDelMedico($medico,$sede)
	{
		$em = $this->getEntityManager();
		$dql = "SELECT 
                            f.id,
                            c.hora,
                            c.estado as estadoCita,
                            f.fecha,            		
                            p.identificacion,
                            p.priNombre,
                            p.segNombre,
                            p.priApellido,
                            p.segApellido,
                            p.id as paciente,
                            cli.id as cliente,					
                            car.nombre as cargo,                                        
                            f.estado
                        FROM ParametrizarBundle:Factura f
                            LEFT JOIN f.cupo c
                            LEFT JOIN f.cliente cli
                            LEFT JOIN f.paciente p
                            LEFT JOIN f.cargo car
                            LEFT JOIN c.agenda a
                        WHERE f.sede = :sede
                            AND f.estado != :estado
                            AND f.estado != 'X'                                  		
                            AND a.usuario = :usuario
                        ORDER BY c.hora ASC";
		
		$query = $em->createQuery($dql);
		
		$fecha = new \DateTime('now');

        $query->setParameter('sede', $sede);
        $query->setParameter('estado', 'I');
        $query->setParameter('usuario', $medico);
        
        return  $query->getArrayResult();
	}
        
        public function findAgendasActivas()
	{
            $em = $this->getEntityManager();
            $dql = 'SELECT a.id, a.fechaInicio, a.fechaFin, a.intervalo, a.nota, a.estado, 
                           u.nombre AS profesional, s.nombre AS sede, s.id AS sedeid
                    FROM AgendaBundle:Agenda a
                    LEFT JOIN a.sede s
                    LEFT JOIN a.usuario u
                    WHERE a.fechaFin > :fi  
                    ORDER BY s.nombre ASC';

            $query = $em->createQuery($dql);                
            $query->setParameter('fi', new \DateTime('now'));	

            return $query->getResult();
	}
	
	
}
