<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class TestController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        $users = $this->userRepository->findAll();

        return $this->render('test/index.html.twig', [
            'users' => $users,
        ]);
    }
}
