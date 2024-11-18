<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class DefaultController extends AbstractController
{
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
