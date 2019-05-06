<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Assos
 *
 * @ORM\Table(name="assos")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssosRepository")
 */
class Assos
{
    use CreatedAtTrait;

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="url_assos", type="string", length=255, nullable=true)
     * @Assert\Url(
     *     message="Ce lien n'est pas valide."
     * )
     */
    private $urlAssos;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @Assert\Image(
     *     maxSize = "2M",
     *     maxSizeMessage="La taile de l'image ne peut pas dépasser {{ limit }} {{ suffix }}."
     * )
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_info", type="string", length=255, nullable=true)
     */
    private $contactInfo;

    /**
     * @var Category
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", inversedBy="assos")
     * @ORM\JoinTable(name="assos_category")
     */
    private $categories;

    /**
     * @var Review
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Review", mappedBy="assos")
     */
    private $reviews;

    /**
     * @var Donation
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Donation", mappedBy="assos")
     */
    private $donations;

    /**
     * @var Payment
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Payment", mappedBy="association")
     */
    private $payments;


    public function __construct()
    {
        $this->createdAt = new \DateTime();

        // On instancie un tableau vide pour éviter un erreur PHP lorsque l'on boucle sur une collection
        $this->reviews = new ArrayCollection();
        $this->donations = new ArrayCollection();
        $this->payments = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Assos
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
     * Set description
     *
     * @param string $description
     *
     * @return Assos
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set contactInfo
     *
     * @param string $contactInfo
     *
     * @return Assos
     */
    public function setContactInfo($contactInfo)
    {
        $this->contactInfo = $contactInfo;

        return $this;
    }

    /**
     * Get contactInfo
     *
     * @return string
     */
    public function getContactInfo()
    {
        return $this->contactInfo;
    }

    /**
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Assos
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add review
     *
     * @param \AppBundle\Entity\Review $review
     *
     * @return Assos
     */
    public function addReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review
     *
     * @param \AppBundle\Entity\Review $review
     */
    public function removeReview(\AppBundle\Entity\Review $review)
    {
        $this->reviews->removeElement($review);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Add donation
     *
     * @param \AppBundle\Entity\Donation $donation
     *
     * @return Assos
     */
    public function addDonation(\AppBundle\Entity\Donation $donation)
    {
        $this->donations[] = $donation;

        return $this;
    }

    /**
     * Remove donation
     *
     * @param \AppBundle\Entity\Donation $donation
     */
    public function removeDonation(\AppBundle\Entity\Donation $donation)
    {
        $this->donations->removeElement($donation);
    }

    /**
     * Get donations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDonations()
    {
        return $this->donations;
    }

    /**
     * Add payment
     *
     * @param \AppBundle\Entity\Payment $payment
     *
     * @return Assos
     */
    public function addPayment(\AppBundle\Entity\Payment $payment)
    {
        $this->payments[] = $payment;

        return $this;
    }

    /**
     * Remove payment
     *
     * @param \AppBundle\Entity\Payment $payment
     */
    public function removePayment(\AppBundle\Entity\Payment $payment)
    {
        $this->payments->removeElement($payment);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * Set urlAssos
     *
     * @param string $urlAssos
     *
     * @return Assos
     */
    public function setUrlAssos($urlAssos)
    {
        $this->urlAssos = $urlAssos;

        return $this;
    }

    /**
     * Get urlAssos
     *
     * @return string
     */
    public function getUrlAssos()
    {
        return $this->urlAssos;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Assos
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
}
