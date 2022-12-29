<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/category')]
class CategoryController extends AbstractController
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

    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'display_cart' => $this->checkCart(),
        ]);
    }

    #[Route('admin/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_shop_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
            'display_cart' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category, ProductRepository $productRepository): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $productRepository->findBy([
                "category" => $category
            ]),
            'display_cart' => $this->checkCart(),
        ]);
    }

    #[Route('admin/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $name = $request->request->get('name');
        $id = $request->attributes->get('category');
        $id = $id->getId();
        $category = $entityManager->getRepository(Category::class)->find($id);
        $category->setName($name);
        $entityManager->flush();
        return $this->redirectToRoute('app_shop_admin');
    }


    // #[Route('admin/{id}', name: 'app_category_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    // {
    //     $form = $this->createForm(CategoryType::class, $category);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $categoryRepository->save($category, true);

    //         return $this->redirectToRoute('app_shop_admin', [], Response::HTTP_SEE_OTHER);
    //     };
    // }

    #[Route('admin/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_shop_admin', ['display_cart' => false,], Response::HTTP_SEE_OTHER);
    }
}
