<?php

namespace App\Repository;

use App\Entity\Reader;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reader>
 *
 * @method Reader|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reader|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reader[]    findAll()
 * @method Reader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReaderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reader::class);
    }

    public function findReadersWithBookCount()
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'COUNT(b.id) as bookCount')
            ->leftJoin('r.borrowedBooks', 'b')
            ->groupBy('r.id')
            ->getQuery()
            ->getResult();
    }

    public function findReadersByBook($bookId)
    {
        return $this->createQueryBuilder('r')
            ->join('r.borrowedBooks', 'b')
            ->where('b.id = :bookId')
            ->setParameter('bookId', $bookId)
            ->getQuery()
            ->getResult();
    }
}