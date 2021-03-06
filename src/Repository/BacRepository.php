<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Repository/BacRepository.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 16/08/2020 09:37

namespace App\Repository;

use App\Entity\Bac;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bac|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bac|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bac::class);
    }

    public function findAll()
    {
        return $this->findBy([], ['libelle' => 'ASC']);
    }

    public function getApogeeArray()
    {
        $data = $this->findAll();
        $t = [];
        foreach ($data as $d) {
            $t[$d->getCodeApogee()] = $d;
        }

        return $t;
    }
}
