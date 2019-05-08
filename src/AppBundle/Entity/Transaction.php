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
    private $donations;

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
        return 'Paiement de vos dons';
    }

    public function getItems(): array
    {
        // here you can return an array of items, with each item being an array of name, quantity, price
        // Note that if the total (price * quantity) of items doesn't match total amount, this won't work
        /*return array(
            ['name' => 'don asso test', 'price' => 100.00, 'quantity' => 1]
        );*/
        return $this->donations;
    }

    public function getShippingAmount(): string
    {
        // here you can return shipping amount. This amount MUST be already in your total amount
        return '0.00';
    }
}