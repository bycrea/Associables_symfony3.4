<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Donation;

/**
 * DonationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DonationRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $id_asso
     * @param null $id_user
     * @param null $id_cookie
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * Vérifie si un dons existe déjà dans le panier(payment_status = 0)
     * vis à vis d'une association et d'un utilisateur (id_user ou id_cookie)
     */
    public function existingBasketDonation($id_asso, $id_user = null, $id_cookie = null)
    {
        // Crée un nouveau $queryBuilder pour préparer notre requête DQL en lien avec l'entity Donation
        $queryBuilder = $this->createQueryBuilder('donation');

        $queryBuilder
            // On join l'entity Assos à notre requête
            ->leftJoin('donation.assos', 'asso')
            // là ou l'id de assos correspond à notre paramètre
            ->where('asso.id = :id_asso')
            ->setParameter('id_asso', $id_asso)
            // et OU le status de paiement est égal à la constante PAY_BASKET(=0)
            ->andWhere('donation.paymentStatus = :status')
            ->setParameter('status', Donation::PAY_BASKET);

        if(!is_null($id_user))
        {
            // et OU l'id_user correspond si l'utilisateur est conecté
            $queryBuilder
                ->leftJoin('donation.user', 'user')
                ->andWhere('user.id = :id_user')
                ->setParameter('id_user', $id_user);
        } else {
            // et OU l'id_cookie correspond dans le cas contraire
            $queryBuilder
                ->andWhere('donation.cookieId = :id_cookie')
                ->setParameter('id_cookie', $id_cookie);
        }

        // Retourne un objet OU null
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param null $id_user
     * @param null $id_cookie
     * @return array
     *
     * Compte le nombre de dons associés à un utilisateur (id_user ou id_cookie)
     * ET
     * La somme total de tous ces dons.
     */
    public function getBasketTotal($id_user = null, $id_cookie = null)
    {
        // Crée un nouveau $queryBuilder pour préparer notre requête DQL en lien avec l'entity Donation
        $queryBuilder = $this->createQueryBuilder('donation');

        $queryBuilder
            // On utilise la fonction SQL count() pour retourner le nombre d'id donation
            ->select('count(donation.id) as quantity, SUM(donation.amount) as amount')
            // là OU le status de paiement est égal à la constante PAY_BASKET(=0)
            ->where('donation.paymentStatus = :status')
            ->setParameter('status', Donation::PAY_BASKET);

        if(!is_null($id_user))
        {
            // et OU l'id_user correspond si l'utilisateur est conecté
            $queryBuilder
                ->leftJoin('donation.user', 'user')
                ->andWhere('user.id = :id_user')
                ->setParameter('id_user', $id_user);
        } else {
            // et OU l'id_cookie correspond dans le cas contraire
            $queryBuilder
                ->andWhere('donation.cookieId = :id_cookie')
                ->setParameter('id_cookie', $id_cookie);
        }

        //  Retourne un tableau des résultats
        $resultArray = $queryBuilder->getQuery()->getScalarResult();
        return $resultArray[0];
    }
}
