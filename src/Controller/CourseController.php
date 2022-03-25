<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\ClassRoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
/**
 * Course controller.
 *
 * @Route("/prof/courses")
 */
class CourseController extends AbstractController
{
    private $em;
    private $repo;
    private $clRepo;

    public function __construct(EntityManagerInterface $em, CourseRepository $repo, ClassRoomRepository $clRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->clRepo = $clRepo;
    }
    /**
     * Lists all Course entities.
     *
     * @Route("/", name="admin_courses")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
       
        $rooms = $this->clRepo->findAll();
        
       return $this->render('course/index.html.twig', compact("rooms"));
    }

    /**
     * Finds and displays a Course entity.
     *
     * @Route("/{id}/show", name="admin_courses_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Course $course)
    {
        return $this->render('course/show.html.twig', compact("course"));
    }

   
    /**
     * @Route("/create",name="admin_courses_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $course = new Course();
    	$form = $this->createForm(CourseType::class, $course);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($course);
            $this->em->flush();
            $this->addFlash('success', 'Course succesfully created');
            return $this->redirectToRoute('admin_courses');
    	}
    	 return $this->render('course/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

    

    /**
     * Creates a new Course entity.
     *
     * @Route("/create", name="admin_courses_create")
     * @Method("POST")
    
     */
    public function createAction(Request $request)
    {
        $course = new Course();
        $form = $this->createForm(new CourseType(), $course);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_courses_show', array('id' => $course->getId())));
        }

        return array(
            'course' => $course,
            'form'   => $form->createView(),
        );
    }

     /**
     * Displays a form to edit an existing Course entity.
     *
     * @Route("/{id}/edit", name="admin_courses_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Course $course): Response
    {
        $form = $this->createForm(CourseType::class, $course, [
            'method'=> 'PUT'
        ]);

        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Course succesfully updated');
            return $this->redirectToRoute('admin_courses');
        }
        return $this->render('course/edit.html.twig'	, [
            'course'=>$course,
            'form'=>$form->createView()
        ]);
    }



   /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_courses_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Course $course, Request $request):Response
    {
	   if($this->isCsrfTokenValid('courses_deletion'.$course->getId(), $request->request->get('csrf_token') )){
            $this->em->remove($course);
           
            $this->em->flush();
            $this->addFlash('info', 'Course succesfully deleted');
	   }
       
        return $this->redirectToRoute('admin_courses');
    }

}
