<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/paiement/success', name: 'payment_success')]
    public function success(PanierService $panierService): Response
    {
        $panierService->vider();
        $this->addFlash('success', '🎉 Paiement réussi ! Merci pour votre achat.');
        return $this->redirectToRoute('app_boutique');
    }

    #[Route('/paiement/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('paiement/cancel.html.twig');
    }
}
