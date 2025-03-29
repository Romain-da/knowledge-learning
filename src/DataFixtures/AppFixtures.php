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
                'nom' => 'Initiation à la guitare',
                'categorie' => 'Musique',
                'description' => 'Apprendre la guitare',
                'prix' => 50,
                'lecons' => [
                    ['titre' => 'Leçon n°1', 'contenu' => 'Découverte de la guitare', 'prix' => 26],
                    ['titre' => 'Leçon n°2', 'contenu' => 'Les accords et les gammes', 'prix' => 26],
                ],
            ],
            [
                'nom' => 'Initation au piano',
                'categorie' => 'Musique',
                'description' => 'Apprendre le piano',
                'prix' => 50,
                'lecons' => [
                    ['titre' => 'Leçon n°1', 'contenu' => 'Découverte de la guitare', 'prix' => 26],
                    ['titre' => 'Leçon n°2', 'contenu' => 'Les accords et les gammes', 'prix' => 26],
                ],
            ],
            [
                'nom' => 'Initation au développent web',
                'categorie' => 'Informatique',
                'description' => 'Apprendre le Développement web',
                'prix' => 60,
                'lecons' => [
                    ['titre' => 'Leçon n°1', 'contenu' => 'Les langages HTML et CSS', 'prix' => 32],
                    ['titre' => 'Leçon n°2', 'contenu' => 'Dynamiser votre site avec JavaScript', 'prix' => 32],
                ],
            ],
            [
                'nom' => 'Initation au jardinage',
                'categorie' => 'Jardinage',
                'description' => 'Apprendre le jardinage',
                'prix' => 30,
                'lecons' => [
                    ['titre' => 'Leçon n°1', 'contenu' => 'Les outils du jardinier', 'prix' => 16],
                    ['titre' => 'Leçon n°2', 'contenu' => 'Jardiner avec la lune', 'prix' => 16],
                ],
            ],
            [
                'nom' => 'Initation à la cuisine',
                'categorie' => 'Cuisine',
                'description' => 'Apprendre la cuisine',
                'prix' => 44,
                'lecons' => [
                    ['titre' => 'Leçon n°1', 'contenu' => 'Les modes de cuisson', 'prix' => 23],
                    ['titre' => 'Leçon n°2', 'contenu' => 'Les saveurs', 'prix' => 23],
                ],
            ],
            [
                'nom' => 'Initation au dressage en cuisine',
                'categorie' => 'Cuisine',
                'description' => 'Apprendre le dressage en cuisine',
                'prix' => 48,
                'lecons' => [
                    ['titre' => 'Leçon n°1', 'contenu' => 'Mettre en oeuvre me style dans les assiettes', 'prix' => 26],
                    ['titre' => 'Leçon n°2', 'contenu' => 'Harmoniser un repas à quatre plat', 'prix' => 26],
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
