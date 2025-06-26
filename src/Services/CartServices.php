<?php

namespace App\Services;

use App\Entity\Articles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartServices
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager
    ){}

    public function getSession(): ?object
    {
        return $this->requestStack->getSession();
    }

// ------------------ADD------------------
    public function add(Articles $articles)
    {
        $id = $articles->getId();

        $panier = $this->getSession()->get('panier', []);

        if ( empty($panier[$id]) ){
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        // $panier[$id] = ( empty($panier[$id]) ) ? 1 : $panier[$id]+1 ;

        $this->getSession()->set('panier', $panier);
    }

// ------------------FULLCART------------------
    public function getFullcart()
    {
        $panier = $this->getSession()->get('panier', []);

        $data = [];
        foreach( $panier as $key => $quantity) {

            $article = $this->entityManager->getRepository(Articles::class)->find($key);

            $data[] = [
                'article' => $article,
                'quantity' => $quantity,
            ];
        }
        return $data;
    }

    
// ------------------TOTAL------------------
    public function getTotal(){
        $total = 0;

        foreach( $this->getFullcart() as $item ) {
            $totalItem = $item['article']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $total;
    }

// ------------------REMOVE------------------
    public function remove(Articles $articles)
    {
        $id = $articles->getId();

        $panier = $this->getSession()->get('panier', []);

        if ( !empty($panier[$id]) ){
            if($panier[$id] > 1){
                $panier[$id]--;
            } else {
                // mettre un overlay pour être sur que le client souhaite retirer l'article de son panier
                unset( $panier[$id] );
            }
        }   

        $this->getSession()->set('panier', $panier);
    }


// ------------------DELETE------------------
    public function delete(Articles $articles)
    {
        $id = $articles->getId();

        $panier = $this->getSession()->get('panier', []);

        if ( !empty($panier[$id]) ){
            // mettre un overlay pour être sur que le client souhaite retirer l'article de son panier
            unset( $panier[$id] );
        }

        $this->getSession()->set('panier', $panier);
    }

}

?>