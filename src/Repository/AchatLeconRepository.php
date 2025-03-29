<?php

namespace App\Repository;

use App\Entity\AchatLecon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AchatLecon>
 *
 * @method AchatLecon|null find($id, $lockMode = null, $lockVersion = null)
 * @method AchatLecon|null findOneBy(array $criteria, array $orderBy = null)
 * @method AchatLecon[]    findAll()
 * @method AchatLecon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatLeconRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AchatLecon::class);
    }

}
