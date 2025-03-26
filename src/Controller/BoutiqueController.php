<?php

namespace App\Controller;

use App\Repository\CursusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(CursusRepository $cursusRepository): Response
    {
        // Récupère tous les cursus
        $cursus = $cursusRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'cursusList' => $cursus, // On envoie les cursus à la vue
        ]);
    }
}