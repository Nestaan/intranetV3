<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Repository/StageMailTemplateRepository.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:21
// @lastUpdate 23/11/2019 09:14

namespace App\Repository;

use App\Entity\StageMailTemplate;
use App\Entity\StagePeriode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method StageMailTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method StageMailTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method StageMailTemplate[]    findAll()
 * @method StageMailTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageMailTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StageMailTemplate::class);
    }

    /**
     * @param              $codeEvent
     * @param StagePeriode $stagePeriode
     *
     * @return StageMailTemplate|null
     * @throws NonUniqueResultException
     *
     */
    public function findEventPeriode($codeEvent, StagePeriode $stagePeriode): ?StageMailTemplate
    {
        return $this->createQueryBuilder('s')
            ->where('s.stagePeriode = :stagePeriode')
            ->andWhere('s.event = :code')
            ->setParameter('stagePeriode', $stagePeriode)
            ->setParameter('code', $codeEvent)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
