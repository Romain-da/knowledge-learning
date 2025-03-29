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
        // Création d'un utilisateur standard
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setNom('Utilisateur');
        $user->setPrenom('Test');
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);
        $user->setPassword($this->hasher->hashPassword($user, 'admin1234'));
        $manager->persist($user);

        // Création d'un administrateur
        $admin = new User();
        $admin->setEmail('admin@example.fr');
        $admin->setNom('Admin');
        $admin->setPrenom('Super');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin1234'));
        $manager->persist($admin);

        // Liste des cursus avec leurs leçons
        $data = [
            [
                'nom' => 'Développement Web',
                'categorie' => 'Informatique',
                'description' => 'Apprenez à créer des sites web modernes.',
                'prix' => 79.99,
                'lecons' => [
                    ['titre' => 'Introduction au HTML', 'contenu' => 'Contenu HTML...'],
                    ['titre' => 'CSS pour les débutants', 'contenu' => 'Contenu CSS...'],
                    ['titre' => 'Bases de JavaScript', 'contenu' => 'Contenu JavaScript...'],
                ],
            ],
            [
                'nom' => 'Marketing Digital',
                'categorie' => 'Communication',
                'description' => 'Maîtrisez les outils du marketing numérique.',
                'prix' => 59.99,
                'lecons' => [
                    ['titre' => 'SEO : Les bases', 'contenu' => 'Contenu SEO...'],
                    ['titre' => 'Publicité sur les réseaux', 'contenu' => 'Contenu Ads...'],
                ],
            ],
            [
                'nom' => 'Design UX/UI',
                'categorie' => 'Design',
                'description' => 'Créez des interfaces intuitives.',
                'prix' => 49.99,
                'lecons' => [
                    ['titre' => 'Principes de design', 'contenu' => 'Contenu design...'],
                    ['titre' => 'Introduction à Figma', 'contenu' => 'Contenu Figma...'],
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
                $lecon->setCursus($cursus);
                $manager->persist($lecon);
            }
        }

        $manager->flush();
    }
}
