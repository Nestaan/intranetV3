<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Classes/MyPagination.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 09:14

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 23/06/2018
 * Time: 08:16
 */

namespace App\Classes;

use App\Entity\Constantes;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Routing\RouterInterface;
use function count;

/**
 * Class MyPagination
 * @package App\Classes
 */
class MyPagination
{
    /** @var RouterInterface */
    private $router;

    private $nbMaxResult;
    private $data;
    private $page;
    private $nbPages;
    private $link;

    /**
     * MyPagination constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * @param QueryBuilder $queryBuilder
     * @param              $link
     * @param int          $page
     */
    public function calculPagination(QueryBuilder $queryBuilder, $link, $page = 1): void
    {
        $this->page = $page;
        $this->link = $link;

        $debut = $page * Constantes::NB_RESULTS_PER_PAGE - 2;

        $this->nbMaxResult = count($queryBuilder->getQuery()->getArrayResult());
        $this->data = $queryBuilder->setFirstResult($debut)
            ->setMaxResults(Constantes::NB_RESULTS_PER_PAGE)
            ->getQuery()
            ->getResult();

        $this->nbPages = ceil($this->nbMaxResult / Constantes::NB_RESULTS_PER_PAGE);
    }

    /**
     * @return mixed
     */
    public function getNbPages()
    {
        return $this->nbPages;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->page;
    }


    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @return mixed
     */
    public function getNbMaxResult()
    {
        return $this->nbMaxResult;
    }

    /**
     * @return bool
     */
    public function hasPreviousLink(): bool
    {
        return $this->page !== 1;
    }

    /**
     * @return string
     */
    public function getPreviousLink(): string
    {
        $args = $this->link['args'];
        $args['page'] = $this->page - 1;

        return $this->router->generate($this->link['path'], $args);
    }

    /**
     * @return bool
     */
    public function hasNextLink(): bool
    {
        return $this->page !== $this->nbPages;
    }

    /**
     * @return string
     */
    public function getNextLink(): string
    {
        $args = $this->link['args'];
        $args['page'] = $this->page + 1;

        return $this->router->generate($this->link['path'], $args);
    }

    /**
     * @param $page
     *
     * @return string
     */
    public function getLinkPage($page): string
    {
        $args = $this->link['args'];
        $args['page'] = $page;


        return $this->router->generate($this->link['path'], $args);
    }
}
