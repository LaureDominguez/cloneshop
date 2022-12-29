<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GalleryRepository;

#[Route('/product')]
class ProductController extends AbstractController
{
    public CartController $cartController;
    public function __construct(CartController $cartController)
    {
        $this->cartController = $cartController;
    }
    public function checkCart()
    {
        $this->checkCart = $this->cartController->checkCart();
    }
///////// Index /////////////////

    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
            'display_cart' => $this->checkCart(),
        ]);
    }

///////// New /////////

    #[Route('admin/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository$productRepository, GalleryRepository $galleryRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            $images = $form->get('gallery')->getData();

                if ($images == null) {
                    $img = new Gallery();
                    $img->setPicture('<i class="fa-regular fa-image"></i>');
                    $galleryRepository->save($img, true);
                    $product->addGallery($img);
                    $productRepository->save($product, true);
                    return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
                }
                
                else {
                    $fichier = md5(uniqid()) . '-' . uniqid() . '.' . $images->guessExtension();
                    $images->move(
                        $this->getParameter('kernel.project_dir') . ('/public/uploads/images_directory'),
                        $fichier
                    );
                    $img = new Gallery();
                    $img->setPicture($fichier);
                    $galleryRepository->save($img, true);
                    $product->addGallery($img);
                    $productRepository->save($product, true);
                    return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
                };
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'display_cart' => false,
        ]);
    }

////////////// Show ////////////

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, GalleryRepository $galleryRepository): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'gallery' => $galleryRepository->findBy([
                "product" => $product,
            ]),
            'display_cart' => $this->checkCart(),
        ]);
    }

///////////// Edit ////////////////////

    #[Route('admin/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, GalleryRepository $galleryRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            $images = $form->get('gallery')->getData();

            if ($images == null) {
                $img = new Gallery();
                $img->setPicture('<i class="fa-regular fa-image"></i>');
                $galleryRepository->save($img, true);
                $product->addGallery($img);
                $productRepository->save($product, true);
                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            } else {
                $fichier = md5(uniqid()) . '-' . uniqid() . '.' . $images->guessExtension();
                $images->move(
                    $this->getParameter('kernel.project_dir') . ('/public/uploads/images_directory'),
                    $fichier
                );
                $img = new Gallery();
                $img->setPicture($fichier);
                $galleryRepository->save($img, true);
                $product->addGallery($img);
                $productRepository->save($product, true);
                return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
            };
        }


        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'display_cart' => false,
        ]);
    }

///////////////// Delete ///////////////

    #[Route('admin/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', ['display_cart' => false,], Response::HTTP_SEE_OTHER);
    }
}
