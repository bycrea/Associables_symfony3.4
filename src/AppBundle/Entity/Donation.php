<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CreatedAtTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Donation
 *
 * @ORM\Table(name="donation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DonationRepository")
 */
class Donation
{
    use CreatedAtTrait;

    // Constantes des status de paiement des dons
    const PAY_BASKET = 0;
    const PAY_ERROR = 1;
    const PAY_REFUSED = 2;
    const PAY_CANCEL = 3;
    // Constantes des status de paiement des dons et de paiement aux assos
    const PAY_IN_TRANSFER = 4;
    const PAY_PROCESSED = 5;
    const PAYEMENT_STATUS = [
        self::PAY_BASKET => 'Panier',
        self::PAY_ERROR => 'Erreur de paiement',
        self::PAY_REFUSED => 'Paiement refusé',
        self::PAY_CANCEL => 'Paiement annulé',
        self::PAY_IN_TRANSFER => 'En attente de transfert',
        self::PAY_PROCESSED => 'Transfert effectué'
    ];

    // Constantes des modes de paiment des dons et des transferts aux associations
    const PAY_STRIPE = 0;
    const PAY_PAYPAL = 1;
    const PAY_CARD = 2;
    const PAY_WIRETRANS = 3;
    const PAY_CHECK = 4;
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
     * @var bool
     *
     * @ORM\Column(name="recurrent", type="boolean")
     */
    private $recurrent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="donations")
     */
    private $user;

    /**
     * @var Assos
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Assos", inversedBy="donations")
     */
    private $assos;

    /**
     * @var Payment
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Payment", inversedBy="donations")
     */
    private $payment;


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->recurrent = false;
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
     * Set amount
     *
     * @param integer $amount
     *
     * @return Donation
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

    /**
     * Set paymentStatus
     *
     * @param integer $paymentStatus
     *
     * @return Donation
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
     * @return Donation
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
     * Set recurrent
     *
     * @param boolean $recurrent
     *
     * @return Donation
     */
    public function setRecurrent($recurrent)
    {
        $this->recurrent = $recurrent;

        return $this;
    }

    /**
     * Get recurrent
     *
     * @return boolean
     */
    public function getRecurrent()
    {
        return $this->recurrent;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Donation
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
     * @return Donation
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

    /**
     * Set payment
     *
     * @param \AppBundle\Entity\Payment $payment
     *
     * @return Donation
     */
    public function setPayment(\AppBundle\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \AppBundle\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }
}
