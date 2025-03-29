<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\CursusRepository;

class PanierService
{
    private $session;
    private CursusRepository $cursusRepository;

    public function __construct(RequestStack $requestStack, CursusRepository $cursusRepository)
    {
        $this->session = $requestStack->getSession();
        $this->cursusRepository = $cursusRepository;
    }

    public function ajouter(int $id): void
    {
        $panier = $this->session->get('panier', []);

        $panier[$id] = ($panier[$id] ?? 0) + 1;

        $this->session->set('panier', $panier);
    }

    public function retirer(int $id): void
    {
        $panier = $this->session->get('panier', []);

        if (!isset($panier[$id])) return;

        if ($panier[$id] > 1) {
            $panier[$id]--;
        } else {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function vider(): void
    {
        $this->session->remove('panier');
    }

    /**
     * Retourne un tableau enrichi : [ 'cursus' => Cursus, 'quantite' => int ]
     */
    public function getPanier(): array
    {
        $panier = $this->session->get('panier', []);
        $details = [];

        foreach ($panier as $id => $quantite) {
            $cursus = $this->cursusRepository->find($id);

            if ($cursus) {
                $details[] = [
                    'cursus' => $cursus,
                    'quantite' => $quantite,
                ];
            }
        }

        return $details;
    }

    /**
     * Retourne un tableau simplifié à enregistrer en session pour le paiement
     * (utile si tu veux t’en servir ailleurs aussi)
     */
    public function getPanierPourSession(): array
    {
        $panier = $this->session->get('panier', []);
        $sessionItems = [];

        foreach ($panier as $id => $quantite) {
            $sessionItems[] = [
                'cursus_id' => $id,
                'quantite' => $quantite,
            ];
        }

        return $sessionItems;
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getPanier() as $item) {
            $total += $item['cursus']->getPrix() * $item['quantite'];
        }

        return $total;
    }
}
