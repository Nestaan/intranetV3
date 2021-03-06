<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Controller/api/GroupesApiController.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 08:09

namespace App\Controller\api;

use App\Controller\BaseController;
use App\Entity\Groupe;
use App\Entity\Semestre;
use App\Entity\TypeGroupe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MatiereApiController
 * @package App\Controller
 * @Route("/api/groupe")
 */
class GroupesApiController extends BaseController
{

    /**
     * @param Semestre $semestre
     * @Route("/type-groupe/{semestre}", name="api_type_groupe_semestre", options={"expose":true})
     *
     * @return Response
     */
    public function typeGroupeSemestreAjax(Semestre $semestre): Response
    {
        $json = [];
        /** @var TypeGroupe $typeGroupe */
        foreach ($semestre->getTypeGroupes() as $typeGroupe) {
            $json[] = ['libelle' => $typeGroupe->getLibelle(), 'id' => $typeGroupe->getId()];
        }

        return $this->json($json);
    }

    /**
     * @Route("/groupe/{typeGroupe}", name="api_groupe_type_groupe", options={"expose":true})
     * @param TypeGroupe $typeGroupe
     *
     * @return Response
     */
    public function grouepTypeGroupeAjax(TypeGroupe $typeGroupe): Response
    {
        $json = [];
        /** @var Groupe $groupe */
        foreach ($typeGroupe->getGroupes() as $groupe) {
            $json[] = ['libelle' => $groupe->getLibelle(), 'id' => $groupe->getId()];
        }

        return $this->json($json);
    }
}
