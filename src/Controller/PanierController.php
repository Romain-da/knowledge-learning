<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier_afficher')]
    public function index(PanierService $panierService): Response
    {
        return $this->render('panier/index.html.twig', [
            'panier' => $panierService->getPanier(),
            'total' => $panierService->getTotal()
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'panier_ajouter')]
    public function ajouter(PanierService $panierService, Cursus $cursus): Response
    {
        $panierService->ajouter($cursus->getId());
        $this->addFlash('success', '✅ Cursus ajouté au panier !');

        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/panier/retirer/{id}', name: 'panier_retirer')]
    public function retirer(PanierService $panierService, Cursus $cursus): Response
    {
        $panierService->retirer($cursus->getId());
        $this->addFlash('warning', '⚠️ Cursus retiré du panier.');

        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/panier/vider', name: 'panier_vider')]
    public function vider(PanierService $panierService): Response
    {
        $panierService->vider();
        $this->addFlash('danger', '🗑 Panier vidé.');

        return $this->redirectToRoute('panier_afficher');
    }
}
