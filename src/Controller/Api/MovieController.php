<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * API movies
 */
class MovieController extends AbstractController
{
    /**
     * Read all movies
     *
     * @Route("/api/movies", name="api_movies_read", methods="GET")
     */
    public function read(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        // Le 4ème argument représente le "contexte"
        // qui sera transmis au Serializer
        return $this->json($movies, 200, [], ['groups' => 'movies_read']);
    }

    /**
     * Read one movie
     *
     * @Route("/api/movies/{id<\d+>}", name="api_movies_read_item", methods="GET")
     */
    public function readItem(Movie $movie = null): Response
    {
        
        if ($movie === null) {

            // Optionnel, message pour le front
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Film non trouvé.',
            ];

            // On défini un message custom et un status code HTTP 404
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        return $this->json($movie, 200, [], ['groups' => [
                'movies_read',
                'movies_read_item',
            ]
        ]);
    }

    /**
     * Create movie
     *
     * On a besoin de Request et du Serializer
     *
     * @Route("/api/movies", name="api_movies_create", methods="POST")
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        

        // Récupérer le contenu de la requête, càd le JSON
        $jsonContent = $request->getContent();


        // On désérialise ce JSON en entité Movie, grâce au Serializer
        // On transforme le JSON en objet de type App\Entity\Movie
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-an-object
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');

        // Si objets liés (Genres) ils seront validés si annotation @Valid
        // présente sur la propriété $genres de la classe Movie
        $errors = $validator->validate($movie);

        if (count($errors) > 0) {

        
            // Le tableau des erreurs est retourné sous forme de JSON
            // avec un status 422
            // @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // On sauvegarde le film
        $entityManager->persist($movie);
        $entityManager->flush();

        // On redirige vers movies_read_item
        return $this->redirectToRoute(
            'api_movies_read_item',
            ['id' => $movie->getId()],
          
            Response::HTTP_CREATED
        );
    }

    /**
     * Edit movie (PUT et PATCH)
     * 
     * @Route("/api/movies/{id<\d+>}", name="api_movies_put", methods={"PUT"})
     * @Route("/api/movies/{id<\d+>}", name="api_movies_patch", methods={"PATCH"})
     */
    public function putAndPatch(Movie $movie = null, EntityManagerInterface $em, SerializerInterface $serializer, Request $request, ValidatorInterface $validator)
    {
       

        if ($movie === null) {
            // On retourne un message JSON + un statut 404
            return $this->json(['error' => 'Film non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Notre JSON qui se trouve dans le body
        $jsonContent = $request->getContent();

       
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        $serializer->deserialize(
            $jsonContent,
            Movie::class,
            'json',
            // On a cet argument en plus qui indique au serializer quelle entité existante modifier
            [AbstractNormalizer::OBJECT_TO_POPULATE => $movie]
        );

        // Validation de l'entité désérialisée
        $errors = $validator->validate($movie);
        // Génération des erreurs
        if (count($errors) > 0) {
            // On retourne le tableau d'erreurs en Json au front avec un status code 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // On flush $movie qui a été modifiée par le Serializer
        $em->flush();

     
        return $this->json(['message' => 'Film modifié.'], Response::HTTP_OK);
    }

    /**
     * Delete movie
     * 
     * @Route("/api/movies/{id<\d+>}", name="api_movies_delete", methods="DELETE")
     */
    public function delete(Movie $movie = null, EntityManagerInterface $entityManager)
    {
        // 404
        if ($movie === null) {

            // Optionnel, message pour le front
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Film non trouvé.',
            ];

            // On défini un message custom et un status code HTTP 404
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        // Sinon on supprime en base
        $entityManager->remove($movie);
        $entityManager->flush();

        // L'objet $movie existe toujours en mémoire PHP jusqu'à la fin du script
        return $this->json(
            ['message' => 'Le film ' . $movie->getTitle() . ' a été supprimé !'],
            Response::HTTP_OK);
    }
}
