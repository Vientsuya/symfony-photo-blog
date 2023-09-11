<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: "app_category")]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
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
        $postsWithMediaAndComments = $em
        ->getRepository(Post::class)
        ->createQueryBuilder('posts')
        ->leftJoin('posts.postMedia', 'postsMedia')
        ->leftJoin('posts.comments', 'comments') // Join the comments related to the post
        ->leftJoin('comments.created_by', 'commentUser') // Join the users who created the comments
        ->where('posts.category = :category')
        ->setParameter('category', $categoryId)
        ->orderBy('posts.created_at', 'DESC')
        ->getQuery()
        ->getResult();

        $pagination = $paginator->paginate($postsWithMediaAndComments, $request->query->getInt('page', 1), 12);

        if (!$postsWithMediaAndComments) {
            throw $this->createNotFoundException('Posts not found');
        }

        return $this->render('category/show_category.html.twig', [
            'categoryName' => $category,
            'pagination' => $pagination,
        ]);
    }
}
