<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Repository\LeconRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeconController extends AbstractController
{
    #[Route('/lecons/{id}', name: 'lecons_cursus')]
    public function index(Cursus $cursus, LeconRepository $leconRepository): Response
    {
        $user = $this->getUser();

        if (!$user || !$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder aux leçons.');
            return $this->redirectToRoute('app_login');
        }

        // 🔒 Vérifie si l'utilisateur a bien acheté ce cursus
        $hasAccess = false;
        foreach ($user->getAchats() as $achat) {
            if ($achat->getCursus()->getId() === $cursus->getId()) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $this->addFlash('danger', 'Vous n\'avez pas acheté ce cursus.');
            return $this->redirectToRoute('app_dashboard');
        }

        $lecons = $leconRepository->findBy(['cursus' => $cursus]);

        return $this->render('lecon/index.html.twig', [
            'cursus' => $cursus,
            'lecons' => $lecons,
        ]);
    }
}
