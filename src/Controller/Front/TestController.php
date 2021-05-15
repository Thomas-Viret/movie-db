<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Entity\Review;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    /**
     * @Route("/test/movie/list", name="test_movie_list")
     */
    public function movieList()
    {
        // 1. On accède au Repository de l'entité Movie
        // Rappel FQCN = 'App\Entity\Movie' = Movie::class
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);

        // 2. On fait appel à la méthode findAll() du Repository
        $movies = $movieRepository->findAll();
        dump($movies);

        return $this->render('Front/test/list.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/test/movie/add", name="test_movie_add")
     */
    public function movieAdd(): Response
    {
        // 1. Crée une nouvelle entité
        $avatar = new Movie();
        $avatar->setTitle('clone');
        $avatar->setReleaseDate(new \DateTime('2012-12-20'));
        $avatar->setCreatedAt(new \DateTime());
        dump($avatar);

        // Ajoutons une Review à ce film
        $review = new Review();
        $review->setContent('Ce film est super !');
        $review->setPublishedAt(new \DateTime());
        // Associons le film à la review
        $review->setMovie($avatar);



        // 2. On fait appel au Manager d'entités de Doctrine
        $entityManager = $this->getDoctrine()->getManager();

        // 3. Demander au Manager de se préparer à ajouter notre objet en BDD
        $entityManager->persist($avatar);
        // On persiste aussi la review
        $entityManager->persist($review);

        // 4. Demande au Manager de sauvegarder l'objet en base
        // => requête SQL exécutée
        $entityManager->flush();

        return new Response('Film ajouté.</body>');
    }

    /**
     * @Route("/test/movie/edit/{id}", name="test_movie_edit")
     */
    public function editMovie($id)
    {
        // 1. On accède au Repository de l'entité Movie
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);

        // 2. On va chercher le film demandé
        $movie = $movieRepository->find($id);

        // @todo : 404 ?

        // 3. On modifie l'objet
        $movie->setUpdatedAt(new \DateTime());

        // 4. On fait appel au Manager pour mettre à jour
        // /!\ Pas besoin de ->persist($movie) puisque $movie existe déjà en database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('test_movie_list');
    }
}
