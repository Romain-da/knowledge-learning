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

        if (!isset($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        $this->session->set('panier', $panier);
    }

    public function retirer(int $id): void
    {
        $panier = $this->session->get('panier', []);

        if (isset($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $this->session->set('panier', $panier);
    }

    public function vider(): void
    {
        $this->session->remove('panier');
    }

    public function getPanier(): array
    {
        $panier = $this->session->get('panier', []);
        $panierDetails = [];

        foreach ($panier as $id => $quantite) {
            $cursus = $this->cursusRepository->find($id);
            if ($cursus) {
                $panierDetails[] = [
                    'cursus' => $cursus,
                    'quantite' => $quantite
                ];
            }
        }

        return $panierDetails;
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
