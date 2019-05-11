<?php

namespace AppBundle\Entity;

use Beelab\PaypalBundle\Entity\Transaction as BaseTransaction;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Transaction extends BaseTransaction
{
    /**
     * Entity Transaction
     * Utililisé par le 'BeelabPaypalBundle' et le 'PaymentController' pour les transaction PayPal
     *
     * doc: https://github.com/Bee-Lab/BeelabPaypalBundle/blob/master/Resources/doc/index.md
     */

    // On initialise un tableau vide pour la méthode 'setDonation'
    private $donations  = [];

    // La méthode 'setDonations' permet de récupérer les attribus
    // 'name' et 'price' des donations sous forme de tableau
    // afin de les injecter à la transaction PayPal comme le nécessite le Bundle
    public function setDonations($donations)
    {
        foreach($donations as $donation)
        {
            $this->donations[] = [
                'name' => $donation->getAssos()->getName(),
                'price' => $donation->getAmount(),
                'quantity' => 1
            ];
        }
    }

    public function getDescription(): ?string
    {
        // Description du paiement
        return 'Vos dons aux associations';
    }

    public function getItems(): array
    {
        // Articles de la transaction
        return $this->donations;
    }

    public function getShippingAmount(): string
    {
        // Frais de port (=null)
        return '0.00';
    }
}