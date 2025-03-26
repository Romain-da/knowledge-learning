<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Recherche des utilisateurs par email ou nom.
     *
     * @param string $search Le terme de recherche.
     * @return User[] Retourne un tableau d'utilisateurs correspondants.
     */
    public function searchByEmailOrName(string $search): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.email LIKE :search')
            ->orWhere('u.nom LIKE :search')
            ->orWhere('u.prenom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les utilisateurs avec pagination.
     *
     * @param int $page Numéro de la page.
     * @param int $limit Nombre d'éléments par page.
     * @return array Retourne les utilisateurs paginés.
     */
    public function getPaginatedUsers(int $page, int $limit): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.nom', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre total d'utilisateurs.
     *
     * @return int Le nombre total d'utilisateurs.
     */
    public function countUsers(): int
    {
        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
