<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CategoryRepository;

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
    public function showCategoryAction($category, EntityManagerInterface $em): Response
    {
        // Translate category name to id
        $categoryEntity = $em
        ->getRepository(Category::class)
        ->findOneBy(['title' => $category]);

        if (!$categoryEntity) {
            throw $this->createNotFoundException('Category not found');
        }

        $categoryId = $categoryEntity->getId();

        // Get posts with their postMedias only from the category
        $postsWithMedia = $em
        ->getRepository(Post::class)
        ->createQueryBuilder('p')
        ->leftJoin('p.postMedia', 'pm')
        ->where('p.category = :category')
        ->setParameter('category', $categoryId)
        ->getQuery()
        ->getResult();

        if (!$postsWithMedia) {
            throw $this->createNotFoundException('Posts not found');
        }

        return $this->render('category/show_category.html.twig', [
            'categoryName' => $category,
            'posts' => $postsWithMedia
        ]);
    }
}