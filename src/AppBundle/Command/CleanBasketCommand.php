<?php

namespace AppBundle\Command;

use AppBundle\Entity\Donation;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanBasketCommand extends Command
{
    /**
     * Symfony Command : php bin/console app:clean:basket [default: 10]
     * Permet de supprimer les donations expirées dans 'les' paniers (paymentStatus = 0)
     * Par défaut les donations de plus de 10 jours
     *
     * Plus tard, cette Command sera utilisé par CRON (ou CRONTAB) pour automatisé la Command
     * doc: https://symfony.com/doc/3.4/console.html
     */

    // Défini le nom de la Command
    protected static $defaultName = 'app:clean:basket';

    /**
     * @var RegistryInterface
     */
    private $entityManager;

    /**
     * Injection de dépendance
     * @param RegistryInterface $entityManager
     */
    public function __construct(RegistryInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    /**
     * Configuration de la Command
     */
    protected function configure()
    {
        $this
            // Description court de la Command pour "php bin/console list"
            ->setDescription('Clean donation from basket')

            // Description complète de la Command "--help" option
            ->setHelp('This command allows you to clean the basket from all donations')

            // Ajout d'arguments à InputIterface ($input)
            ->addArgument('day', InputArgument::OPTIONAL, 'nombre de jours','10')
        ;
    }

    /**
     * Programme à exécuter
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Récupère les arguments ajouté en paramètre de $input
        $day = $input->getArgument('day');

        // Récupère les donations concernées grâce à la méthode 'getExpiredDonations'
        $donations = $this->entityManager->getRepository(Donation::class)
            ->getExpiredDonations($day);

        foreach ($donations as $donation)
        {
            // Supprime chaque donation une par une
            $this->entityManager->getManager()->remove($donation);
        }

        $this->entityManager->getManager()->flush();
    }
}
