<?php

namespace dlaser\ParametrizarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * dlaser\ParametrizarBundle\Entity\Cliente
 *
 * @ORM\Table(name="cliente")
 * @ORM\Entity
 */
class Cliente
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $codEps
     * 
     * @ORM\Column(name="cod_eps", type="string", length=20, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 20)     
     */
    private $codEps;

    /**
     * @var string $nit
     * 
     * @ORM\Column(name="nit", type="string", length=12, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Range(min = 1000000)     
     * @Assert\Length(max = 12)     
     */
    private $nit;

    /**
     * @var string $nombre
     * 
     * @ORM\Column(name="nombre", type="string", length=60, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 60)     
     */
    private $nombre;
    
    /**
     * @var string $razon
     *
     * @ORM\Column(name="razon", type="string", length=255, nullable=true)
     * @Assert\Length(max = 255)     
     */
    private $razon;
    
    /**
     * @var string $direccion
     *
     * @ORM\Column(name="direccion", type="string", length=80, nullable=true)
     * @Assert\Length(max = 80)     
     */
    private $direccion;
    
    /**
     * @var string $telefono
     *
     * @ORM\Column(name="telefono", type="string", length=10, nullable=true)
     * @Assert\Length(max = 10)
     */
    private $telefono;
    
    
    public function __toString()
    {
        return $this->getNombre();
    }    
    
   /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codEps
     *
     * @param string $codEps
     */
    public function setCodEps($codEps)
    {
        $this->codEps = $codEps;
    }

    /**
     * Get codEps
     *
     * @return string 
     */
    public function getCodEps()
    {
        return $this->codEps;
    }

    /**
     * Set nit
     *
     * @param string $nit
     */
    public function setNit($nit)
    {
        $this->nit = $nit;
    }

    /**
     * Get nit
     *
     * @return string 
     */
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }
    
    /**
     * Set razon
     *
     * @param string $razon
     */
    public function setRazon($razon)
    {
    	$this->razon = $razon;
    }
    
    /**
     * Get razon
     *
     * @return string
     */
    public function getRazon()
    {
    	return $this->razon;
    }
    
    /**
     * Set direccion
     *
     * @param string $direccion
     */
    public function setDireccion($direccion)
    {
    	$this->direccion = $direccion;
    }
    
    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
    	return $this->direccion;
    }
    
    /**
     * Set telefono
     *
     * @param string $telefono
     */
    public function setTelefono($telefono)
    {
    	$this->telefono = $telefono;
    }
    
    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
    	return $this->telefono;
    }
}