<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\LivreRepository;

final class CatalogueController extends AbstractController
{
    #[Route('/', name: 'app_catalogue')]
    public function index(LivreRepository $livreRepository): Response
    {
        $genres = $livreRepository->findGenres();
        $auteurs = $livreRepository->findAuteurs();
        // Ci dessous je récupere la liste de tous les livres
        $livres = $livreRepository->findAll();
        // var_dump($livres[3]);
        return $this->render('catalogue/index.html.twig', [
            'livres' => $livres,
            'genres' => $genres,
            'auteurs' => $auteurs
        ]);
    }
}
