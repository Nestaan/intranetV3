<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Classes/MyStageEtudiant.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 12/10/2020 15:38

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 06/08/2018
 * Time: 15:49
 */

namespace App\Classes;

use App\Entity\Etudiant;
use App\Entity\StageEtudiant;
use App\Entity\StagePeriode;
use App\Event\StageEvent;
use App\Repository\StageEtudiantRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MyStageEtudiant
{
    protected EntityManagerInterface $entityManager;

    protected EventDispatcherInterface $eventDispatcher;

    protected StageEtudiantRepository $stageEtudiantRepository;

    protected StageEtudiant $stageEtudiant;

    private Configuration $configuration;

    /**
     * MyStageEtudiant constructor.
     *
     * @param Configuration            $configuration
     * @param EntityManagerInterface   $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param StageEtudiantRepository  $stageEtudiantRepository
     */
    public function __construct(
        Configuration $configuration,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        StageEtudiantRepository $stageEtudiantRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->configuration = $configuration;
        $this->stageEtudiantRepository = $stageEtudiantRepository;
    }

    /**
     * @param StagePeriode $stagePeriode
     * @param Etudiant     $etudiant
     * @param              $etat
     *
     * @throws NonUniqueResultException
     */
    public function changeEtat(StagePeriode $stagePeriode, Etudiant $etudiant, $etat): void
    {
        $this->stageEtudiant = $this->checkStageEtudiantExist($stagePeriode, $etudiant);

        $eventNotif = '';

        switch ($etat) {
            case StageEtudiant::ETAT_STAGE_AUTORISE:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_AUTORISE);
                $this->stageEtudiant->setDateAutorise(new Carbon('now'));
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_AUTORISE;
                break;
            case StageEtudiant::ETAT_STAGE_REFUS:
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_REFUS;
                $this->entityManager->remove($this->stageEtudiant);
                break;
            case StageEtudiant::ETAT_STAGE_INCOMPLET:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_AUTORISE);
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_INCOMPLET;
                break;
            case StageEtudiant::ETAT_STAGE_DEPOSE:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_DEPOSE);
                $this->stageEtudiant->setDateDepotFormulaire(new Carbon('now'));
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_DEPOSE;
                break;
            case StageEtudiant::ETAT_STAGE_VALIDE:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_VALIDE);
                $this->stageEtudiant->setDateValidation(new Carbon('now'));
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_VALIDE;
                break;
            case StageEtudiant::ETAT_STAGE_CONVENTION_IMPRIME:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_CONVENTION_IMPRIME);
                $this->stageEtudiant->setDateImprime(new Carbon('now'));
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_CONVENTION_IMPRIME;
                break;
            case StageEtudiant::ETAT_STAGE_CONVENTION_ENVOYEE:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_CONVENTION_ENVOYEE);
                $this->stageEtudiant->setDateConventionEnvoyee(new Carbon('now'));
                $eventNotif = StageEvent::CHGT_ETAT_STAGE_CONVENTION_ENVOYEE;
                break;
            case StageEtudiant::ETAT_STAGE_CONVENTION_RECUE:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_CONVENTION_RECUE);
                $this->stageEtudiant->setDateConventionRecu(new Carbon('now'));
                $eventNotif = StageEvent::CHGT_ETAT_CONVENTION_RECUE;
                break;
            case StageEtudiant::ETAT_STAGE_ERASMUS:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_ERASMUS);
                break;
            case StageEtudiant::ETAT_STAGE_ETRANGER:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_ETRANGER);
                break;
            case StageEtudiant::ETAT_STAGE_APPRENTISSAGE:
                $this->stageEtudiant->setEtatStage(StageEtudiant::ETAT_STAGE_APPRENTISSAGE);
                break;
        }

        $this->entityManager->persist($this->stageEtudiant);
        $this->entityManager->flush();

        $event = new StageEvent($this->stageEtudiant);

        if ($eventNotif !== '') {
            $this->eventDispatcher->dispatch($event, $eventNotif);
        }

    }

    /**
     * @param StagePeriode $stagePeriode
     * @param Etudiant     $etudiant
     *
     * @return StageEtudiant|mixed
     * @throws NonUniqueResultException
     * @throws Exception
     */
    private function checkStageEtudiantExist(StagePeriode $stagePeriode, Etudiant $etudiant)
    {
        $result = $this->stageEtudiantRepository->findExist($stagePeriode, $etudiant);

        return $result ?? $this->createStageEtudiant($stagePeriode, $etudiant);
    }

    /**
     * @param StagePeriode $stagePeriode
     * @param Etudiant     $etudiant
     *
     * @return StageEtudiant
     * @throws Exception
     */
    private function createStageEtudiant(StagePeriode $stagePeriode, Etudiant $etudiant): StageEtudiant
    {
        $stageEtudiant = new StageEtudiant($this->configuration->get('GRATIFICATION_HEURE_STAGE'));
        $stageEtudiant->setEtudiant($etudiant);
        $stageEtudiant->setStagePeriode($stagePeriode);
        $stageEtudiant->setDateDebutStage($stagePeriode->getDateDebut());
        $stageEtudiant->setDateFinStage($stagePeriode->getDateFin());
        $stageEtudiant->setDureeJoursStage($stagePeriode->getNbJours());

        $this->entityManager->persist($stageEtudiant);
        $this->entityManager->flush();

        return $stageEtudiant;
    }

    public function update(StageEtudiant $stageEtudiant, $name, $value, $type = 'text'): bool
    {
        if ($type === 'date') {
            $value = Tools::convertDateToObject($value);
        }

        if ($stageEtudiant) {

            $method = 'set' . $name;
            if (method_exists($stageEtudiant, $method)) {
                $stageEtudiant->$method($value);
                $this->entityManager->flush();

                return true;
            }
        }

        return false;
    }
}
