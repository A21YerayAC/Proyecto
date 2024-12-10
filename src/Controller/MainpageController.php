<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainpageController extends AbstractController
{
    #[Route(path: '/main', name: 'app_homepage')]
    public function index(Security $security, ReviewRepository $reviewRepository): Response
    {
    return $this->render('main.html.twig'
    );
    }
}
