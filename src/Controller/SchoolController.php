<?php

namespace App\Controller;

use App\Entity\ClassRoom;
use App\Repository\UserRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\SubscriptionRepository;
use App\Service\OfficialExamService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SchoolYearService;
use App\Repository\MainTeacherRepository;


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
    private SchoolYearService $schoolYearService;
    private MainTeacherRepository $mainTeacherRepo;

    public function __construct(MainTeacherRepository $mainTeacherRepo,SchoolYearService $schoolYearService,EntityManagerInterface $em, UserRepository $userRepo, SchoolYearRepository $scRepo, ClassRoomRepository $rmRepo, SubscriptionRepository $subRepo)
    {
        $this->em = $em;
        $this->userRepo = $userRepo;
        $this->scRepo = $scRepo;
        $this->rmRepo = $rmRepo;
        $this->subRepo = $subRepo;
        $this->schoolYearService = $schoolYearService;
        $this->mainTeacherRepo = $mainTeacherRepo;
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        $rooms = $this->rmRepo->findBy(array("apc" => true), array("level" => "ASC"));
        $year_before = $this->scRepo->findOneBy(array("activated" => true));
        $year = $this->scRepo->findOneBy(array("id" => $year_before->getId()));
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
     * HELP.
     *
     * @Route("/help", name="app_help")
     * @Method("GET")
     * @Template()
     */
    public function helpAction()
    {
        return $this->render('school/help.html.twig');
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
    public function roomListAction(PaginatorInterface $paginator,  Request $request)
    {
        $year_before = $this->scRepo->findOneBy(array("activated" => true));
        $year = $this->scRepo->findOneBy(array("id" => $year_before->getId() - 1));
        $mainTeachers =  $this->mainTeacherRepo->findBy(array("schoolYear" => $year));
        $mainTeachersMap = array();
        foreach($mainTeachers as $mt){
            $mainTeachersMap[$mt->getClassRoom()->getId()] = $mt->getTeacher();
        }
        $entities = $this->rmRepo->findAll();
      
        $subscriptions = $this->subRepo->findEnrollementThisYear($year);
        $rooms = $paginator->paginate($entities, $request->query->get('page', 1), ClassRoom::NUM_ITEMS_PER_PAGE);
        $rooms->setCustomParameters([
            'position' => 'centered',
            'size' => 'large',
            'rounded' => true,
        ]);
        return $this->render('school/roomList.html.twig', compact("rooms", "year", "subscriptions", "mainTeachersMap"));
    }

    /**
     * Finds and displays a Section entity.
     *
     * @Route("/{roomId}/exam", name="official_exam", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function callOffialExam(int $roomId, OfficialExamService $officialExamService)
    {
        $rate = $officialExamService->successRate($roomId);
        $subscriptions = $officialExamService->subscriptions($roomId);
        return $this->render('school/roomList.html.twig', [
            'rate' => $rate,
            'subscriptions' => $subscriptions
        ]);
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

      /**
     * @Route("/update_school_year", name="update_school_year", methods={"POST"})
     */
    public function updateSessionValue(Request $request)
    {
        $selectedSchoolYear = $request->request->get('selectedSchoolYear');
        // Update session with the selected value
        $session = $request->getSession();
        $session->set('session_school_year', $selectedSchoolYear);
        return new Response('Session updated', 200);
    }
}
