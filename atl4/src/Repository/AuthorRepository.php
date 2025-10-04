<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findAuthorsByUsername(string $username)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.username LIKE :username')
            ->setParameter('username', '%' . $username . '%')
            ->getQuery()
            ->getResult();
    }

    public function findByNumberBooksRange($min, $max)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nb_books >= :min')
            ->andWhere('a.nb_books <= :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max)
            ->getQuery()
            ->getResult();
    }
}
