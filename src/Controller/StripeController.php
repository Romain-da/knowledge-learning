<?php

namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/paiement/checkout', name: 'stripe_checkout', methods: ['POST'])]
    public function checkout(StripeService $stripeService): JsonResponse
    {
        $session = $stripeService->createCheckoutSession(
            [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Achat de cursus'],
                    'unit_amount' => 1000, // 10,00€
                ],
                'quantity' => 1,
            ],
            $this->generateUrl('payment_success', [], true),
            $this->generateUrl('payment_cancel', [], true)
        );

        return $this->json(['id' => $session->id]);
    }

    #[Route('/paiement/success', name: 'payment_success')]
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/paiement/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/cancel.html.twig');
    }
}
