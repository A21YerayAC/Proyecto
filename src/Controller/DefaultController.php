<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function index()
    {
        return $this->render('login.html.twig');
    }

    #[Route(path: '/main', name: 'app_homepage')]
    public function main()
    {
        return $this->render('main.html.twig');
    }

    #[Route(path: '/registro', name: 'app_registro')]
    public function registro()
    {
        return $this->render('registro.html.twig');
    }
}
