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
    // On crée un atribue de Class pour la méthode 'setDonation'
    private $donations;

    // La méthode 'setDonations' permet de récupérer les attribus
    // 'name' et 'price' des donations sous forme de tableau
    // afin de les injecter à la transaction PayPal comme le nécessite le Bundle
    public function setDonations($donations)
    {
        $this->donations = [];

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
        // Détails de la transaction
        return $this->donations;
    }

    public function getShippingAmount(): string
    {
        // Frais de port
        return '0.00';
    }
}