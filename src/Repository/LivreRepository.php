<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livre>
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

        /** Liste des genres, sans doublon */
    public function findGenres(): array
    {
        $lignes = $this->createQueryBuilder('l')
            ->select('DISTINCT l.genre')
            ->orderBy('l.genre')
            ->getQuery()
            ->getResult();

        return array_column($lignes, 'genre');
    }

    /** Liste des auteurs, sans doublon */
    public function findAuteurs(): array
    {
        $lignes = $this->createQueryBuilder('l')
            ->select('DISTINCT l.auteur')
            ->orderBy('l.auteur')
            ->getQuery()
            ->getResult();

        return array_column($lignes, 'auteur');
    }

    /** Catalogue filtré par genre et/ou auteur, trié */
    public function findCatalogue(?string $genre, ?string $auteur, ?string $tri): array
    {
        $qb = $this->createQueryBuilder('l');

        if ($genre) {
            $qb->andWhere('l.genre = :genre')->setParameter('genre', $genre);
        }

        if ($auteur) {
            $qb->andWhere('l.auteur = :auteur')->setParameter('auteur', $auteur);
        }

        match ($tri) {
            'annee_desc' => $qb->orderBy('l.annee', 'DESC'),
            'annee_asc'  => $qb->orderBy('l.annee', 'ASC'),
            default      => $qb->orderBy('l.titre', 'ASC'),
        };

        return $qb->getQuery()->getResult();
    }

    /** Livres du même genre, sauf celui affiché */
    public function findMemeGenre(Livre $livre, int $nombre = 4): array
    {
        return $this->createQueryBuilder('l') // Création d'un QueryBuilder pour l'entité Livre, avec l'alias 'l'
            ->where('l.genre = :genre') // On filtre les livres par genre, en utilisant un paramètre nommé :genre
            ->andWhere('l.id != :id') // On exclut le livre actuel de la liste, en filtrant par son ID
            ->setParameter('genre', $livre->getGenre()) // On remplace le paramètre :genre par la valeur du genre du livre passé en argument
            ->setParameter('id', $livre->getId()) // On remplace le paramètre :id par l'ID du livre passé en argument
            ->orderBy('l.titre')
            ->setMaxResults($nombre) // On determine le nombre maximum de résultats à retourner, par défaut 4
            ->getQuery() // On récupère la requête construite par le QueryBuilder
            ->getResult(); // On exécute la requête et on récupère les résultats sous forme de tableau d'objets Livre
    }

    /** Livres les plus empruntés */
    public function findPlusEmpruntes(int $nombre = 5): array
    {
        return $this->createQueryBuilder('l')
            ->join('l.emprunts', 'e')
            ->addSelect('COUNT(e.id) AS HIDDEN nbEmprunts')
            ->groupBy('l.id')
            ->orderBy('nbEmprunts', 'DESC')
            ->setMaxResults($nombre)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Livre[] Returns an array of Livre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livre
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
