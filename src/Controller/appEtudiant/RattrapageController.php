<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Controller/appEtudiant/RattrapageController.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 20/09/2020 15:12

namespace App\Controller\appEtudiant;

use App\Controller\BaseController;
use App\Entity\Rattrapage;
use App\Form\RattrapageType;
use App\Repository\RattrapageRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/application/etudiant/rattrapage")
 * Class RattrapageController
 * @package App\Controller\appEtudiant
 * @IsGranted("ROLE_ETUDIANT")
 */
class RattrapageController extends BaseController
{
    /**
     * @Route("/", name="application_etudiant_rattrapage_index")
     * @param RattrapageRepository $rattrapageRepository
     * @param Request              $request
     *
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function index(
        RattrapageRepository $rattrapageRepository,
        Request $request
    ) {
        $rattrapage = new Rattrapage($this->getConnectedUser());
        $form = $this->createForm(
            RattrapageType::class,
            $rattrapage,
            [
                'semestre' => $this->getConnectedUser()->getSemestre(),
                'locale'   => $request->getLocale(),
                'attr'     => [
                    'data-provide' => 'validation'
                ],
                'action'   => $this->generateUrl('application_etudiant_rattrapage_index')
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $rattrapage->setAnneeUniversitaire($this->getEtudiantAnneeUniversitaire());
            $this->entityManager->persist($rattrapage);
            $this->entityManager->flush();

            return $this->redirectToRoute('application_index', ['onglet' => 'rattrapage']);
        }

        return $this->render('appEtudiant/rattrapage/index.html.twig', [
            'form'        => $form->createView(),
            'rattrapages' => $rattrapageRepository->findByEtudiant($this->getConnectedUser())
        ]);
    }
}
