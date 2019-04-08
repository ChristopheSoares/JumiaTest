<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bridge\Doctrine\RegistryInterface;


class CountryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function fetchCountryForSelect(){
        return $this->createQueryBuilder('c')
            ->select('c.id')
            ->addSelect('c.Name AS text')
            ->getQuery()
            ->getArrayResult();
    }

}
