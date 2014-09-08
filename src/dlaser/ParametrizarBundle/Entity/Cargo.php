<?php

namespace dlaser\ParametrizarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;


/**
 * dlaser\ParametrizarBundle\Entity\Cargo
 *
 * @ORM\Table(name="cargo")
 * @ORM\Entity
 * @DoctrineAssert\UniqueEntity(fields="cups", message="Este codigo ya esta siendo usado en el sistema")
 * @DoctrineAssert\UniqueEntity(fields="nombre", message="Este nombre ya esta siendo usado en el sistema.")
 */
class Cargo
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
     * @var string $cups
     * 
     * @ORM\Column(name="cups", type="string", length=8, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 8)     
     */
    private $cups;

    /**
     * @var string $nombre
     * 
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 255, maxMessage="Este campo debe contener menos de 255 caracteres")
     */
    private $nombre;

    /**
     * @var text $indicacion
     * 
     * @ORM\Column(name="indicacion", type="text", nullable=true)
     * @Assert\Length(max = 255, maxMessage="Este campo debe contener menos de 255 caracteres")
     */
    private $indicacion;

    /**
     * @var integer $valor
     * 
     * @ORM\Column(name="valor", type="integer", nullable=true)
     * @Assert\Range(min = 1,max = 9999999, minMessage = "Este valor debe ser mayor o igual a 1", maxMessage = "Este valor debe ser menor o igual a 9999999")     
     */
    private $valor;
    
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
     * Set cups
     *
     * @param string $cups
     */
    public function setCups($cups)
    {
        $this->cups = $cups;
    }

    /**
     * Get cups
     *
     * @return string 
     */
    public function getCups()
    {
        return $this->cups;
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
     * Set indicacion
     *
     * @param text $indicacion
     */
    public function setIndicacion($indicacion)
    {
        $this->indicacion = $indicacion;
    }

    /**
     * Get indicacion
     *
     * @return text 
     */
    public function getIndicacion()
    {
        return $this->indicacion;
    }

    /**
     * Set valor
     *
     * @param integer $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * Get valor
     *
     * @return integer 
     */
    public function getValor()
    {
        return $this->valor;
    }
}