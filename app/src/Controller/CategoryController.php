<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{category}', name: 'category')]
    public function showCategoryAction(Request $request, $category, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        // Translate category name to id
        $categoryEntity = $em
        ->getRepository(Category::class)
        ->findOneBy(['title' => $category]);

        if (!$categoryEntity) {
            throw $this->createNotFoundException('Category not found');
        }

        $categoryId = $categoryEntity->getId();

        // Get posts with their postMedias only from the specific category
        $postsWithMedia = $em
        ->getRepository(Post::class)
        ->createQueryBuilder('p')
        ->leftJoin('p.postMedia', 'pm')
        ->where('p.category = :category')
        ->setParameter('category', $categoryId)
        ->orderBy('p.created_at', 'DESC')
        ->getQuery()
        ->getResult();

        $pagination = $paginator->paginate($postsWithMedia, $request->query->getInt('page', 1), 12);

        if (!$postsWithMedia) {
            throw $this->createNotFoundException('Posts not found');
        }

        return $this->render('category/show_category.html.twig', [
            'categoryName' => $category,
            'pagination' => $pagination
        ]);
    }
}