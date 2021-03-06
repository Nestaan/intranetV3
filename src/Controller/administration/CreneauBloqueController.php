<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Controller/administration/CreneauBloqueController.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 08:09

namespace App\Controller\administration;

use App\Controller\BaseController;
use App\Entity\CreneauBloque;
use App\Entity\CreneauCours;
use App\Repository\CalendrierRepository;
use App\Repository\CreneauBloqueRepository;
use App\Repository\CreneauCoursRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administration/creneau/bloque")
 */
class CreneauBloqueController extends BaseController
{
    /**
     * @Route("/", name="administration_creneau_bloque_index", methods={"GET"})
     * @param CreneauBloqueRepository $creneauBloqueRepository
     * @param CreneauCoursRepository  $creneauCoursRepository
     * @param CalendrierRepository    $calendrierRepository
     *
     * @return Response
     */
    public function index(
        CreneauBloqueRepository $creneauBloqueRepository,
        CreneauCoursRepository $creneauCoursRepository,
        CalendrierRepository $calendrierRepository
    ): Response {
        if ($this->dataUserSession->getDepartement() !== null && $this->dataUserSession->getDepartement()->getAnneeUniversitairePrepare() !== null) {
            $creneaux = $creneauCoursRepository->findByAnneeDepartement($this->dataUserSession->getDepartement(),
                $this->dataUserSession->getDepartement()->getAnneeUniversitairePrepare());
            $tCreneaux = [];
            /** @var CreneauCours $creneau */
            foreach ($creneaux as $creneau) {
                if (!array_key_exists($creneau->getJourLong(), $tCreneaux)) {
                    $tCreneaux[$creneau->getJourLong()] = [];
                }
                $tCreneaux[$creneau->getJourLong()][] = $creneau;
            }

            return $this->render('administration/creneau_bloque/index.html.twig', [
                'creneau_bloques' => $creneauBloqueRepository->findAll(),
                'creneaux'        => $tCreneaux,
                'semaines'        => $calendrierRepository->findByAnneeUniversitaire($this->dataUserSession->getDepartement()->getAnneeUniversitairePrepare())
            ]);
        }

        return $this->render('bundles/TwigBundle/Exception/error666.html.twig');
    }

    /**
     * @Route("/modifie_etat", name="administration_creneau_bloque_modifie_etat", methods={"POST"},
     *                         options={"expose"=true})
     * @param CreneauCoursRepository  $creneauCoursRepository
     * @param CalendrierRepository    $calendrierRepository
     * @param CreneauBloqueRepository $creneauBloqueRepository
     * @param Request                 $request
     *
     * @return JsonResponse
     */
    public function modifieEtatCreneau(
        CreneauCoursRepository $creneauCoursRepository,
        CalendrierRepository $calendrierRepository,
        CreneauBloqueRepository $creneauBloqueRepository,
        Request $request
    ): Response {
        $cr = $creneauCoursRepository->find($request->request->get('creneau'));
        $semaine = $calendrierRepository->find($request->request->get('semaine'));

        if ($cr && $semaine) {
            $type = $request->request->get('type');
            $crBl = $creneauBloqueRepository->findBy(['creneau' => $cr->getId(), 'semaine' => $semaine->getId()]);

            if (count($crBl) === 1) {
                if ($type === 'dispo') {
                    $this->entityManager->remove($crBl[0]);
                } else {
                    $crBl[0]->setType($type);
                    $this->entityManager->persist($crBl[0]);
                }
                $this->entityManager->flush();

                return $this->json(true, Response::HTTP_OK);
            }

            if (count($crBl) === 0) {
                $crBl = new CreneauBloque();
                $crBl->setType($type);
                $crBl->setCreneau($cr);
                $crBl->setSemaine($semaine);
                $this->entityManager->persist($crBl);
                $this->entityManager->flush();

                return $this->json(true, Response::HTTP_OK);
            }

            return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);

    }
}
