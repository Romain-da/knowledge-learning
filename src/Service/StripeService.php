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
        // On lit la clé ici (pas dans les arguments)
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? throw new \RuntimeException('Clé STRIPE_SECRET_KEY manquante'));
        $this->urlGenerator = $urlGenerator;
    }

    public function createCheckoutSession(array $items): Session
    {
        $lineItems = [];

        foreach ($items as $item) {
            $label = $item['type'] === 'lecon'
                ? 'Leçon : ' . $item['item']->getTitre()
                : 'Cursus : ' . $item['item']->getNom();

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => $label],
                    'unit_amount' => $item['prix'] * 100,
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

