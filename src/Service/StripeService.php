<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    private string $secretKey;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(string $stripeSecretKey, UrlGeneratorInterface $urlGenerator)
    {
        $this->secretKey = $stripeSecretKey;
        $this->urlGenerator = $urlGenerator;

        // ✅ Initialisation Stripe dès le constructeur
        Stripe::setApiKey($this->secretKey);
    }

    public function createCheckoutSession(array $items): ?Session
    {
        try {
            $lineItems = [];

            foreach ($items as $item) {
                $cursus = $item['cursus'];
                $quantite = $item['quantite'];

                if (!$cursus || !is_numeric($cursus->getPrix())) {
                    throw new \InvalidArgumentException("Le prix du cursus est invalide.");
                }

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $cursus->getNom(),
                        ],
                        'unit_amount' => (int) ($cursus->getPrix() * 100), // ✅ Prix en centimes
                    ],
                    'quantity' => $quantite,
                ];
            }

            return Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->urlGenerator->generate('paiement_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->urlGenerator->generate('paiement_annule', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

        } catch (ApiErrorException $e) {
            // 🚨 Gère les erreurs Stripe
            throw new \RuntimeException("Erreur lors de la création de la session de paiement : " . $e->getMessage());
        }
    }
}
