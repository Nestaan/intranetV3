<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Classes/MyConfiguration.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 04/10/2020 08:41

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 19/05/2018
 * Time: 18:48
 */

namespace App\Classes;

use App\Entity\AnneeUniversitaire;
use App\Entity\Personnel;
use App\Repository\AnneeRepository;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\DepartementRepository;
use App\Repository\DiplomeRepository;
use App\Repository\PersonnelRepository;
use App\Repository\SemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use function strlen;

/**
 * Class MyConfiguration
 * @package App\Classes
 */
class MyConfiguration
{
    private DepartementRepository $departementRepository;

    private DiplomeRepository $diplomeRepository;

    private AnneeRepository $anneeRepository;

    private SemestreRepository $semestreRepository;

    private AnneeUniversitaireRepository $anneeUniversitaireRepository;

    private PersonnelRepository $personnelRepository;

    private EntityManagerInterface $entityManager;

    /**
     * MyConfiguration constructor.
     *
     * @param DepartementRepository        $departementRepository
     * @param DiplomeRepository            $diplomeRepository
     * @param AnneeRepository              $anneeRepository
     * @param SemestreRepository           $semestreRepository
     * @param PersonnelRepository          $personnelRepository
     * @param AnneeUniversitaireRepository $anneeUniversitaireRepository
     * @param EntityManagerInterface       $entityManager
     */
    public function __construct(
        DepartementRepository $departementRepository,
        DiplomeRepository $diplomeRepository,
        AnneeRepository $anneeRepository,
        SemestreRepository $semestreRepository,
        PersonnelRepository $personnelRepository,
        AnneeUniversitaireRepository $anneeUniversitaireRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->departementRepository = $departementRepository;
        $this->diplomeRepository = $diplomeRepository;
        $this->anneeRepository = $anneeRepository;
        $this->semestreRepository = $semestreRepository;
        $this->personnelRepository = $personnelRepository;
        $this->anneeUniversitaireRepository = $anneeUniversitaireRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $type
     * @param $id
     * @param $name
     * @param $value
     *
     * @return boolean
     */
    public function updateOption($type, $id, $name, $value): bool
    {
        switch ($type) {
            case 'departement':
                return $this->updateDepartement($id, $name, $value);
            case 'diplome':
                return $this->updateDiplome($id, $name, $value);
            case 'annee':
                return $this->updateAnnee($id, $name, $value);
            case 'semestre':
                return $this->updateSemestre($id, $name, $value);
        }

        return false;
    }

    /**
     * @param $id
     * @param $name
     * @param $value
     *
     * @return bool
     */
    private function updateDepartement($id, $name, $value): bool
    {
        $departement = $this->departementRepository->find($id);
        if ($departement) {
            $departement->update($name, $this->transformeValue($value));
            $this->entityManager->persist($departement);
            $this->entityManager->flush();
            echo 'update';

            return true;
        }

        return false;
    }

    /**
     * @param $value
     *
     * @return Personnel|bool|null|AnneeUniversitaire
     */
    private function transformeValue($value)
    {
        if ($value === 'false') {
            return false;
        }

        if ($value === 'true') {
            return true;
        }

        if (0 === strpos($value, 'pers')) {
            return $this->personnelRepository->find(substr($value, 4, strlen($value)));
        }

        if (0 === strpos($value, 'anneeuniv')) {
            return $this->anneeUniversitaireRepository->find(substr($value, 9, strlen($value)));
        }

        if (empty($value) && $value !== '0') {
            return null;
        }

        return $value;
    }

    /**
     * @param $id
     * @param $name
     * @param $value
     *
     * @return bool
     */
    private function updateDiplome($id, $name, $value): bool
    {
        $diplome = $this->diplomeRepository->find($id);
        if ($diplome) {
            $diplome->update($name, $this->transformeValue($value));
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param $name
     * @param $value
     *
     * @return bool
     */
    private function updateAnnee($id, $name, $value): bool
    {
        $annee = $this->anneeRepository->find($id);
        if ($annee) {
            $annee->update($name, $this->transformeValue($value));
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * @param $id
     * @param $name
     * @param $value
     *
     * @return bool
     */
    private function updateSemestre($id, $name, $value): bool
    {
        $semestre = $this->semestreRepository->find($id);
        if ($semestre) {
            $semestre->update($name, $this->transformeValue($value));
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
