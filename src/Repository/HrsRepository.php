<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Repository/HrsRepository.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 08:09

namespace App\Repository;

use App\Entity\Departement;
use App\Entity\Hrs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hrs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hrs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hrs[]    findAll()
 * @method Hrs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HrsRepository extends ServiceEntityRepository
{
    /**
     * HrsRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hrs::class);
    }

    /**
     * @param $getUser
     * @param $annee
     *
     * @return mixed
     */
    public function findByEnseignant($getUser, $annee)
    {
        return $this->createQueryBuilder('h')
            ->where('h.personnel = :user')
            ->andWhere('h.annee = :annee')
            ->setParameter('user', $getUser)
            ->setParameter('annee', $annee)
            ->orderBy('h.typeHrs', 'ASC')
            ->orderBy('h.semestre', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Departement $departement
     * @param           $annee
     *
     * @return mixed
     */
    public function findByDepartement(Departement $departement, $annee)
    {
        return $this->createQueryBuilder('h')
            ->where('h.departement = :departement')
            ->andWhere('h.annee = :annee')
            ->setParameter('departement', $departement->getId())
            ->setParameter('annee', $annee)
            ->orderBy('h.typeHrs', 'ASC')
            ->orderBy('h.semestre', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
