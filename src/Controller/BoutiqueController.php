<?php

namespace App\Controller;

use App\Repository\CursusRepository;
use App\Repository\LeconRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(CursusRepository $cursusRepository, LeconRepository $leconRepository): Response
    {
        $cursusList = $cursusRepository->findAll();
        $lecons = $leconRepository->findBy(['isValidated' => true]);

        return $this->render('boutique/index.html.twig', [
            'cursusList' => $cursusList,
            'lecons' => $lecons,
        ]);
    }
}

