<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Studentme controller.
 *
 * @Route("/admin/students")
 */
class StudentController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;


    public function __construct(EntityManagerInterface $em, StudentRepository $repo, SchoolYearRepository $scRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
    }

     /**
     * Lists all Studentme entities.
     *
     * @Route("/", name="admin_students")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
       // $year = $this->scRepo->findOneBy(array("activated" => true));
        $students = $this->repo->findEnrolledStudentsThisYear2();
        
       return $this->render('student/list.html.twig', compact("students"));
    }

    /**
     * Finds and displays a Studentme entity.
     *
     * @Route("/{id}/show", name="admin_students_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Student $student)
    {
        $filename = "assets/images/student/".$student->getImageName();
        $file_exists = file_exists($filename);
        return $this->render('student/show.html.twig', [
            'student'=>$student,
            'file_exists'=>$file_exists
        ]);
    }

  /**
     * @Route("/create",name= "admin_students_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $student = new Student();
    	$form = $this->createForm(StudentType::class, $student);

        $numero = $this->repo->getNumeroDispo();
        $student->setMatricule($numero);

    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($student);
            $this->em->flush();
            $this->addFlash('success', 'Student succesfully created');
            return $this->redirectToRoute('admin_students');
    	}
    	 return $this->render('student/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Studentme entity.
     *
     * @Route("/{id}/edit", name="admin_students_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Student $student): Response
    {
        $form = $this->createForm(StudentType::class, $student, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Student succesfully updated');
            return $this->redirectToRoute('admin_students');
        }
        return $this->render('student/edit.html.twig'	, [
            'student'=>$student,
            'form'=>$form->createView()
        ]);
    }

    

    /**
     * Deletes a Studentme entity.
     *
     * @Route("/{id}/delete", name="admin_students_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Student $student, Request $request):Response
    {
        if($this->isCsrfTokenValid('students_deletion'.$student->getId(), $request->request->get('csrf_token') )){
            $this->em->remove($student);
           
            $this->em->flush();
            $this->addFlash('info', 'Student succesfully deleted');
       }
       
        return $this->redirectToRoute('admin_students');
    }

}
