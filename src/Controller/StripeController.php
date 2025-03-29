<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\AchatLecon;
use App\Repository\CursusRepository;
use App\Repository\LeconRepository;
use App\Service\PanierService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
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
    public function checkout(
        StripeService $stripeService,
        PanierService $panierService,
        RequestStack $requestStack
    ): JsonResponse {
        $panier = $panierService->getPanier();

        if (empty($panier)) {
            return $this->json(['error' => 'Panier vide'], 400);
        }

        // Préparer un panier simplifié à enregistrer en session
        $panierEnAttente = [];

        foreach ($panier as $item) {
            $panierEnAttente[] = [
                'type' => $item['type'],
                'id' => $item['item']->getId(),
                'quantite' => $item['quantite']
            ];
        }

        $requestStack->getSession()->set('achat_en_attente', $panierEnAttente);

        $sessionStripe = $stripeService->createCheckoutSession($panier);

        return $this->json(['id' => $sessionStripe->id]);
    }

    #[Route('/paiement/success', name: 'payment_success')]
    public function success(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        PanierService $panierService,
        CursusRepository $cursusRepository,
        LeconRepository $leconRepository
    ): Response {
        $user = $this->getUser();
        $session = $requestStack->getSession();
        $panier = $session->get('achat_en_attente', []);

        if (!$user || empty($panier)) {
            $this->addFlash('warning', 'Aucun achat à valider.');
            return $this->redirectToRoute('app_dashboard');
        }

        foreach ($panier as $item) {
            if ($item['type'] === 'cursus') {
                $cursus = $cursusRepository->find($item['id']);
                if ($cursus) {
                    $achat = new Achat();
                    $achat->setUser($user);
                    $achat->setCursus($cursus);
                    $achat->setDateAchat(new \DateTime());
                    $achat->setMontant($cursus->getPrix() * $item['quantite']);
                    $em->persist($achat);
                }
            } elseif ($item['type'] === 'lecon') {
                $lecon = $leconRepository->find($item['id']);
                if ($lecon) {
                    $achatLecon = new AchatLecon();
                    $achatLecon->setUser($user);
                    $achatLecon->setLecon($lecon);
                    $achatLecon->setDateAchat(new \DateTime());
                    $achatLecon->setMontant($lecon->getPrix() * $item['quantite']);
                    $em->persist($achatLecon);
                }
            }
        }

        $em->flush();
        $panierService->vider();
        $session->remove('achat_en_attente');

        $this->addFlash('success', '🎉 Paiement réussi, vos achats ont été enregistrés !');
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/paiement/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('danger', '❌ Paiement annulé.');
        return $this->redirectToRoute('app_dashboard');
    }
}
