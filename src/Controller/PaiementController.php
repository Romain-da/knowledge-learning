<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Repository\AchatRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaiementController extends AbstractController
{
    #[Route('/paiement/success', name: 'payment_success')]
    public function success(PanierService $panierService, PdfGenerator $pdfGenerator, RequestStack $requestStack, AchatRepository $achatRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Récupérer la session
        $session = $requestStack->getSession();

        // Récupérer l'ID de l'achat en attente depuis la session
        $achatId = $session->get('achat_en_attente');

        if (!$achatId) {
            $this->addFlash('warning', 'Aucun achat en attente trouvé.');
            return $this->redirectToRoute('app_boutique');
        }

        // Récupérer l'achat correspondant depuis la base de données
        $achat = $achatRepository->find($achatId);

        if (!$achat || $achat->getUser() !== $user) {
            $this->addFlash('danger', 'Achat non trouvé ou vous n\'avez pas les droits nécessaires.');
            return $this->redirectToRoute('app_boutique');
        }

        // Générer le certificat PDF
        $pdfContent = $pdfGenerator->generate('certificat/certificat.html.twig', [
            'user' => $user,
            'achat' => $achat,
        ]);

        // Vider le panier après l'achat
        $panierService->vider();

        // Supprimer l'ID de l'achat en attente de la session
        $session->remove('achat_en_attente');

        // Ajouter un message flash pour informer l'utilisateur du succès du paiement
        $this->addFlash('success', '🎉 Paiement réussi ! Merci pour votre achat.');

        // Retourner la réponse avec le PDF en pièce jointe
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificat.pdf"',
        ]);
    }

    #[Route('/paiement/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('paiement/cancel.html.twig');
    }

}
