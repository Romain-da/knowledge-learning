<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Entity\LeconSuivie;
use App\Repository\LeconRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CertificationController extends AbstractController
{
    #[Route('/certificat/{id}', name: 'generate_certificat')]
    public function generate(
        Cursus $cursus,
        LeconRepository $leconRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        // ✅ Vérifie que l'utilisateur a bien acheté ce cursus
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

        // ✅ Vérifie que toutes les leçons ont été suivies
        $lecons = $leconRepository->findBy(['cursus' => $cursus]);
        $suivies = $em->getRepository(LeconSuivie::class)->findBy(['user' => $user]);

        $leconsSuiviesIds = array_map(fn($ls) => $ls->getLecon()->getId(), $suivies);

        foreach ($lecons as $lecon) {
            if (!in_array($lecon->getId(), $leconsSuiviesIds)) {
                $this->addFlash('warning', '⚠️ Vous devez consulter toutes les leçons avant de pouvoir générer votre certificat.');
                return $this->redirectToRoute('lecons_cursus', ['id' => $cursus->getId()]);
            }
        }

        // ✅ Génère le certificat PDF
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
