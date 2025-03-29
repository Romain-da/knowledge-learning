<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Entity\Lecon;
use App\Entity\LeconSuivie;
use App\Repository\LeconRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LeconController extends AbstractController
{
    #[Route('/lecons/{id}', name: 'lecons_cursus')]
    public function index(Cursus $cursus, LeconRepository $leconRepository): Response
    {
        $user = $this->getUser();

        if (!$user || !$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'Vous devez être connecté pour accéder aux leçons.');
            return $this->redirectToRoute('app_login');
        }

        $aAchat = $user->getAchats()->exists(fn($key, $achat) => $achat->getCursus() === $cursus);

        if (!$aAchat) {
            $this->addFlash('danger', 'Vous n\'avez pas acheté ce cursus.');
            return $this->redirectToRoute('app_dashboard');
        }

        $lecons = $leconRepository->findBy(['cursus' => $cursus]);

        return $this->render('lecon/index.html.twig', [
            'cursus' => $cursus,
            'lecons' => $lecons,
        ]);
    }

    #[Route('/lecon/{id}/voir', name: 'voir_lecon')]
    public function voirLecon(Lecon $lecon, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user || !$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'Vous devez être connecté pour consulter cette leçon.');
            return $this->redirectToRoute('app_login');
        }

        // 🔐 Vérifie que l’utilisateur a bien accès à cette leçon (par cursus ou leçon achetée individuellement)
        $aAchatCursus = $user->getAchats()->exists(fn($k, $a) => $a->getCursus() === $lecon->getCursus());
        $aLeconIndividuelle = $user->getAchatLecons()->exists(fn($k, $a) => $a->getLecon() === $lecon);

        if (!$aAchatCursus && !$aLeconIndividuelle) {
            $this->addFlash('danger', '⛔ Vous n\'avez pas accès à cette leçon.');
            return $this->redirectToRoute('app_dashboard');
        }

        // 🔄 Enregistre la consultation (si pas déjà vue)
        $suivieRepo = $em->getRepository(LeconSuivie::class);
        $existe = $suivieRepo->findOneBy([
            'user' => $user,
            'lecon' => $lecon,
        ]);

        if (!$existe) {
            $suivi = new LeconSuivie();
            $suivi->setUser($user);
            $suivi->setLecon($lecon);
            $suivi->setDateVue(new \DateTime());

            $em->persist($suivi);
            $em->flush();
        }

        return $this->render('lecon/voir.html.twig', [
            'lecon' => $lecon,
        ]);
    }
}
