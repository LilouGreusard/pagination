<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    #[Route('/{slug}', name: 'app_main_slug')]
    public function index(string $slug = null, ArticlesRepository $articlesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $dql = $articlesRepository->findArticlesByLimitPagination($slug);

        $pagination = $paginator->paginate(
            $dql,
            $request->query->getInt('page',1),
            48
        );

        return $this->render('main/index.html.twig', [
            'articles' => $pagination,
        ]);
    }
}
