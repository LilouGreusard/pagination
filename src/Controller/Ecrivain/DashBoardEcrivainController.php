<?php

namespace App\Controller\Ecrivain;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DashBoardEcrivainController extends AbstractController
{
    #[Route('/dash/board/ecrivain', name: 'app_dash_board_ecrivain')]
    #[IsGranted('ROLE_ECRIVAIN', message :  'You must be logged to acces this page.', statusCode: 404, exceptionCode: 404)]
    public function index(): Response
    {
        return $this->render('ecrivain/dash_board_ecrivain/index.html.twig', [
            'controller_name' => 'DashBoardEcrivainController',
        ]);
    }
}
