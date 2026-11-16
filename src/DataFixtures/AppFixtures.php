<?php

namespace App\DataFixtures;

use App\Entity\Adherent;
use App\Entity\Emprunt;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1. Les livres : [titre, auteur, genre, année, exemplaires]
        $catalogue = [
            ['1984',                             'George Orwell',      'Science-fiction', 1949, 3],
            ['Dune',                             'Frank Herbert',      'Science-fiction', 1965, 2],
            ['Fondation',                        'Isaac Asimov',       'Science-fiction', 1951, 2],
            ['Germinal',                         'Émile Zola',         'Roman',           1885, 3],
            ['L\'Étranger',                      'Albert Camus',       'Roman',           1942, 2],
            ['Le Chien des Baskerville',         'Arthur Conan Doyle', 'Policier',        1902, 2],
            ['Le Comte de Monte-Cristo',         'Alexandre Dumas',    'Aventure',        1844, 2],
            ['Le Crime de l\'Orient-Express',    'Agatha Christie',    'Policier',        1934, 3],
            ['Le Tour du monde en 80 jours',     'Jules Verne',        'Aventure',        1872, 4],
            ['Les Misérables',                   'Victor Hugo',        'Roman',           1862, 2],
            ['Madame Bovary',                    'Gustave Flaubert',   'Roman',           1857, 1],
            ['Vingt mille lieues sous les mers', 'Jules Verne',        'Aventure',        1870, 3],
        ];

        $livres = [];

        foreach ($catalogue as [$titre, $auteur, $genre, $annee, $exemplaires]) {
            $livre = new Livre();
            $livre->setTitre($titre);
            $livre->setAuteur($auteur);
            $livre->setGenre($genre);
            $livre->setAnnee($annee);
            $livre->setExemplaires($exemplaires);
            $livre->setResume($faker->paragraph());
            $livre->setImage('https://placehold.co/400x600?text=' . urlencode($titre));

            $manager->persist($livre);
            $livres[] = $livre;
        }

        // 2. Les adhérents
        $adherents = [];

        for ($i = 0; $i < 20; $i++) {
            $adherent = new Adherent();
            $adherent->setNom($faker->name());
            $adherent->setVille($faker->city());
            $adherent->setEmail($faker->unique()->safeEmail());

            $manager->persist($adherent);
            $adherents[] = $adherent;
        }

        // 3. Les emprunts
        for ($i = 0; $i < 60; $i++) {
            $livre = $faker->randomElement($livres);

            $emprunt = new Emprunt();
            $emprunt->setAdherent($faker->randomElement($adherents));

            // 3 emprunts sur 10 sont en cours, mais seulement s'il reste un exemplaire disponible
            $enCours = !$faker->boolean(70);

            if ($enCours) {
                // Un emprunt en cours a commencé récemment
                $dateEmprunt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-20 days'));
                $emprunt->setDateEmprunt($dateEmprunt);
            } else {
                // Un emprunt rendu a commencé il y a au moins 30 jours, pour que le retour soit dans le passé
                $dateEmprunt = \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', '-30 days'));
                $emprunt->setDateEmprunt($dateEmprunt);
                $emprunt->setDateRetour($dateEmprunt->modify('+' . $faker->numberBetween(7, 30) . ' days'));
            }

            // addEmprunt() ajoute l'emprunt dans la collection du livre ET appelle setLivre()
            $livre->addEmprunt($emprunt);

            $manager->persist($emprunt);
        }

        $manager->flush();
    }
}