<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Repository/NotificationRepository.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 08/08/2020 08:37

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\Personnel;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{

    private const DAYS_BEFORE_REMOVE = 30;

    /**
     * NotificationRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function deleteOldNotification()
    {
        return $this->getDeleteOldNotification()->delete()->getQuery()->execute();
    }

    /**
     * @return QueryBuilder
     * @throws Exception
     */
    public function getDeleteOldNotification(): QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->where('n.created < :date')
            ->setParameter('date', new DateTime(-self::DAYS_BEFORE_REMOVE . 'days'));
    }

    public function findNonLuByUser($user)
    {
        $query = $this->createQueryBuilder('n')
            ->where('n.lu = false');

        if ($user instanceof Personnel) {
            $query->andWhere('n.personnel = :user');
        } else {
            $query->andWhere('n.etudiant = :user');
        }
        $query->setParameter('user', $user->getId());

        return $query->getQuery()->getResult();

    }

    public function findByUser($user)
    {
        $query = $this->createQueryBuilder('n');

        if ($user instanceof Personnel) {
            $query->andWhere('n.personnel = :user');
        } else {
            $query->andWhere('n.etudiant = :user');
        }
        $query->setParameter('user', $user->getId());

        return $query->getQuery()->getResult();
    }
}
