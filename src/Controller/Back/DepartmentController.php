<?php

namespace App\Controller\Back;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DepartmentController extends AbstractController
{
       /**
     * @Route("/back/department/browse", name="department_browse", methods={"GET"})
     */
    public function browse(DepartmentRepository $departmentRepository): Response
    {


       
        $departments = $departmentRepository->findAll();

       
        return $this->render('back/department/browse.html.twig', [
            'departments' => $departments,
        ]);
    }

     /**
     * 
     * 
     * @Route("/back/department/read/{id<\d+>}", name="department_read", methods={"GET"})
     */
    public function read(Department $department = null): Response
    {
       
        
         if ($department === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        return $this->render('back/department/department_read.html.twig', [
            'department' => $department,
            
        ]);
    }
}
