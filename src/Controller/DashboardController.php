<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Achat;
use App\Entity\Certification;
use App\Form\AchatType; 
use App\Form\CertificationType;
use App\Form\RegistrationFormType;
use App\Repository\AchatRepository;
use App\Repository\CertificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(AchatRepository $achatRepository, CertificationRepository $certificationRepository): Response
    {
        $user = $this->getUser(); // Récupération de l'utilisateur connecté

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $this->redirectToRoute('admin_dashboard'); // Redirige les admins directement
        }

        return $this->render('dashboard/client.html.twig', [
            'user' => $user,
            'achats' => $achatRepository->findBy(['user' => $user]),
            'certifications' => $certificationRepository->findBy(['user' => $user]),
        ]);
    }

    // ✅ GESTION ADMIN
    #[Route('/dashboard/admin', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        return $this->render('dashboard/admin.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/users', name: 'admin_users')]
    public function manageUsers(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('dashboard/admin/users.html.twig', [
            'users' => $userRepository->findAll(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/users/edit/{id}', name: 'admin_user_edit')]
    public function editUser(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('dashboard/admin/edit_user.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/dashboard/admin/users/delete/{id}', name: 'admin_user_delete')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($user);
        $em->flush();
        $this->addFlash('danger', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('admin_users');
    }

    // ✅ GESTION CONTENUS (CERTIFICATIONS)
    #[Route('/dashboard/admin/content', name: 'admin_content')]
    public function manageContent(CertificationRepository $certificationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('dashboard/admin/content.html.twig', [
            'certifications' => $certificationRepository->findAll(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/content/create', name: 'admin_content_create')]
    public function createCertification(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $certification = new Certification();
        $form = $this->createForm(CertificationType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($certification);
            $em->flush();
            $this->addFlash('success', 'Certification ajoutée avec succès.');
            return $this->redirectToRoute('admin_content');
        }

        return $this->render('dashboard/admin/certification_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/content/edit/{id}', name: 'admin_certification_edit')]
    public function editCertification(Certification $certification, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CertificationType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Certification modifiée avec succès.');
            return $this->redirectToRoute('admin_content');
        }

        return $this->render('dashboard/admin/certification_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/content/delete/{id}', name: 'admin_certification_delete')]
    public function deleteCertification(Certification $certification, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($certification);
        $em->flush();
        $this->addFlash('danger', 'Certification supprimée avec succès.');
        return $this->redirectToRoute('admin_content');
    }

    // ✅ GESTION ACHATS
    #[Route('/dashboard/admin/orders', name: 'admin_orders')]
    public function manageOrders(AchatRepository $achatRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('dashboard/admin/orders.html.twig', [
            'achats' => $achatRepository->findAll(),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/orders/create', name: 'admin_order_create')]
    public function createOrder(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $achat = new Achat();
        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($achat);
            $em->flush();
            $this->addFlash('success', 'Achat ajouté avec succès.');
            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('dashboard/admin/order_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => false,
        ]);
    }

    #[Route('/dashboard/admin/orders/edit/{id}', name: 'admin_order_edit')]
    public function editOrder(Achat $achat, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(AchatType::class, $achat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Achat modifié avec succès.');
            return $this->redirectToRoute('admin_orders');
        }

        return $this->render('dashboard/admin/order_form.html.twig', [
            'form' => $form->createView(),
            'is_edit' => true,
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/dashboard/admin/orders/delete/{id}', name: 'admin_order_delete')]
    public function deleteOrder(Achat $achat, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($achat);
        $em->flush();
        $this->addFlash('danger', 'Achat supprimé avec succès.');
        return $this->redirectToRoute('admin_orders');
    }
}
