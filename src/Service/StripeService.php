<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        // 🔒 Sécurité : on vérifie que la clé est bien définie
        $secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;

        if (!$secretKey) {
            throw new \RuntimeException('❌ La clé STRIPE_SECRET_KEY est manquante dans votre environnement.');
        }

        Stripe::setApiKey($secretKey);
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Crée une session Stripe Checkout
     *
     * @param array $lineItem Un seul élément (nom, prix, quantité, etc.)
     * @return Session
     */
    public function createCheckoutSession(array $items): Session
    {
        $lineItems = [];

        foreach ($items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['cursus']->getNom(),
                    ],
                    'unit_amount' => $item['cursus']->getPrix() * 100, // En centimes
                ],
                'quantity' => $item['quantite'],
            ];
        }

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
