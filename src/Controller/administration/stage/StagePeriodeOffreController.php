<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Controller/administration/stage/StagePeriodeOffreController.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 08:33

namespace App\Controller\administration\stage;

use App\Controller\BaseController;
use App\Entity\Constantes;
use App\Entity\StagePeriode;
use App\Entity\StagePeriodeOffre;
use App\Form\StagePeriodeOffreType;
use App\Classes\MyExport;
use PhpOffice\PhpSpreadsheet\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/administration/stage/offre")
 */
class StagePeriodeOffreController extends BaseController
{
    /**
     * @Route("/{uuid}", name="administration_stage_periode_offre_index", methods="GET")
     * @ParamConverter("stagePeriode", options={"mapping": {"uuid": "uuid"}})
     * @param StagePeriode $stagePeriode
     *
     * @return Response
     */
    public function index(StagePeriode $stagePeriode): Response
    {
        return $this->render(
            'administration/stage/stage_periode_offre/index.html.twig',
            [
                'stage_periode_offres' => $stagePeriode->getStagePeriodeOffres(),
                'stagePeriode'         => $stagePeriode
            ]
        );
    }

    /**
     * @Route("/{stagePeriode}/export.{_format}", name="administration_stage_periode_offre_export", methods="GET",
     *                                            requirements={"_format"="csv|xlsx|pdf"})
     * @param MyExport                    $myExport
     * @param StagePeriode                $stagePeriode
     * @param                             $_format
     *
     * @return Response
     * @throws Exception
     * @ParamConverter("stagePeriode", options={"mapping": {"stagePeriode": "uuid"}})
     */
    public function export(
        MyExport $myExport,
        StagePeriode $stagePeriode,
        $_format
    ): Response {
        $offres = $stagePeriode->getStagePeriodeOffres();
        return $myExport->genereFichierGenerique(
            $_format,
            $offres,
            'offres_stage',
            ['stage_offre_administration'],
            ['libelle', 'entreprise', 'ville']
        );
    }

    /**
     * @Route("/new/{stagePeriode}", name="administration_stage_periode_offre_new", methods="GET|POST")
     * @ParamConverter("stagePeriode", options={"mapping": {"stagePeriode": "uuid"}})
     * @param Request      $request
     * @param StagePeriode $stagePeriode
     *
     * @return Response
     */
    public function create(Request $request, StagePeriode $stagePeriode): Response
    {
        $stagePeriodeOffre = new StagePeriodeOffre($stagePeriode);
        $form = $this->createForm(StagePeriodeOffreType::class, $stagePeriodeOffre, [
            'departement' => $this->dataUserSession->getDepartement(),
            'annee'       => $stagePeriode->getAnneeUniversitaire(),
            'attr'        => [
                'data-provide' => 'validation'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($stagePeriodeOffre);
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'stage_periode_offre.create.success.flash');

            return $this->redirectToRoute('administration_stage_periode_offre_index',
                ['uuid' => $stagePeriode->getUuidString()]);
        }

        return $this->render('administration/stage/stage_periode_offre/new.html.twig', [
            'stage_periode_offre' => $stagePeriodeOffre,
            'form'                => $form->createView(),
            'stagePeriode'        => $stagePeriode
        ]);
    }



    /**
     * @Route("/{id}/edit", name="administration_stage_periode_offre_edit", methods="GET|POST")
     * @param Request           $request
     * @param StagePeriodeOffre $stagePeriodeOffre
     *
     * @return Response
     */
    public function edit(Request $request, StagePeriodeOffre $stagePeriodeOffre): Response
    {
        $form = $this->createForm(StagePeriodeOffreType::class, $stagePeriodeOffre, [
            'departement' => $this->dataUserSession->getDepartement(),
            'attr'        => [
                'data-provide' => 'validation'
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'stage_periode_offre.edit.success.flash');

            return $this->redirectToRoute('administration_stage_periode_offre_edit',
                ['id' => $stagePeriodeOffre->getId()]);
        }

        return $this->render('administration/stage/stage_periode_offre/edit.html.twig', [
            'stage_periode_offre' => $stagePeriodeOffre,
            'form'                => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/duplicate", name="administration_stage_periode_offre_duplicate", methods="GET")
     *
     * @param StagePeriodeOffre $stagePeriodeOffre
     *
     * @return Response
     */
    public function duplicate(StagePeriodeOffre $stagePeriodeOffre): Response
    {
        $newStagePeriodeOffre = clone $stagePeriodeOffre;
        $this->entityManager->persist($newStagePeriodeOffre);
        $this->entityManager->flush();
        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'stage_periode_offre.duplicate.success.flash');

        return $this->redirectToRoute('administration_stage_periode_offre_edit',
            ['id' => $newStagePeriodeOffre->getId()]);
    }

    /**
     * @Route("/{id}/{stagePeriode}", name="administration_stage_periode_offre_show", methods="GET")
     * @ParamConverter("stagePeriode", options={"mapping": {"stagePeriode": "uuid"}})
     * @param StagePeriodeOffre $stagePeriodeOffre
     * @param StagePeriode      $stagePeriode
     *
     * @return Response
     */
    public function show(StagePeriodeOffre $stagePeriodeOffre, StagePeriode $stagePeriode): Response
    {
        return $this->render('administration/stage/stage_periode_offre/show.html.twig', [
            'stage_periode_offre' => $stagePeriodeOffre,
            'stagePeriode'        => $stagePeriode
        ]);
    }

    /**
     * @Route("/{id}", name="administration_stage_periode_offre_delete", methods="DELETE")
     * @param Request           $request
     *
     * @param StagePeriodeOffre $date
     *
     * @return Response
     */
    public function delete(Request $request, StagePeriodeOffre $date): Response
    {
        $id = $date->getId();
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $this->entityManager->remove($date);
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'stage_periode_offre.delete.success.flash');

            return $this->json($id, Response::HTTP_OK);
        }

        $this->addFlashBag(Constantes::FLASHBAG_ERROR, 'stage_periode_offre.delete.error.flash');

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
