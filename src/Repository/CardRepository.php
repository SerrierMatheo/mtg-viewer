<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 *
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    public function getAllUuids(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.uuid')
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return array_column($result, 'uuid');
    }

    public function findAllPaginated(int $page = 1, int $limit = 100): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getTotalCount(): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function searchByName(string $name): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :name')
            ->setParameter('name', $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function getAllSetCode(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('c.setCode')
            ->distinct()
            ->getQuery()
            ->getResult(AbstractQuery::HYDRATE_ARRAY);
        return array_column($result, 'setCode');
    }

    public function findBySetCode(string $setCode, int $page = 1, int $limit = 100): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.setCode = :setCode')
            ->setParameter('setCode', $setCode)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getCountBySetCode(string $setCode): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->where('c.setCode = :setCode')
            ->setParameter('setCode', $setCode)
            ->getQuery()
            ->getSingleScalarResult();
    }
}