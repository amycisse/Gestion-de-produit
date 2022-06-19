<?php

namespace App\Controller;
use Doctrine\ORM\EntityRepository;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\ProductRepository;

use App\Repository\ProductRepository as RepositoryProductRepository;
use Container1n05Ibm\getProductRepositoryService;
use PHPUnit\Util\Filesystem;
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

    #[Route('/product/details/{id}', name: 'product_show')]
    public function show(ProductRepository $product,$id): Response
    {
        return $this->renderForm('product/show.html.twig', [
            'product' => $product->find($id),
            'photo_url'=>'http://127.0.0.1:8000/uploads/'
        ]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function editProduct(Product $product,Request $request): Response
    {
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
                'your product was updated');  
                $this->redirectToRoute('product_list');   
         }
        return $this->renderForm('product/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(ProductRepository $product): Response
    {
         $filesystem = new Filesystem();
         $imagePath = './uploats/'.$product->getImage();
         if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
         }
         $this->entityManager->remove($product);
         $this->entityManager->flush();
         $this->addFlash(
             'success',
             'your product was removed'); 
             $this->redirectToRoute('product_list');
    }
}
