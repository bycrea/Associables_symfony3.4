<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Review
 *
 * @ORM\Table(name="review")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReviewRepository")
 */
class Review
{
    // Importe la propriété 'createdAt' du trait 'CreatedAtTrait'
    use CreatedAtTrait;

    // Variable constante pour les notes (mark)
    const MARK = [5 => 5, 4 => 4, 3 => 3, 2 => 2, 1 => 1];

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
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var int
     *
     * @ORM\Column(name="mark", type="integer")
     */
    private $mark;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="reviews")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Assos
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Assos", inversedBy="reviews")
     * @ORM\JoinColumn(name="assos_id", referencedColumnName="id", nullable=true)
     */
    private $assos;


    public function __construct()
    {
        // On défini la date à l'intant T ou l'on crée notre notre nouvelle Review
        $this->createdAt = new DateTime();
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Review
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set mark
     *
     * @param integer $mark
     *
     * @return Review
     */
    public function setMark($mark)
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * Get mark
     *
     * @return int
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Review
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set assos
     *
     * @param \AppBundle\Entity\Assos $assos
     *
     * @return Review
     */
    public function setAssos(\AppBundle\Entity\Assos $assos = null)
    {
        $this->assos = $assos;

        return $this;
    }

    /**
     * Get assos
     *
     * @return \AppBundle\Entity\Assos
     */
    public function getAssos()
    {
        return $this->assos;
    }
}
