<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LivreRepository;
use App\Entity\Livre;
use App\Repository\EmpruntRepository;

final class CatalogueController extends AbstractController
{
    #[Route('/', name: 'app_catalogue')]
    public function index(Request $request, LivreRepository $livreRepository): Response
    {
        $genre = $request->query->get('genre');
        var_dump($genre);
        $auteur = $request->query->get('auteur');
        var_dump($auteur);
        $tri = $request->query->get('tri');
        var_dump($auteur);


        $genres = $livreRepository->findGenres();
        $auteurs = $livreRepository->findAuteurs();
        // Ci dessous je récupere la liste de tous les livres
        $livres = $livreRepository->findCatalogue($genre, $auteur, $tri);
        // var_dump($livres[3]);
        return $this->render('catalogue/index.html.twig', [
            'livres' => $livres,
            'genres' => $genres,
            'auteurs' => $auteurs
        ]);
    }

    #[Route('/livre/{id}', name:'page_detail')]
    public function pageDetail(Livre $livre, EmpruntRepository $empruntRepository, LivreRepository $livreRepository): Response
    {
        // Ci dessous je récupere les derniers emprunts du livre passé en paramètre
        $derniersEmprunts = $empruntRepository->findDerniersPourLivre($livre);
        $memeGenres = $livreRepository->findMemeGenre($livre, 5);
        return $this->render('catalogue/detail.html.twig', [
            'livre' => $livre,
            'derniersEmprunts' => $derniersEmprunts,
            'memeGenres' => $memeGenres
        ]);
    }


}
