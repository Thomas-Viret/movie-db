<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use App\Service\MySlugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MovieController extends AbstractController
{

    /**
     * @Route("/back/movie/home", name="back_home", methods={"GET"})
     */
    public function home(): Response
    {
        return $this->render('back/movie/home.html.twig');
    }


    /**
     * @Route("/back/movie/browse", name="movie_browse", methods={"GET"})
     */
    public function browse(MovieRepository $movieRepository): Response
    {


       
        $movies = $movieRepository->findAllOrderedByTitleAsc();

       
        return $this->render('back/movie/browse.html.twig', [
            'movies' => $movies,
        ]);
    }

     /**
     * Page d'un film
     * 
     * @Route("/back/movie/read/{id<\d+>}", name="movie_read", methods={"GET"})
     */
    public function read(Movie $movie = null, CastingRepository $castingRepository): Response
    {

         // 404 ?
         if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        // On peut également récupérer les castings depuis le contrôleur
        // plutôt que de laisser Doctrine le faire depuis Twig
        // $castings = $castingRepository->findBy(['movie' => $movie], ['creditOrder' => 'ASC']);
       

        $castings = $castingRepository->findOneByMovieJoinedToPersonDQL($movie);
        //dd($castings);

        return $this->render('back/movie/movie_read.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
        ]);
    }

     /**
     * Ajout d'un film
     * 
     *
     * @Route("/back/movie/add", name="movie_add", methods={"GET", "POST"})
     */
    public function add(Request $request, EntityManagerInterface $entityManager, MySlugger $slugger): Response
    {
       
        $movie = new Movie();

        // On crée le form en lui donnant l'entité à laquelle il est associé
        $form = $this->createForm(MovieType::class, $movie);

        // On inspecte la requête, on soumet les données au form et à l'entité contenu dans le form
        $form->handleRequest($request);

        // Traitement du form
        if ($form->isSubmitted() && $form->isValid()) {

            // A ce stade, le $post d'origine est modifié
            // On pourrait modifier une propriété de l'entité
            // par ex. encoder un mot de passe
             // On récupère les données du form
            //  $reviewData = $form->getData();
            // dd($reviewData);

            //$movie->setSlug($slugger->toSlug($movie->getTitle()));
             // On slugifie le titre
            // => cela a été déplacé dans le Listener
            
            // On demande au Manager de sauvegarder l'entité
            //$entityManager = $this->getDoctrine()->getManager();// déjà mis en paramètre
            $entityManager->persist($movie);
            $entityManager->flush();

            // On redirige vers la liste
            return $this->redirectToRoute('movie_browse');
        }
        
        // Sinon on affiche le formulaire d'ajout
        return $this->render('back/movie/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


      /**
     * @Route("/back/movie/edit/{id}", name="movie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Movie $movie, MySlugger $slugger): Response
    {
        

        $form = $this->createForm(MovieType::class, $movie);
        // Le mot de passe du $user va être écrasé par $request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            
            //$movie->setSlug($slugger->toSlug($movie->getTitle()));
            // => cela a été déplacé dans le Listener

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('movie_browse');
        }

        return $this->render('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

     /**
     * Supprimer un film (méthode HTTP DELETE !)
     * 
     * 
     * 
     * @Route("/back/movie/delete/{id<\d+>}", name="movie_delete", methods={"DELETE"})
     */
    public function delete(Movie $movie = null, Request $request, EntityManagerInterface $entityManager)
    {
        // 404 ?
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        // @see https://symfony.com/doc/current/security/csrf.html#generating-and-checking-csrf-tokens-manually
        // On réupère le nom du token qu'on a déposé dans le form
        $submittedToken = $request->request->get('token');

        // 'delete-movie' is the same value used in the template to generate the token
        if (! $this->isCsrfTokenValid('delete-movie', $submittedToken)) {
            // On jette une 403
            throw $this->createAccessDeniedException('Are you token to me !??!??');
        }

        // Sinon on supprime
        $entityManager->remove($movie);
        $entityManager->flush();

        return $this->redirectToRoute('movie_browse');
    }
}
