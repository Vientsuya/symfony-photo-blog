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
use App\Entity\Comment;
use App\Form\Type\CommentType;
use Symfony\Component\Security\Core\Security;

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
    public function showCategoryAction(Request $request, $category, EntityManagerInterface $em, PaginatorInterface $paginator, Security $security): Response
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

        // Handle a comment form
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(\DateTimeImmutable::createFromMutable(new \DateTime()));
            
            // Set the 'post' field of the comment entity based on the hidden field value
            $postId = $request->request->get('comment')['post'];
            $post = $em->getRepository(Post::class)->find($postId);
            $comment->setPost($post);

            $comment->setCreatedBy($security->getUser());
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('category', ['category' => $category]);
        }

        if (!$postsWithMediaAndComments) {
            throw $this->createNotFoundException('Posts not found');
        }

        return $this->render('category/show_category.html.twig', [
            'categoryName' => $category,
            'pagination' => $pagination,
            'commentForm' => $form,
        ]);
    }
}
