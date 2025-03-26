<?php

namespace App\Controller;

use App\Service\PanierService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/paiement', name: 'paiement')]
    public function index(StripeService $stripeService): Response
    {
        // Test si la clé est bien récupérée
        dump($stripeService); die();

        return $this->render('paiement/index.html.twig');
    }

    #[Route('/paiement/success', name: 'paiement_success')]
    public function success(PanierService $panierService): Response
    {
        $panierService->vider();

        return $this->render('paiement/success.html.twig');
    }

    #[Route('/paiement/annule', name: 'paiement_annule')]
    public function cancel(): Response
    {
        return $this->render('paiement/annule.html.twig');
    }
}
