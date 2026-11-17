<?php

namespace App\Repository;

use App\Entity\Emprunt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Livre;

/**
 * @extends ServiceEntityRepository<Emprunt>
 */
class EmpruntRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emprunt::class);
    }

        /** Les derniers emprunts d'un livre, du plus récent au plus ancien */
    public function findDerniersPourLivre(Livre $livre, int $nombre = 5): array
    {
        return $this->createQueryBuilder('e') // ici e est un alias à emprunt
            ->where('e.livre = :livre') // on filtre sur le livre
            ->setParameter('livre', $livre) // on remplace le paramètre :livre par l'objet $livre passé en argument
            ->orderBy('e.dateEmprunt', 'DESC') // on trie par date d'emprunt décroissante
            ->setMaxResults($nombre) // on limite le nombre de résultats
            ->getQuery() // on récupère la requête
            ->getResult(); // on exécute la requête et on récupère les résultats
    }

//    /**
//     * @return Emprunt[] Returns an array of Emprunt objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Emprunt
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
