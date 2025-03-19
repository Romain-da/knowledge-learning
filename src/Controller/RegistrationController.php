<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Encoder le mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Sauvegarde en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'email de confirmation
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('noreply@knowledge-learning.com', 'Knowledge Learning'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de votre email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Message flash pour indiquer à l'utilisateur de vérifier son email
            $this->addFlash('success', 'Un email de confirmation a été envoyé. Veuillez vérifier votre boîte mail.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            // Marquer l'utilisateur comme vérifié
            $user->setIsVerified(true);
            $entityManager->flush();
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // Message flash de succès après vérification
        $this->addFlash('success', 'Votre adresse email a été vérifiée avec succès. Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }
}
