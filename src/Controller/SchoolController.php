<?php

namespace App\Controller;
use App\Repository\UserRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * User controller.
 *
 * @Route("/")
 */

class SchoolController extends AbstractController
{

    private $em;
    private $userRepo;
    private $rmRepo;
    private $scRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, SchoolYearRepository $scRepo, ClassRoomRepository $rmRepo)
    {
        $this->em = $em;
        $this->userRepo= $userRepo;
        $this->scRepo = $scRepo;
        $this->rmRepo = $rmRepo;
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('school/index.html.twig');
    }


     /**
     * Lists all User entities.
     *
     * @Route("/teachers", name="app_teachers")
     * @Method("GET")
     * @Template()
     */
    public function teacherListAction()
    {
        
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $users = $this->userRepo->findAllOfCurrentYear($year);
        
        return $this->render('school/teacher.html.twig', compact("users"));
    }


     /**
     * Lists all User entities.
     *
     * @Route("/rooms", name="app_rooms")
     * @Method("GET")
     * @Template()
     */
    public function roomListAction()
    {
        
       
        $rooms = $this->rmRepo->findAll();
        
        return $this->render('school/roomList.html.twig', compact("rooms"));
    }


     /**
     * @Route("/admin", name="dashboard")
     */
    /*public function adminAction(Request $request)
    {
      
        $effectif = $this->container->get('app_service.stat');
        $em = $this->getDoctrine()->getManager();
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
       
        return $this->render('default/index.html.twig', array(
            'year' => $year,
            
        ));
    }*/
}
