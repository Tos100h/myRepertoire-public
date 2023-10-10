<?php

namespace App\Repository;

use App\Entity\Song;
use App\Entity\Artist;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Song>
 *
 * @method Song|null find($id, $lockMode = null, $lockVersion = null)
 * @method Song|null findOneBy(array $criteria, array $orderBy = null)
 * @method Song[]    findAll()
 * @method Song[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    public function add(Song $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Song $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /*
    * @return Song[] Returns an array of Song objects
    */
    public function navbarSearch($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.name LIKE :val OR s.ost LIKE :val OR s.originalName LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    * @return Song[] Returns an array of Song objects
    */
    public function getSongsByArtist(int $id): array
    {
        return $this->createQueryBuilder('s')
            ->addSelect('a')
            ->join('s.artists', 'a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    * @return Song[] Returns an array of Song objects
    */
    public function getSongsByTag(int $id): array
    {
        return $this->createQueryBuilder('s')
            ->addSelect('t')
            ->join('s.tags', 't')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    * @return Song[] Returns an array of Song objects
    */
    public function getSongsByPlaylist(int $id): array
    {
        return $this->createQueryBuilder('s')
            ->addSelect('p')
            ->join('s.playlists', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Song[] Returns an array of Song objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Song
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
