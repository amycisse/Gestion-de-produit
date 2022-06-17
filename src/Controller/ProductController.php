<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ProductRepository;

use App\Repository\ProductRepository as RepositoryProductRepository;
use Container1n05Ibm\getProductRepositoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $produitRepositoty;
    public function __construct(ManagerRegistry $doctrine
    )
    {
        $this->entityManager = $doctrine->getManager();
        
    }
    #[Route('/product', name: 'product_list')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/store/product', name: 'product_store')]
    public function store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            if ($request->files->get('product')['image']) {
              $image = $request->files->get('product')['image'];   
              $image_name = time().'_'.$image->getClientOriginalName();
              $image->move($this->getParameter('image_directory'),$image_name);
              $product->setImage($image_name);
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'your product was saved');  
                $this->redirectToRoute('product_list');   
         }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }
}
