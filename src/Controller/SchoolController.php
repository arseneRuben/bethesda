<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\SubscriptionRepository;

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
    private $subRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo, SchoolYearRepository $scRepo, ClassRoomRepository $rmRepo, SubscriptionRepository $subRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->scRepo = $scRepo;
        $this->rmRepo = $rmRepo;
        $this->subRepo = $subRepo;
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $rooms = $this->rmRepo->findBy(array("apc" => true), array("level" => "ASC"));
        $year = $this->scRepo->findOneBy(array("activated" => true));

        $results = [];

        foreach ($rooms as $room) {

            $officialExamResults = $this->subRepo->countByMention($year, $room);
            $mentionCategories = [];
            $mentionCountCategories = [];
            foreach ($officialExamResults as $exam) {
                switch ($exam["officialExamResult"]) {
                    case  "0":
                        $mentionCategories[] = "ECHEC";
                        break;
                    case  "1p":
                        $mentionCategories[] = "SUCCESS";
                        break;
                    case  "1a":
                        $mentionCategories[] = "ASSEZ-BIEN";
                        break;
                    case  "1b":
                        $mentionCategories[] = "BIEN";
                        break;
                    case  "1t":
                        $mentionCategories[] = "TRES-BIEN";
                        break;
                    case  "1e":
                        $mentionCategories[] = "EXCELLENT";
                        break;
                    case  "A":
                        $mentionCategories[] = "5 POINTS";
                        break;
                    case  "B":
                        $mentionCategories[] = "4 POINTS";
                        break;
                    case  "C":
                        $mentionCategories[] = "3 POINTS";
                        break;
                    case  "D":
                        $mentionCategories[] = "2 POINTS";
                        break;
                    case  "E":
                        $mentionCategories[] = "1 POINT";
                        break;
                }
                $mentionCountCategories[] = $exam["count"];
            }
            $couple["mentionCategories"] = $mentionCategories;
            $couple["mentionCountCategories"] = $mentionCountCategories;
            $results[str_replace(' ', '', strtolower($room->getName()))] = json_encode($couple);
        }

        // dd($results);

        return $this->render('school/index.html.twig', compact("results"));
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

        //phpinfo();
        $rooms = $this->rmRepo->findAll();

        return $this->render('school/roomList.html.twig', compact("rooms"));
    }


    /**
     * @Route("/staff", name="app_staff")
     */
    public function staffAction(Request $request)
    {

        $qb = $this->em->createQueryBuilder();
        $qb->select('u')->from('App:User', 'u')->where('u.roles LIKE :roles')->setParameter('roles', '%"' . "ROLE_ADMIN" . '"%');
        $users = $qb->getQuery()->getResult();
        //$users = $this->userRepo->findByRoles("ROLE_ADMIN");
        return $this->render('school/staff.html.twig', compact("users"));
    }
}
