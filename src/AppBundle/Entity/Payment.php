<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * payment
 *
 * @ORM\Table(name="payment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 */
class Payment
{
    // On défini la date à l'intant T ou l'on crée notre notre nouvelle Donation
    use CreatedAtTrait;

    // Constantes des status de paiement des transferts aux associations
    const PAY_BASKET = 0;
    const PAY_ERROR = 1;
    const PAY_REFUSED = 2;
    const PAY_CANCEL = 3;
    const PAY_IN_TRANSFER = 4;
    const PAY_PROCESSED = 5;
    // Tableau des constantes
    const PAYEMENT_STATUS = [
        self::PAY_BASKET => 'Panier',
        self::PAY_ERROR => 'Erreur de paiement',
        self::PAY_REFUSED => 'Paiement refusé',
        self::PAY_CANCEL => 'Paiement annulé',
        self::PAY_IN_TRANSFER => 'En attente de transfert',
        self::PAY_PROCESSED => 'Transfert effectué'
    ];

    // Constantes des modes de paiment des transferts aux associations
    const PAY_STRIPE = 0;
    const PAY_PAYPAL = 1;
    const PAY_CARD = 2;
    const PAY_WIRETRANS = 3;
    const PAY_CHECK = 4;
    // Tableau des constantes
    const PAYEMENT_MODE = [
        self::PAY_STRIPE => 'Stripe',
        self::PAY_PAYPAL => 'PayPal',
        self::PAY_CARD => 'Credit Card',
        self::PAY_WIRETRANS => 'Virement',
        self::PAY_CHECK => 'Chèque'
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_status", type="integer")
     */
    private $paymentStatus;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_mode", type="integer")
     */
    private $paymentMode;

    /**
     * @var Assos
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Assos", inversedBy="payments")
     */
    private $association;

    /**
     * @var Donation
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Donation", mappedBy="payment")
     */
    private $donations;


    public function __construct()
    {
        // On défini la date à l'intant T ou l'on crée notre notre nouvelle Payment
        $this->createdAt = new DateTime();
        $this->paymentStatus = self::PAY_PROCESSED;
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
     * Set paymentStatus
     *
     * @param integer $paymentStatus
     *
     * @return Payment
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return integer
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set paymentMode
     *
     * @param integer $paymentMode
     *
     * @return Payment
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * Get paymentMode
     *
     * @return integer
     */
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }

    /**
     * Set association
     *
     * @param \AppBundle\Entity\Assos $association
     *
     * @return Payment
     */
    public function setAssociation(\AppBundle\Entity\Assos $association = null)
    {
        $this->association = $association;

        return $this;
    }

    /**
     * Get association
     *
     * @return \AppBundle\Entity\Assos
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * Add donation
     *
     * @param \AppBundle\Entity\Donation $donation
     *
     * @return Payment
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
     * Set amount
     *
     * @param integer $amount
     *
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
