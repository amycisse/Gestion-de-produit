<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
     
    #[Route('/orders', name: 'order')]
    public function index(): Response
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {
        if (!$this->getUser()) {
            $this->redirectToRoute('app_login'); 
        }
        return $this->render('order/user.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/store/order/{product}', name: 'order_store')]
    public function store(Product $product,ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        if (!$this->getUser()) {
            $this->redirectToRoute('app_login'); 
        }
        $order = new Order();
        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setStatus('processing ...');
        $order->setUser($this->getUser());
      
        $entityManager->persist($order);
        $entityManager->flush();
            $this->addFlash(
                'success',
                'your order was saved');  
               return $this->redirectToRoute('user_order_list');   
    }

}
