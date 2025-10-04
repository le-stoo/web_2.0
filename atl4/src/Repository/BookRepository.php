<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findBooksByPriceRange($min, $max)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.price >= :min')
            ->andWhere('b.price <= :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getQuery()
            ->getResult();
    }

    public function findBooksByPublicationYear($year)
    {
        $startDate = new \DateTimeImmutable($year.'-01-01');
        $endDate = new \DateTimeImmutable($year.'-12-31');

        return $this->createQueryBuilder('b')
            ->andWhere('b.publicationDate >= :start')
            ->andWhere('b.publicationDate <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->getQuery()
            ->getResult();
    }

    public function findBooksByAuthorAndCategory($authorId, $category)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.author = :authorId')
            ->andWhere('b.category = :category')
            ->setParameter('authorId', $authorId)
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }
}