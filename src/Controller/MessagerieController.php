<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/Controller/MessagerieController.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 06/10/2020 16:01

namespace App\Controller;

use App\Classes\MyUpload;
use App\Entity\Etudiant;
use App\Entity\Message;
use App\Entity\Personnel;
use App\Classes\MyMessagerie;
use App\Repository\MessageDestinataireEtudiantRepository;
use App\Repository\MessageDestinatairePersonnelRepository;
use App\Repository\MessageRepository;
use App\Repository\TypeGroupeRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MessagerieController
 * @package App\Controller
 * @Route("/messagerie")
 *
 */
class MessagerieController extends BaseController
{
    /**
     * @Route("/{param}", name="messagerie_index", requirements={"param"="\d+"})
     * @param string $param
     *
     * @return Response
     */
    public function index($param = ''): Response
    {
        return $this->render('messagerie/index.html.twig', [
            'filtre' => 'all',
            'param'  => $param,
        ]);
    }

    /**
     * @Route("/ecrire/{message}", name="messagerie_nouveau", options={"expose":true})
     * @param TypeGroupeRepository $typeGroupeRepository
     *
     * @param Message|null         $message
     *
     * @return Response
     */
    public function nouveauMessage(TypeGroupeRepository $typeGroupeRepository, ?Message $message = null): Response
    {
        return $this->render('messagerie/nouveauMessage.html.twig', [
            'type_groupes' => $typeGroupeRepository->findByDepartement($this->dataUserSession->getDepartement()),
            'message'      => $message
        ]);
    }

    /**
     * @Route("/change-etat", name="messagerie_change_etat", options={"expose":true})
     * @param MessageDestinatairePersonnelRepository $messagePersonnelRepository
     * @param MessageDestinataireEtudiantRepository  $messageEtudiantRepository
     * @param MessageRepository                      $messageRepository
     * @param Request                                $request
     *
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function changeEtat(
        MessageDestinatairePersonnelRepository $messagePersonnelRepository,
        MessageDestinataireEtudiantRepository $messageEtudiantRepository,
        MessageRepository $messageRepository,
        Request $request
    ): JsonResponse {
        $message = $messageRepository->find($request->request->get('message'));
        $etat = $request->request->get('etat');

        if ($message !== null) {
            if ($this->getConnectedUser() instanceof Etudiant) {
                $messaged = $messageEtudiantRepository->findDest($this->getConnectedUser(), $message);
            } elseif ($this->getConnectedUser() instanceof Personnel) {
                $messaged = $messagePersonnelRepository->findDest($this->getConnectedUser(), $message);
            } else {
                return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($messaged !== null) {
                $messaged->setEtat($etat);
                $this->entityManager->persist($messaged);
                $this->entityManager->flush();

                return $this->json(true, Response::HTTP_OK);
            }
        }

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param MessageRepository                      $messageRepository
     * @param MessageDestinatairePersonnelRepository $messagePersonnelRepository
     * @param MessageDestinataireEtudiantRepository  $messageEtudiantRepository
     * @param                                        $filtre
     *
     * @return JsonResponse
     * @Route("/filtre/{filtre}", name="messagerie_filtre", options={"expose"=true})
     */
    public function filtre(
        MessageRepository $messageRepository,
        MessageDestinatairePersonnelRepository $messagePersonnelRepository,
        MessageDestinataireEtudiantRepository $messageEtudiantRepository,
        $filtre
    ): Response {

        if ($filtre === 'sent') {
            $messages = $messageRepository->findBy(['expediteur' => $this->getConnectedUser()], ['updated' => 'DESC']);
        } elseif ($filtre === 'draft') {
            $messages = $messageRepository->findBy(['expediteur' => $this->getConnectedUser(), 'etat' => 'D'],
                ['updated' => 'DESC']);
        } else if ($this->getConnectedUser() instanceof Etudiant) {
            $messages = $messageEtudiantRepository->findLast($this->getConnectedUser(), 0, $filtre);
        } elseif ($this->getConnectedUser() instanceof Personnel) {
            $messages = $messagePersonnelRepository->findLast($this->getConnectedUser(), 0, $filtre);
        } else {
            $messages = null;
        }

        return $this->render('messagerie/listeMessages.html.twig', [
            'filtre'     => $filtre,
            'messages'   => $messages,
            'pagination' => ['depart' => 1, 'fin' => count($messages)]
        ]);
    }

    /**
     * @Route("/liste-messages/{filtre}/{page}", name="messagerie_liste_messages", options={"expose":true})
     * @param MessageRepository                      $messageRepository
     * @param MessageDestinatairePersonnelRepository $messagePersonnelRepository
     * @param MessageDestinataireEtudiantRepository  $messageEtudiantRepository
     * @param string                                 $filtre
     * @param int                                    $page
     *
     * @return Response
     */
    public function listeMessages(
        MessageRepository $messageRepository,
        MessageDestinatairePersonnelRepository $messagePersonnelRepository,
        MessageDestinataireEtudiantRepository $messageEtudiantRepository,
        $filtre = 'all',
        $page = 0
    ): Response {
        if ($filtre === 'send') {
            $messages = $messageRepository->findBy(['expediteur' => $this->getConnectedUser(), 'etat' => 'E']);
        } elseif ($filtre === 'draft') {
            $messages = $messageRepository->findBy(['expediteur' => $this->getConnectedUser(), 'etat' => 'D']);
        } else if ($this->getConnectedUser() instanceof Etudiant) {
            $messages = $messageEtudiantRepository->findLast($this->getConnectedUser(), 0, $filtre, $page);
        } elseif ($this->getConnectedUser() instanceof Personnel) {
            $messages = $messagePersonnelRepository->findLast($this->getConnectedUser(), 0, $filtre, $page);
        } else {
            $messages = null;
        }

        //feature: gérer la pagination?
        return $this->render('messagerie/listeMessages.html.twig', [
            'filtre'     => $filtre,
            'messages'   => $messages,
            'pagination' => ['depart' => 1, 'fin' => count($messages)]
        ]);
    }

    /**
     * @Route("/envoyer", name="messagerie_sent", methods={"POST"}, options={"expose":true})
     * @param MyUpload     $myUpload
     * @param Request      $request
     * @param MyMessagerie $messagerie
     *
     * @return JsonResponse
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function sendMessage(MyUpload $myUpload, Request $request, MyMessagerie $messagerie): JsonResponse
    {
        $typeDestinataire = $request->request->get('messageDestinataireType');
        $destinataires = $this->getDestinataires($typeDestinataire, $request);

        $sujet = $request->request->get('messageSubject');
        $copie = $request->request->get('copie');
        $message = $request->request->get('message');

        foreach ($request->files as $file) {
            if ($file !== null) {
                $fichier = $myUpload->upload($file, 'pj/');
                chmod($fichier, 775);
                $messagerie->addPj($fichier);
            }
        }

        $messagerie->setMessage($sujet, $message, $this->getConnectedUser());
        $messagerie->sendToDestinataires($this->checkArray($destinataires), $typeDestinataire, $this->getDepartement());


        if ($copie !== null) {
            $messagerie->setCopie($copie, $this->getDepartement());
        }

        $messagerie->sendSynthese();

        return $this->json(['message' => $messagerie->getId()]);
    }

    /**
     * @Route("/sauvegarder", name="messagerie_draft", methods={"POST"}, options={"expose":true})
     * @param Request      $request
     * @param MyMessagerie $messagerie
     *
     * @return JsonResponse
     */
    public function messageSave(Request $request, MyMessagerie $messagerie): JsonResponse
    {
        $typeDestinataire = $request->request->get('typeDestinataire');
        $destinataires = $this->getDestinataires($typeDestinataire, $request);


        $sujet = $request->request->get('sujet');
        $copie = $request->request->get('copie');
        $message = $request->request->get('message');

        $messagerie->saveDraft($sujet, $message, $this->getConnectedUser(), $copie, $destinataires, $typeDestinataire);

        return $this->json('ok');
    }

    private function getDestinataires($typeDestinataire, Request $request)
    {
        switch ($typeDestinataire) {
            case 's':
                return $request->request->get('messageToSemestre');
            case 'g':
                return $request->request->get('messageToGroupe');
            case 'e':
                return $request->request->get('messageToLibreEtudiant');
            case 'p':
                return $request->request->get('messageToLibrePersonnel');

        }

        return null;
    }

    /**
     * @Route("/message-envoye/{message}", name="messagerie_message_envoye", options={"expose":true})
     * @param Message $message
     *
     * @return Response
     */
    public function messageSent(
        Message $message
    ): Response {
        return $this->render('messagerie/message_envoye.html.twig', [
            'message' => $message
        ]);
    }


    /**
     * @Route("/{message}", name="messagerie_message", options={"expose":true})
     * @param MessageDestinatairePersonnelRepository $messagePersonnelRepository
     * @param MessageDestinataireEtudiantRepository  $messageEtudiantRepository
     * @param Message                                $message
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    public function message(
        MessageDestinatairePersonnelRepository $messagePersonnelRepository,
        MessageDestinataireEtudiantRepository $messageEtudiantRepository,
        Message $message
    ): Response {
        if ($this->getConnectedUser() instanceof Etudiant) {
            $messaged = $messageEtudiantRepository->findDest($this->getConnectedUser(), $message);
        } elseif ($this->getConnectedUser() instanceof Personnel) {
            $messaged = $messagePersonnelRepository->findDest($this->getConnectedUser(), $message);
        } else {
            return $this->redirectToRoute('erreur_666');
        }

        if ($messaged->getEtat() === 'U') {
            $messaged->setEtat('R');
            $messaged->setDateLu(new DateTime('now'));
            $this->entityManager->persist($messaged);
            $this->entityManager->flush();
        }

        return $this->render('messagerie/message.html.twig', [
            'message' => $message
        ]);
    }

    private function checkArray($get)
    {
        if (!is_array($get)) {
            return [$get];
        }

        return $get;
    }


}
