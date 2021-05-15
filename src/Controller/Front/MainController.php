<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Form\ReviewType;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Page d'accueil
     * 
     * Le paramètre de recherche est dans le paramètre GET ?search=xxx
     * 
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(MovieRepository $movieRepository, Request $request, GenreRepository $genreRepository
    ): Response
    {
        // Le paramètre GET à récupérer
        $search = $request->query->get('search');
        // Tous les films par ordre alphabétique
        // $movies = $movieRepository->findBy([], ['title' => 'ASC']);
        $movies = $movieRepository->findAllOrderedByTitleAsc($search);
        // Tous les genres
        $genres = $genreRepository->findBy([], ['name' => 'ASC']);


        return $this->render('front/main/home.html.twig', [
            'movies' => $movies,
            'genres' => $genres,

        ]);
    }

     /**
     * Page d'un film
     * 
     * @Route("/movie/{slug<[\w\d\-]+>}", name="movie_show",methods={"GET"})
     */
    public function movieShow(Movie $movie = null, CastingRepository $castingRepository): Response
    {

      
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }
       

        $castings = $castingRepository->findOneByMovieJoinedToPersonDQL($movie);
  

        return $this->render('front/main/movie_show.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
        ]);
    }

   /**
     * Affiche formulaire ajout critique de film
     * 
     * @todo Lier la critique à un film donné
     * 
     * @Route("/review/add", name="review_add", methods={"GET", "POST"})
     */
    public function reviewAdd(Request $request): Response
    {
        // On génère le form
        // On lui transmet une valeur par défaut, par ex. la date pour le champ date
        $form = $this->createForm(ReviewType::class, ['date' => new \DateTime()]);

        // On inspecte la requête
        // et on mappe les infos postées dans le form
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données du form
            $loginData = $form->getData();

            // Fait quelque chose ...
            dd($loginData);

            // On redirige vers...
            // return $this->redirectToRoute(...)
        }
        
        return $this->render('front/main/review_add.html.twig', [
            // On envoie au template "une vue de formulaire" via createView()
            'form' => $form->createView(),
        ]);
    }

}
