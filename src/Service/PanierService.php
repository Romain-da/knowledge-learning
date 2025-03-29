<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\CursusRepository;
use App\Repository\LeconRepository;

class PanierService
{
    private $session;
    private CursusRepository $cursusRepository;
    private LeconRepository $leconRepository;

    public function __construct(RequestStack $requestStack, CursusRepository $cursusRepository, LeconRepository $leconRepository)
    {
        $this->session = $requestStack->getSession();
        $this->cursusRepository = $cursusRepository;
        $this->leconRepository = $leconRepository;
    }

    public function ajouterCursus(int $id): void
    {
        $panier = $this->session->get('panier', []);
        $key = 'cursus_' . $id;
        $panier[$key] = ($panier[$key] ?? 0) + 1;
        $this->session->set('panier', $panier);
    }

    public function retirerCursus(int $id): void
    {
        $panier = $this->session->get('panier', []);
        $key = 'cursus_' . $id;

        if (isset($panier[$key])) {
            if ($panier[$key] > 1) {
                $panier[$key]--;
            } else {
                unset($panier[$key]);
            }
        }

        $this->session->set('panier', $panier);
    }

    public function ajouterLecon(int $id): void
    {
        $panier = $this->session->get('panier', []);
        $key = 'lecon_' . $id;
        $panier[$key] = ($panier[$key] ?? 0) + 1;
        $this->session->set('panier', $panier);
    }

    public function retirerLecon(int $id): void
    {
        $panier = $this->session->get('panier', []);
        $key = 'lecon_' . $id;

        if (isset($panier[$key])) {
            if ($panier[$key] > 1) {
                $panier[$key]--;
            } else {
                unset($panier[$key]);
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
        $details = [];

        foreach ($panier as $key => $quantite) {
            if (str_starts_with($key, 'lecon_')) {
                $id = (int) str_replace('lecon_', '', $key);
                $lecon = $this->leconRepository->find($id);
                if ($lecon) {
                    $details[] = [
                        'type' => 'lecon',
                        'item' => $lecon,
                        'quantite' => $quantite,
                        'prix' => $lecon->getPrix()
                    ];
                }
            } elseif (str_starts_with($key, 'cursus_')) {
                $id = (int) str_replace('cursus_', '', $key);
                $cursus = $this->cursusRepository->find($id);
                if ($cursus) {
                    $details[] = [
                        'type' => 'cursus',
                        'item' => $cursus,
                        'quantite' => $quantite,
                        'prix' => $cursus->getPrix()
                    ];
                }
            }
        }

        return $details;
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getPanier() as $item) {
            $total += $item['prix'] * $item['quantite'];
        }
        return $total;
    }
}
