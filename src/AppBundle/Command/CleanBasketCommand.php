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
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:clean:basket';

    /**
     * @var RegistryInterface
     */
    private $entityManager;

    public function __construct(RegistryInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Clean donation from basket')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to clean the basket from all donations')

            ->addArgument('day', InputArgument::OPTIONAL, 'nombre de jours','10')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $day = $input->getArgument('day');
        $donations = $this->entityManager->getRepository(Donation::class)
            ->getExpiredDonations($day);

        foreach ($donations as $donation)
        {
            $this->entityManager->getManager()->remove($donation);
        }

        $this->entityManager->getManager()->flush();
    }
}
