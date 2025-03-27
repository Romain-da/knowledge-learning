<?php

namespace App\Controller;

use App\Service\StripeService;
use App\Service\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/paiement', name: 'paiement')]
    public function index(PanierService $panierService): Response
    {
        return $this->render('paiement/index.html.twig', [
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? 'clé_manquante',
            'total' => $panierService->getTotal()
        ]);
    }

    #[Route('/paiement/checkout', name: 'stripe_checkout', methods: ['POST'])]
    public function checkout(StripeService $stripeService, PanierService $panierService): JsonResponse
    {
        $items = $panierService->getPanier();

        if (empty($items)) {
            return $this->json(['error' => 'Panier vide'], 400);
        }

        $session = $stripeService->createCheckoutSession(
            $items,
            $this->generateUrl('payment_success', [], true),
            $this->generateUrl('payment_cancel', [], true)
        );

        return $this->json(['id' => $session->id]);
    }

    #[Route('/paiement/success', name: 'payment_success')]
    public function success(PanierService $panierService): Response
    {
        // ✅ Vider le panier après un paiement réussi
        $panierService->vider();

        $this->addFlash('success', '🎉 Paiement réussi, merci pour votre achat !');
        return $this->redirectToRoute('app_boutique');
    }

    #[Route('/paiement/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('warning', '⚠️ Paiement annulé.');
        return $this->redirectToRoute('panier_afficher');
    }
}
