<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @UniqueEntity("name")
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var Assos
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Assos", inversedBy="categories")
     */
    private $assos;

    /**
     * Constructor
     */
    public function __construct()
    {
        // On instancie un tableau vide pour éviter un erreur PHP lorsque l'on boucle sur une collection
        $this->assos = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add asso
     *
     * @param \AppBundle\Entity\Assos $asso
     *
     * @return Category
     */
    public function addAsso(\AppBundle\Entity\Assos $asso)
    {
        $this->assos[] = $asso;

        return $this;
    }

    /**
     * Remove asso
     *
     * @param \AppBundle\Entity\Assos $asso
     */
    public function removeAsso(\AppBundle\Entity\Assos $asso)
    {
        $this->assos->removeElement($asso);
    }

    /**
     * Get assos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssos()
    {
        return $this->assos;
    }
}
