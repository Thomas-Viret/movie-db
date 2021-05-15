<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Job;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Review;
use App\Entity\Casting;
use App\Entity\Department;
use App\Service\MySlugger;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\MovieDbProvider;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Classe de Fixture
 * à exécuter avec la commande
 * `bin/console doctrine:fixtures:load`
 */
class AppFixtures extends Fixture
{
    private $slugger;

    // Password encoder
    private $passwordEncoder;

    // Connection à MySQL (DBAL => PDO)
    private $dbalConnexion;

    /**
     * On injecte les dépendances (les objets utiles au fonctionnement de nos Fixtures) dans le constructeur car AppFixtures est elle aussi un service
     */
    public function __construct(MySlugger $slugger, UserPasswordEncoderInterface $passwordEncoder, Connection $connection)
    {
        $this->slugger = $slugger;
        $this->passwordEncoder = $passwordEncoder;
        $this->connection = $connection;
    }

    // On règle nos valeurs ici
    const NB_GENRES = 20;
    const NB_MOVIES = 10;
    // On veut par ex. 5 reviews par film
    const NB_REVIEWS = 5 * self::NB_MOVIES;
    // On veut par ex. 8 casting par film
    const NB_CASTINGS = 8 * self::NB_MOVIES;
    // On veut par ex. le double de castings
    const NB_PERSONS = 2 * self::NB_CASTINGS;
    const NB_JOBS = 2 * self::NB_MOVIES;
    const NB_DEPARTMENTS= self::NB_MOVIES;

    private function truncate()
    {
        // On passen mode SQL ! On cause avec MySQL
        // Désactivation des contraintes FK
        $users = $this->connection->query('SET foreign_key_checks = 0');
        // On tronque
        $users = $this->connection->query('TRUNCATE TABLE casting');
        $users = $this->connection->query('TRUNCATE TABLE department');
        $users = $this->connection->query('TRUNCATE TABLE genre');
        $users = $this->connection->query('TRUNCATE TABLE job');
        $users = $this->connection->query('TRUNCATE TABLE movie');
        $users = $this->connection->query('TRUNCATE TABLE movie_genre');
        $users = $this->connection->query('TRUNCATE TABLE person');
        $users = $this->connection->query('TRUNCATE TABLE review');
        $users = $this->connection->query('TRUNCATE TABLE team');
        $users = $this->connection->query('TRUNCATE TABLE user');
        // etc.
    }



    public function load(ObjectManager $manager)
    {

        // On va truncate nos tables à la main pour revenir à id=1
        $this->truncate();


        $faker = Factory::create('fr_FR');
        $faker->seed(2021);
        


        // Instanciation du Provider
        // $movieDbProvider = new MovieDbProvider();
        // Fourniture de notre Provider à Faker
        $faker->addProvider(new MovieDbProvider());


        // Utilisateurs
        $user = new User();
        $user->setEmail('user@user.com');
        // user
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'user'
            ));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        // admin
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'admin'
            ));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // MANAGER
        $userManager = new User();
        $userManager->setEmail('manager@manager.com');
        // manager
        $userManager->setPassword($this->passwordEncoder->encodePassword(
            $userManager,
            'manager'
            ));
        $userManager->setRoles(['ROLE_MANAGER']);
        $manager->persist($userManager);


        // PERSONS

        // et y accéder depuis la création des castings
        $personsList = [];
        for ($i=1; $i <= self::NB_PERSONS; $i++) {
            
            $person = new Person();
            $person->setFirstname($faker->unique()->firstName());
            $person->setLastname($faker->unique()->lastName());
            $person->setCreatedAt(new \DateTime());
            // On stocke la personne pour usage ultérieur
            $personsList[] = $person;

            $manager->persist($person);
        }


        // Genres
        // Un taleau pour stocker nos genres
        $genresList = [];
    
       
        for ($i=1; $i <= self::NB_GENRES; $i++) {
            // Un genre
            $genre = new Genre();
            //$genre->setName( $faker->unique()->word());
            $genre->setName($faker->unique()->movieGenre());

            // On ajoute le genre à la liste
            $genresList[] = $genre;

            $manager->persist($genre);
        }$person->setFirstname($faker->unique()->firstName());
        $person->setLastname($faker->unique()->lastName());



         // DEPARTMENTS

        // et y accéder depuis la création des jobs
        $departmentsList = [];
        for ($i=1; $i <= self::NB_DEPARTMENTS; $i++) {
            
            $department = new Department();
            $department->setName($faker->unique()->movieDepartmentName());
            $department->setCreatedAt(new \DateTime());
            // On stocke la personne pour usage ultérieur
            $departmentsList[] = $department;

            

            $manager->persist($department);
        }


          // JOBS

        // et y accéder depuis la création des jobs
        $jobsList = [];
        for ($i=1; $i <= self::NB_JOBS; $i++) {
            
            $job = new Job();
            $job->setName($faker->unique()->movieJobName());
            $job->setCreatedAt(new \DateTime());
            // On stocke la personne pour usage ultérieur
            $jobsList[] = $job;

            $randomDepartment = $departmentsList[mt_rand(0, count($departmentsList) - 1)];
            $job->setDepartment($randomDepartment);

            $manager->persist($job);
        }


        // Movies
        
        // On crée ce tableau pour associer les films aux Reviews
        $moviesList = [];
        
        for ($i=1; $i <= self::NB_MOVIES; $i++) { 
            

            // Un film
            $movie = new Movie();
            //$movie->setTitle($faker->unique()->state());
            $movie->setTitle($faker->unique()->movieTitle());
            // Génère un timestamp aléatoire de 1926 à maintenant
            $movie->setReleaseDate($faker->dateTimeBetween('-100 years'));
            $movie->setCreatedAt(new \DateTime());

            // On associe de 1 à 3 genres au hasard
            // /!\ Attention ici on ne gère pas l'unicité,
            // on peut avoir plusieurs fois le même genre...
            // mais notre Entité gère ce doublon dans la méthode addGenre()
            shuffle($genresList);
            
            for ($r = 0; $r < mt_rand(1, 3); $r++) {
                // On va chercher l'index $r dans le tableau mélangé
                // => l'unicité est garanti
                $randomGenre = $genresList[$r];
                $movie->addGenre($randomGenre);
            }


            //slug
            // $movie->setSlug($this->slugger->toSlug($movie->getTitle()));
            // => cela a été déplacé dans le Listener

            $moviesList[] = $movie;
            // On le persist
            $manager->persist($movie);
        }


        // Les reviews
        for ($i = 1; $i <= self::NB_REVIEWS; $i++) {
            $review = new Review();
            $review->setContent($faker->paragraph());
            $review->setPublishedAt(new \DateTime());

            // On va chercher un film au hasard dans la liste des films créée au-dessus
            // Variante avec mt_rand et count
            $randomMovie = $moviesList[mt_rand(0, count($moviesList) - 1)];
            $review->setMovie($randomMovie);

            // On persist
            $manager->persist($review);
        }

        // Les castings
        for ($i = 1; $i < self::NB_CASTINGS; $i++) {
            $casting = new Casting();
            $casting->setRole($faker->unique()->jobTitle());
            // $casting->setCreditOrder(mt_rand(1, 100));
            $casting->setCreditOrder($faker->numberBetween(1, 10));

            // On va chercher un film au hasard dans la liste des films créée au-dessus
            // Variante avec mt_rand et count
            $randomMovie = $moviesList[mt_rand(0, count($moviesList) - 1)];
            $casting->setMovie($randomMovie);

            // On va chercher une personne au hasard dans la liste des personnes créée au-dessus
            // Variante avec array_rand()
            $randomPerson = $personsList[array_rand($personsList)];
            $casting->setPerson($randomPerson);

            // On persist
            $manager->persist($casting);
        }


        // On flush
        $manager->flush();
    }

    
}
