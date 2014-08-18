<?php

namespace dlaser\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\SecurityExtraBundle\Security\Util\String;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * dlaser\UsuarioBundle\Entity\Usuario
 * 
 * @ORM\Table(name="usuario")
 * @DoctrineAssert\UniqueEntity("cc")
 * @ORM\Entity 
 */
class Usuario implements UserInterface, \Serializable
{
     
    /**
     * Método requerido por la interfaz UserInterface
     */
    function eraseCredentials()
    {
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function getRoles()
    {
        return array($this->getPerfil());
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function getSalt()
    {
        return null;
    }
    
    /**
     * Método requerido por la interfaz UserInterface
     */
    function getUsername()
    {
        return $this->getNombre();
    }
    
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var integer $cc
     * @ORM\Column(name="cc", type="integer", nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")     
     * @Assert\Length(min = 8) 
     * @Assert\Length(max = 13)     
     * 
     */
    private $cc;

    /**
     * @var string $nombre
     * @ORM\Column(name="nombre", type="string", length=60, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 60)
     * 
     */
    private $nombre;

    /**
     * @var string $apellido
     * @ORM\Column(name="apellido", type="string", length=60, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 60)
     * 
     */
    private $apellido;

    /**
     * @var string $perfil
     * @ORM\Column(name="perfil", type="string", length=13, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Length(max = 13)
     * 
     */
    private $perfil;

    /**
     * @var string $telefono
     * @ORM\Column(name="telefono", type="string", length=11, nullable=false)
     * @Assert\NotBlank(message="El valor ingresado no puede estar vacio.")
     * @Assert\Range(max = 99999999999)
     * 
     */
    private $telefono;

    /**
     * @var string $direccion
     *
     * @ORM\Column(name="direccion", type="string", length=60, nullable=true)
     * @Assert\Length(max = 60)
     */
    private $direccion;

    /**
     * @var string $tp
     * @ORM\Column(name="tp", type="string", length=11, nullable=true)
     * @Assert\Length(max = 11)
     * 
     */
    private $tp;

    /**
     * @var string $especialidad
     * @ORM\Column(name="especialidad", type="string", length=30, nullable=true)
     * @Assert\Length(max = 30)
     * 
     */
    private $especialidad;

    /**
     * @var string $password
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @Assert\NotBlank(groups={"registro"})
     * @Assert\Length(min = 6)
     * 
     */
    private $password;

    /**
     * @var string $email
     * @ORM\Column(name="email", type="string", length=200, nullable=true)
     * @Assert\Email()
     * 
     */
    private $email;

    /**
     * @var string $firma
     *
     * @ORM\Column(name="firma", type="string", length=255, nullable=true)
     */
    private $firma;

    

    /**
     * @var Sede
     *
     * @ORM\ManyToMany(targetEntity="dlaser\ParametrizarBundle\Entity\Sede", mappedBy="usuario")
     */
    private $sede;

    public function __construct()
    {        
	    $this->sede = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    public function __toString()
    {
        return $this->getNombre()." ".$this->getApellido();
    }
    
    public function serialize()
    {
       return serialize($this->getId());
    }
 
    public function unserialize($data)
    {
        $this->id = unserialize($data);
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
     * Set cc
     *
     * @param integer $cc
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    /**
     * Get cc
     *
     * @return integer 
     */
    public function getCc()
    {
        return $this->cc;
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
     * Set apellido
     *
     * @param string $apellido
     */
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    /**
     * Get apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->apellido;
    }

    /**
     * Set perfil
     *
     * @param string $perfil
     */
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;
    }

    /**
     * Get perfil
     *
     * @return string 
     */
    public function getPerfil()
    {
        return $this->perfil;
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
     * Set tp
     *
     * @param string $tp
     */
    public function setTp($tp)
    {
        $this->tp = $tp;
    }

    /**
     * Get tp
     *
     * @return string 
     */
    public function getTp()
    {
        return $this->tp;
    }

    /**
     * Set especialidad
     *
     * @param string $especialidad
     */
    public function setEspecialidad($especialidad)
    {
        $this->especialidad = $especialidad;
    }

    /**
     * Get especialidad
     *
     * @return string 
     */
    public function getEspecialidad()
    {
        return $this->especialidad;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firma
     *
     * @param string $firma
     */
    public function setFirma($firma)
    {
        $this->firma = $firma;
    }

    /**
     * Get firma
     *
     * @return string 
     */
    public function getFirma()
    {
        return $this->firma;
    }    

    /**
     * Add sede
     *
     * @param dlaser\ParametrizarBundle\Entity\Sede $sede
     */
    public function addSede(\dlaser\ParametrizarBundle\Entity\Sede $sede)
    {
        
        if (!$this->hasSede($sede)) {
        	$this->sede[] = $sede;
        	return true;
        }
        return false;
    }
    
    
    public function hasSede(\dlaser\ParametrizarBundle\Entity\Sede $sede)
    {
    	foreach ($this->sede as $value) {
    		if ($value->getId() == $sede->getId()) {
    			return true;
    		}
    	}
    	return false;
    }
    
    

    /**
     * Get sede
     *
     * @return Doctrine\Common\Collections\Collection $sede 
     */
    public function getSede()
    {
        return $this->sede;
    }
}
