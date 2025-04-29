<?php

// src/Controller/FrontOfficeController.php

// src/Controller/FrontOfficeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontOfficeController extends AbstractController
{
    #[Route('/aceuill', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('frontoffice/index.html.twig');
    }
}
