<?php

namespace App\Controller;


use Stripe\Stripe;
use Stripe\TaxeRate;
use App\Entity\Articles;
use App\Services\CartServices;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    #[Route('/payement', name: 'payement', priority: 10)]
    public function payement(){
        $stripeSk = $_ENV['STRIPE_SK'];
        Stripe::setApiKey($stripeSk);
        
        // $taxeRate = TaxeRate::create([
        //     'display_name'=>'tva',
        //     'inclusive'=>false,
        //     'percentage'=>20.0,
        //     'country'=>'FR',
        // ]);

        $lineItems = [];
        foreach ($this->cartServices->getFullcart() as $product)
        {
            $lineItems[] = [
                'price_data'=>[
                    'currency'=>'eur',
                    'unit_amount'=>$product['article']->getPrice()*100,
                    'product_data'=>[
                        'name'=>$product['article']->getTitle(),
                        'description'=>'description par défaut',
                    ],
                ],
                'quantity'=>$product['quantity'],
            ];
        }

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types'=>['card', 'sepa_debit'],
            'line_items'=>$lineItems,
            'mode'=>'payment',
            'success_url'=>'https://127.0.0.1:8000/cart/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'=>$this->generateUrl('app_cancel_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url,303);
    }

    #[Route('/success', name: 'success_url',priority:10)]
    public function success(Request $request){
        $sessionId = $request->query->get('session_id');
        $stripe = new \Stripe\StripeClient($_ENV['STRIPE_SK']);
        $session = $stripe->checkout->session->retrieve($sessionId);
        $payment = $stripe->paymentIntents->retrieve($session->payment_intent);
        $customer = $stripe->customer->retrieve($payment->customer);

        dd($sessionId, $stripe, $session, $payment, $customer);
    }

    #[Route('/cancel_url', name: 'cancel_url',priority:10)]
    public function cancel(){

    }
}

