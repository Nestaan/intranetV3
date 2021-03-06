<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Classes/Model/ParcoursEtudiant.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 08/08/2020 22:44

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 24/12/2018
 * Time: 16:11
 */

namespace App\Classes\Model;


use App\Entity\Etudiant;
use App\Entity\ScolariteMoyenneUe;
use App\Entity\Semestre;
use App\Repository\ScolariteRepository;

class ParcoursEtudiant
{

    private $parcoursUe = [];

    private $moyenne = 0;

    private $decision = '';

    /** @var ScolariteRepository */
    private $scolariteRepository;


    /**
     * ParcoursEtudiant constructor.
     *
     * @param ScolariteRepository $scolariteRepository
     */
    public function __construct(ScolariteRepository $scolariteRepository)
    {
        $this->scolariteRepository = $scolariteRepository;

    }

    public function calculScolarite(Etudiant $etudiant, Semestre $semestreCourant): ParcoursEtudiant
    {
        $etudiant1 = $etudiant;
        $semestreCourant1 = $semestreCourant;

        foreach ($semestreCourant->getUes() as $ue) {
            $this->parcoursUe[$ue->getNumeroUe()]['style'] = '';
            $this->parcoursUe[$ue->getNumeroUe()]['moyenne'] = 0;
        }
        $scolarite = $this->scolariteRepository->findBy([
            'etudiant' => $etudiant1->getId(),
            'semestre' => $semestreCourant1->getId()
        ], ['ordre' => 'DESC']);

        if (count($scolarite) > 0) {
            $this->moyenne = $scolarite[0]->getMoyenne();
            $this->decision = $scolarite[0]->getDecision();

            /** @var ScolariteMoyenneUe $ue */
            foreach ($scolarite[0]->getScolariteMoyenneUes() as $ue) {
                $this->parcoursUe[$ue->getUe()->getNumeroUe()]['style'] = $ue->getMoyenne() >= 8 ? 'badge badge-success' : 'badge badge-danger';
                $this->parcoursUe[$ue->getUe()->getNumeroUe()]['moyenne'] = $ue->getMoyenne();
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getParcoursUe(): array
    {
        return $this->parcoursUe;
    }

    /**
     * @return int
     */
    public function getMoyenne(): int
    {
        return $this->moyenne;
    }

    /**
     * @return string
     */
    public function getStyleDecision(): string
    {
        switch ($this->decision) {
            case 'V':
                return 'badge badge-success';
            case 'NV':
                return 'badge badge-danger';
            case 'VCA':
            case 'VCJ':
                return 'badge badge-warning';
            default:
                return '';
        }
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->moyenne >= 10 ? 'badge badge-success' :  'badge badge-danger';
    }

    public function getDecision(): string
    {
        return $this->decision;
    }
}
