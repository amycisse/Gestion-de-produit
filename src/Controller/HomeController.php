<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'photo_url'=>'http://127.0.0.1:8000/uploads/'
        ]);
    }

    #[Route('/product/{category}', name: 'product_category')]
    public function categoryProduct(CategoryRepository $categoryR,Category $category): Response
    {
        return $this->renderForm('home/index.html.twig', [
            'categories' => $categoryR->findAll(),
            'products' => $category->getProducts(),
            'photo_url'=>'http://127.0.0.1:8000/uploads/'
        ]);
    }
}
