<?php

namespace App\Controller;

use App\Entity\Cursus;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CertificationController extends AbstractController
{
    #[Route('/certificat/{id}', name: 'generate_certificat')]
    public function generate(Cursus $cursus): Response
    {
        $user = $this->getUser();

        // Vérifie si l'utilisateur a acheté le cursus
        $hasAccess = false;
        foreach ($user->getAchats() as $achat) {
            if ($achat->getCursus()->getId() === $cursus->getId()) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $this->addFlash('danger', "⛔ Vous n'avez pas accès à ce certificat.");
            return $this->redirectToRoute('app_dashboard');
        }

        // Vérifie que le cursus contient bien des leçons
        if (!$cursus->hasLecons()) {
            $this->addFlash('warning', '❌ Ce cursus ne contient pas encore de leçons. Le certificat ne peut pas être généré.');
            return $this->redirectToRoute('app_dashboard');
        }

        // Génération du PDF avec Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('certificat/certificat.html.twig', [
            'user' => $user,
            'cursus' => $cursus,
            'date' => new \DateTime(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="certificat.pdf"',
        ]);
    }
}