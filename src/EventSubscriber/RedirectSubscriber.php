<?php
// Copyright (c) 2020. | David Annebicque | IUT de Troyes  - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetV3/src/EventSubscriber/RedirectSubscriber.php
// @author davidannebicque
// @project intranetV3
// @lastUpdate 05/07/2020 08:13

namespace App\EventSubscriber;

use App\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RedirectSubscriber implements EventSubscriberInterface
{
    /** @var RouterInterface */
    private $router;

    /**
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::REDIRECT_TO_LOGIN => 'onRedirectToLogin',
            Events::CHOISIR_DEPARTEMENT_DEFAUT => 'onChoixDepartementDefaut',
        ];
    }

    public function onRedirectToLogin($event): RedirectResponse
    {
        //logout
        return new RedirectResponse($this->router->generate('security_login', ['message' => $event->getSubject()]));
    }

    public function onChoixDepartementDefaut($event): RedirectResponse
    {
        //logout
        return new RedirectResponse($this->router->generate('security_choix_departement', ['message' => $event->getSubject()]));
    }
}
