<?php
// src/Repository/FilmRepository.php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    public function findAllPaginated($page, $limit)
    {
        $query = $this->createQueryBuilder('f')
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $query->getResult();
    }

    public function findByTitleOrDescription($title, $description)
    {
        $query = $this->createQueryBuilder('f')
            ->where('f.name LIKE :title OR f.description LIKE :description')
            ->setParameter('title', '%'. $title. '%')
            ->setParameter('description', '%'. $description. '%')
            ->getQuery();

        return $query->getResult();
    }
}