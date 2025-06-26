<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart', name: 'app_')]
final class CartController extends AbstractController
{
    public function __construct(
        private CartServices $cartServices
    ) {}

    
// ------------------ADD------------------
    #[Route('/{id}', name: 'cart')]
    public function add(Articles $articles): Response 
    //public function add(Articles $articles, SessionInterface $session): Response 
    {
        // $id = $articles->getId();

        // $panier = $session->get('panier', []);

        // if ( empty($panier[$id]) ){
        //     $panier[$id] = 1;
        // } else {
        //     $panier[$id]++;
        // }

        // // $panier[$id] = ( empty($panier[$id]) ) ? 1 : $panier[$id]+1 ;

        // $session->set('panier', $panier);

        $this->cartServices->add($articles);

        return $this->redirectToRoute('app_index');
    }

// ------------------FULLCART / TOTAL------------------
    #[Route('/', name: 'index')]
    public function index(
        // SessionInterface $session,
        // ArticlesRepository $articlesRepository

    ){
        // $panier = $session->get('panier', []);

        // $data = [];
        // $total = 0;

        // foreach( $panier as $key => $quantity) {

        //     $article = $articlesRepository->find($key);

        //     $data[] = [
        //         'article' => $article,
        //         'quantity' => $quantity,
        //     ];

        //     $total += $article->getPrice() * $quantity;

        // return $this->render('cart/cart.html.twig', [
        //     'data' => $data,
        //     'total' => $total,
        // ]);

        return $this->render('cart/cart.html.twig',[
            'data' => $this->cartServices->getFullcart(),
            'total' => $this->cartServices->getTotal(),
        ]);
    }


// ------------------REMOVE------------------
    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Articles $articles): Response
    {
    //public function remove(Articles $articles, SessionInterface $session){
        // $id = $articles->getId();

        // $panier = $session->get('panier', []);

        // if ( !empty($panier[$id]) ){
        //     if($panier[$id] > 1){
        //         $panier[$id]--;
        //     } else {
        //         // mettre un overlay pour être sur que le client souhaite retirer l'article de son panier
        //         unset( $panier[$id] );
        //     }

        //     $session->set('panier', $panier);
        $this->cartServices->remove($articles);

        return $this->redirectToRoute('app_index');
    }
    
// ------------------DELETE------------------
    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Articles $articles, SessionInterface $session){
        // $id = $articles->getId();

        // $panier = $session->get('panier', []);

        // if ( !empty($panier[$id]) ){
        //     // mettre un overlay pour être sur que le client souhaite retirer l'article de son panier
        //     unset( $panier[$id] );
        // }


        //     $session->set('panier', $panier);
            $this->cartServices->delete($articles);

            return $this->redirectToRoute('app_index');
    }

// ------------------EMPTY------------------
    #[Route('/empty', name: 'empty', priority: 10)]
    public function empty(SessionInterface $session){
        $session->remove('panier');
        return $this->redirectToRoute('app_index');
    }
}

