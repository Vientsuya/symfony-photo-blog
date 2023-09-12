<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/comment/add_comment', name: "add_comment")]
    public function handleCommentSubmission(Request $request): Response
    {
        // Handle form submission here
        // Retrieve and validate comment data from $request
        // Save the comment to the database

        $this->addFlash('success', 'Comment submitted successfully!');

        // Redirect back to the page where the comment was submitted
        return $this->redirectToRoute('target_page'); // Replace 'target_page' with your actual route
    }
}
