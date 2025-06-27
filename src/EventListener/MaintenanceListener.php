<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener
{
    private $maintenance;
    private $twig;

    public function __construct($maintenance, Environment $twig)
    {
        $this->maintenance = $maintenance;
        $this->twig = $twig;
    }

    public function OnKernelRequest(RequestEvent $event)
    {
        if(!file_exists($this->maintenance)){
            return;
        }
        
        $event->setResponse(
            new Response(
                $this->twig->render('maintenance/maintnance.html.twig'),
                Response::HTTP_SERVICE_UNAVAILABLE
            )
        );

$event->stopPropagation();
    }
}