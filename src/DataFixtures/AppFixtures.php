<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Cursus;
use App\Entity\Lecon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // ✅ Création d'un utilisateur standard
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setNom('Utilisateur');
        $user->setPrenom('Test');
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));
        $manager->persist($user);

        // ✅ Création d'un administrateur
        $admin = new User();
        $admin->setEmail('admin@example.fr');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin1234'));
        $manager->persist($admin);

        // ✅ Cursus avec leurs leçons (et prix des leçons)
        $data = [
            [
                'nom' => 'Développement Web',
                'categorie' => 'Informatique',
                'description' => 'Apprenez à créer des sites web modernes.',
                'prix' => 79.99,
                'lecons' => [
                    ['titre' => 'Introduction au HTML', 'contenu' => 'Contenu HTML...', 'prix' => 19.99],
                    ['titre' => 'CSS pour les débutants', 'contenu' => 'Contenu CSS...', 'prix' => 24.99],
                    ['titre' => 'Bases de JavaScript', 'contenu' => 'Contenu JavaScript...', 'prix' => 29.99],
                ],
            ],
            [
                'nom' => 'Marketing Digital',
                'categorie' => 'Communication',
                'description' => 'Maîtrisez les outils du marketing numérique.',
                'prix' => 59.99,
                'lecons' => [
                    ['titre' => 'SEO : Les bases', 'contenu' => 'Contenu SEO...', 'prix' => 19.99],
                    ['titre' => 'Publicité sur les réseaux', 'contenu' => 'Contenu Ads...', 'prix' => 24.99],
                ],
            ],
            [
                'nom' => 'Design UX/UI',
                'categorie' => 'Design',
                'description' => 'Créez des interfaces intuitives.',
                'prix' => 49.99,
                'lecons' => [
                    ['titre' => 'Principes de design', 'contenu' => 'Contenu design...', 'prix' => 19.99],
                    ['titre' => 'Introduction à Figma', 'contenu' => 'Contenu Figma...', 'prix' => 24.99],
                ],
            ],
        ];

        foreach ($data as $cursusData) {
            $cursus = new Cursus();
            $cursus->setNom($cursusData['nom']);
            $cursus->setCategorie($cursusData['categorie']);
            $cursus->setDescription($cursusData['description']);
            $cursus->setPrix($cursusData['prix']);
            $manager->persist($cursus);

            foreach ($cursusData['lecons'] as $leconData) {
                $lecon = new Lecon();
                $lecon->setTitre($leconData['titre']);
                $lecon->setContenu($leconData['contenu']);
                $lecon->setPrix($leconData['prix']); // 🆕 Prix individuel de la leçon
                $lecon->setCursus($cursus);
                $lecon->setValidated(true);
                $manager->persist($lecon);
            }
        }

        $manager->flush();
    }
}
