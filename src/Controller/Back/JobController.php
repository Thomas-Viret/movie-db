<?php

namespace App\Controller\Back;

use App\Entity\Job;
use App\Repository\JobRepository;
use App\Repository\DepartmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JobController extends AbstractController
{
    

     /**
     * @Route("/back/job/browse", name="job_browse", methods={"GET"})
     */
    public function browse(JobRepository $jobRepository): Response
    {


       
        $jobs = $jobRepository->findAll();

       
        return $this->render('back/job/browse.html.twig', [
            'jobs' => $jobs,
        ]);
    }

     /**
     * 
     * 
     * @Route("/back/job/read/{id<\d+>}", name="job_read", methods={"GET"})
     */
    public function read(Job $job = null, DepartmentRepository $departmentRepository): Response
    {

         // 404 ?
         if ($job === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }
        // On peut également récupérer les castings depuis le contrôleur
        // plutôt que de laisser Doctrine le faire depuis Twig
        // $castings = $castingRepository->findBy(['movie' => $movie], ['creditOrder' => 'ASC']);
       

        // $departments = $departmentRepository->findOneByMovieJoinedToPersonDQL($job);
        //dd($castings);

        return $this->render('back/job/job_read.html.twig', [
            'job' => $job,
            //'deparments' => $departments,
        ]);
    }
}
