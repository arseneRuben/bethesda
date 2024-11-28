<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Evaluation;
use App\Filter\EvaluationSearch;
use App\Form\EvaluationType;
use App\Form\Filter\EvaluationSearchType;
use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use App\Repository\StudentRepository;
use App\Repository\AttributionRepository;
use App\Repository\SequenceRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\EvaluationRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\SchoolYearService;


/**
 * Evaluationme controller.
 *
 * @Route("/evaluations")
 */
class EvaluationController extends AbstractController
{
    private $em;
    private EvaluationRepository $repo;
    private UserRepository $userRepo;
    private $scRepo;
    private StudentRepository $stdRepo;
    private $clRepo;
    private CourseRepository $crsRepo;
    private $seqRepo;
    private AttributionRepository $attrRepo;
    private  $notes ; 
    private MarkRepository $markRepo;
    private SchoolYearService $schoolYearService;


    public function __construct(
        UserRepository $userRepo,
        SchoolYearService $schoolYearService,
        EntityManagerInterface $em,
        EvaluationRepository $repo,
        StudentRepository $stdRepo,
        CourseRepository $crsRepo,
        SchoolYearRepository $scRepo,
        ClassRoomRepository $clRepo,
        SequenceRepository $seqRepo,
        AttributionRepository $attrRepo,
        MarkRepository $markRepo
    ) {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->stdRepo = $stdRepo;
        $this->notes = array();
        $this->clRepo = $clRepo;
        $this->crsRepo = $crsRepo;
        $this->seqRepo = $seqRepo;
        $this->schoolYearService = $schoolYearService;
        $this->markRepo = $markRepo;
        $this->attrRepo = $attrRepo;
        $this->userRepo = $userRepo;

    }



    /**
     * Lists all Evaluationme entities.
     *
     * @Route("/", name="admin_evaluations")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(PaginatorInterface $paginator, Request $request, SessionInterface $session)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }

        $search = new EvaluationSearch();
        $searchForm =  $this->createForm(EvaluationSearchType::class, $search);
        $year = $this->schoolYearService->sessionYearById();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $room = $this->clRepo->findOneBy(array("id" => $_GET['room']));
            $sequence = $this->seqRepo->findOneBy(array("id" => $_GET['sequence']));
            $course = $this->crsRepo->findOneBy(array("id" => $_GET['course']));
            $entities = $this->repo->findEvaluations($year->getId(), $room, $sequence, $course);
        } else {
           
            $entities = $this->repo->findAnnualEvaluations($year->getId());
        }
        $evaluations = $paginator->paginate($entities, $request->query->get('page', 1), Evaluation::NUM_ITEMS_PER_PAGE);
        $evaluations->setCustomParameters([
            'position' => 'centered',
            'size' => 'large',
            'rounded' => true,
        ]);
        return $this->render('evaluation/index.html.twig', ['pagination' => $evaluations, 'searchForm' => $searchForm->createView()]);
    }

    /**
     * Finds and displays a Evaluationme entity.
     *
     * @Route("/{id}/show", name="admin_evaluations_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Evaluation $evaluation, SessionInterface $session)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $year = $this->schoolYearService->sessionYearById();
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($evaluation->getClassRoom(), $year);
        return $this->render('evaluation/show.html.twig', ['studentEnrolled' => $studentsEnrolledInClass, 'evaluation' => $evaluation]);
    }



    /**
     * @Route("/new",name= "admin_evaluations_new", methods={"GET"})
     */
    public function new(Request $request, SessionInterface $session): Response
    {
        
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $year = $this->schoolYearService->sessionYearById();

        
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);

        return $this->render('evaluation/new.html.twig', array(
            'evaluation' => $evaluation,
            'response' => null,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Evaluation entity.
     *
     * @Route("/create", name="admin_evaluations_create")
     * @Method({"POST"})
     * @Template()
     */
    public function create(Request $request, SessionInterface $session)
    {

        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $evaluation = new Evaluation();

        if ($content = $request->getContent()) {
            $marks = json_decode($_POST['marks'], true);
            
            $notes = array();
            $effectif = 0;
            $total = 0;
            $pos = 0;
            $room = $request->request->get('idroom');
            $idcourse = $request->request->get('idcourse');
            $idsequence = $request->request->get('idsequence');
            $competence = $request->request->get('competence');
            $year = $this->schoolYearService->sessionYearById();
            $classRoom = $this->clRepo->findOneBy(array("id" => $room));
            $course = $this->crsRepo->findOneBy(array("id" => $idcourse));
            $sequence = $this->seqRepo->findOneBy(array("id" => $idsequence));
            if($sequence == null)
            {
                $sequence = $this->seqRepo->findOneBy(array("activated" => true));
            }
            $evaluation->setCourse($course);
            $evaluation->setAuthor($this->getUser());
            $evaluation->setClassRoom($classRoom);
            $evaluation->setSequence($sequence);
            $evaluation->setCompetence($competence);
            foreach ($marks as $record) {
                $mark = new Mark();
                $matricule = $record["matricule"];
                $note = $record["note"];
                $poids = $record["weight"];
                $appreciation = $record["appreciation"];
                $student = $this->stdRepo->findOneByMatricule($matricule);
                if (strcmp($student->getGender(), "M") == 0) {
                    if ($note < 10) {
                        $evaluation->addFailluresH();
                    } else {
                        $evaluation->addSuccessH();
                    }
                } else {
                    if ($note < 10) {
                        $evaluation->addFailluresf();
                    } else {
                        $evaluation->addSuccessF();
                    }
                }
                if ($poids == 0) {
                    $evaluation->addAbscent();
                } else {
                    $effectif++;
                    $total += $note;
                }
                $mark->setValue($note);
                $mark->setWeight($poids);
                $mark->setAppreciation($appreciation);
                $mark->setEvaluation($evaluation);
                $mark->setStudent($student);
                $notes[$pos++] = $mark; // Construction d'un arrayList pour trie
                $this->em->persist($mark);
                $evaluation->addMark($mark);
            }
            // analysons si l'utilisateur est autorise a enregistrer les notes sur la matiere
            
            // disposition des rang dans les notes
            usort($notes, function ($a, $b) {
                if ($a->getValue() == $b->getValue()) {
                    return 0;
                }
                return ($a->getValue() < $b->getValue()) ? -1 : 1;
            });
            $evaluation->setMini($notes[0]->getValue());
            $evaluation->setMaxi($notes[$effectif-1]->getValue());
            foreach ($notes as $mark) {
                $mark->setRank2($pos);
                $pos--;
            }
            if ($effectif != 0) {
                $evaluation->setMoyenne($total / $effectif);
            } else {
                $evaluation->setMoyenne(0);
            }
            $this->em->persist($evaluation);

            $this->em->flush();

        }
        return $this->redirect($this->generateUrl('admin_evaluations_new'));
    }



    /**
     * Displays a form to edit an existing Evaluationme entity.
     *
     * @Route("/{id}/edit", name="admin_evaluations_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Evaluation $evaluation, SessionInterface $session): Response
    {
         if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        /* if(($evaluation->getTeacher()!=$this->getUser()) && !($this->get('security.context')->isGranted('ROLE_ADMIN')))
        {
            $this->addFlash('warning', 'Access forbidden!');
            return $this->redirectToRoute('app_home');
		}*/

        $form  = $this->createForm(EvaluationType::class, $evaluation, array(
            'action' => $this->generateUrl('prof_evaluations_update', array('id' => $evaluation->getId())),
            'method' => 'PUT',
        ));

        $form->handleRequest($request);
        $idcourse = $request->request->get('idcourse');
        $idsequence = $request->request->get('idsequence');
        $competence = $request->request->get('competence');
        $course = $this->crsRepo->findOneBy(array("id" => $idcourse));
        $sequence = $this->seqRepo->findOneBy(array("id" => $idsequence));
        if($sequence == null)
        {
                $sequence = $this->seqRepo->findOneBy(array("activated" => true));
        }
        $marks = $this->markRepo->findBy(array("evaluation" => $evaluation));
        $notes  = array();
        $year = $this->schoolYearService->sessionYearById();
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($evaluation->getClassRoom(), $year);

        foreach ($studentsEnrolledInClass as $std) {
            foreach ($marks as $mark) {
                if ($mark->getStudent()->getId() == $std->getId()) {
                    $notes[$std->getMatricule()] = $mark;
                    break;
                }
            }
        }

        // dd($marks);
        /*  if($form->isSubmitted() && $form->isValid())
        {
		    $year = $this->scRepo->findOneBy(array("activated" => true));
			
            $this->em->flush();
            $this->addFlash('success', 'Evaluation succesfully updated');
            return $this->redirectToRoute('admin_evaluations');
        }*/
        $evaluation->setAuthor($this->getUser());
        return $this->render('evaluation/edit.html.twig', [
            'marks' => $notes,
            'students' => $studentsEnrolledInClass,
            'evaluation' => $evaluation,
            'edit_form' => $form->createView()
        ]);
    }

    /**
     * Update a mark on an evaluation entity if the student is not absent or add a new mark if the student was absent.
     */
    public function editMark(Request $request, Evaluation $evaluation, String $matricule)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $year = $this->schoolYearService->sessionYearById();
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($evaluation->getClassRoom(), $year);
        $marks = $this->markRepo->findBy(array("evaluation" => $evaluation));
        $note = $_POST[$matricule."note"];
        $appr = $_POST[$matricule."appr"];
        $weight = $_POST[$matricule."weight"];
        $pos = 0;
        $index=0;
        $found = false;
        while($index < count($marks) && !$found)
        {
            if($marks[$index]->getStudent()->getMatricule() == $matricule)
            {
                $found = true;
                $marks[$index]->setValue($note);
                $marks[$index]->setWeight($weight);
                $marks[$index]->setAppreciation($appr);
                $this->em->persist($marks[$index]);
                $this->notes[$pos++] = $marks[$index]; // Construction d'un arrayList pour trie
            }
            else
            {
                $index++;
            }
        }
        if(!$found)
        {
            $newMark = new Mark();
            $student = $this->stdRepo->findOneByMatricule($matricule);
            $newMark->setValue($note);
            $newMark->setWeight($weight);
            $newMark->setAppreciation($appr);
            $newMark->setEvaluation($evaluation);
            $newMark->setStudent($student);
            $evaluation->addMark($newMark);
            $this->em->persist($newMark);
            $this->notes[$pos++] = $newMark; // Construction d'un arrayList pour trie
        }

        $this->em->persist($evaluation);
        $this->em->flush();
        
       
    }

    /**
     * Edits an existing Evaluation entity.
     *
     * @Route("/{id}/update", name="prof_evaluations_update", requirements={"id"="\d+"})
     * @Method("PUT")
     
     */
    public function updateAction(Evaluation $evaluation, Request $request, SessionInterface $session)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $year = $this->schoolYearService->sessionYearById();
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($evaluation->getClassRoom(), $year);
      
        if ($content = $request->getContent()) {
            $evaluation->setFailluresF(0);
            $evaluation->setFailluresH(0);
            $evaluation->setSuccessF(0);
            $evaluation->setSuccessH(0);
            $evaluation->setAbscent(0);
            $effectif = 0;
            $total = 0;
            foreach ($studentsEnrolledInClass as $std) {
                $this->editMark($request, $evaluation, $std->getMatricule());
                $note = $_POST[$std->getMatricule()."note"];
                $weight = $_POST[$std->getMatricule() . "weight"];
                if (strcmp($std->getGender(), "M") == 0) {
                    if ($note < 10) {
                        $evaluation->addFailluresH();
                    } else {
                        $evaluation->addSuccessH();
                    }
                } else {
                    if ($note < 10) {
                        $evaluation->addFailluresH();
                    } else {
                        $evaluation->addSuccessF();
                    }
                }
                if ($weight == 0) {
                    $evaluation->addAbscent();
                } else {
                    $effectif++;
                    $total += $note;
                }
             
            }
        }
        // disposition des rang dans les notes
        usort($this->notes, function ($a, $b) {
            if ($a->getValue() == $b->getValue()) {
                return 0;
            }
            return ($a->getValue() < $b->getValue()) ? -1 : 1;
        });
        $pos = count($this->notes);
        foreach ($this->notes as $mark) {
            $mark->setRank2($pos);
            $pos--;
        }
        if ($effectif != 0) {
            $evaluation->setMoyenne($total / $effectif);
        }
        $this->em->flush();
        $this->addFlash('success', 'Evaluation succesfully updated');
        return $this->redirect($this->generateUrl('admin_evaluations'));
    }


    /**
     * Deletes a Evaluationme entity.
     *
     * @Route("/{id}/delete", name="admin_evaluations_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Evaluation $evaluation, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        /* if($evaluation->getTeacher()!=$this->getUser())
        {
            $this->addFlash('warning', 'Access forbidden!');
            return $this->redirectToRoute('app_home');
        }*/
        //   dd($this->isCsrfTokenValid('evaluations_deletion'.$evaluation->getId(), $request->request->get('csrf_token') ));
        // if($this->isCsrfTokenValid('evaluations_deletion'.$evaluation->getId(), $request->request->get('csrf_token') )){

        foreach ($evaluation->getMarks() as $mark) {
            $this->em->remove($mark);
        }
        $this->em->remove($evaluation);

        $this->em->flush();
        $this->addFlash('info', 'Evaluation succesfully deleted');
        // }

        return $this->redirectToRoute('admin_evaluations');
    }

    /**
     * Displays a form to create a new Evaluation entity.
     *
     * @Route("/fiche", name="admin_classroom_students",  options = { "expose" = true })
     * @Method("POST")
     * @Template()
     */
    public function listStudentsFicheAction(Request $request, SessionInterface $session)
    {
        if ($_POST["idclassroom"] && $_POST["idsequence"]) {
            $idclassroom = $_POST["idclassroom"];
            $idsequence = $_POST["idsequence"];
           
            if ($idclassroom != null && $idsequence != null) {
                $year = $this->schoolYearService->sessionYearById();
                $classRoom = $this->clRepo->findOneById($idclassroom);
                $sequence = $this->seqRepo->findOneById($idsequence);
                $coursesOfRoom = $this->crsRepo->findProgrammedCoursesInClassAndNoYetEvaluated($classRoom, $sequence);
                $coursesOfConnectedUser = $this->getUser()->getCourses($year);
                if ($this->isGranted('ROLE_PROF')) {
                    $courses = array_intersect($coursesOfRoom, $coursesOfConnectedUser);
                }
                if ($this->isGranted('ROLE_ADMIN')) {
                    $courses = $coursesOfRoom;
                }

                // Liste des élèves inscrit dans la salle de classe sélectionnée
                $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($classRoom, $year);
                if ($studentsEnrolledInClass != null) {
                    return $this->render('evaluation/liststudents.html.twig', array('students' => $studentsEnrolledInClass, 'courses' => $courses));
                }
            }
        }
        return new Response("No Students");
    }


    /**
     * Finds and displays a Evaluation entity.
     *
     * @Route("/{id}/pdf", name="admin_evaluations_pdf", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function pdfAction(Evaluation $evaluation, \Knp\Snappy\Pdf $snappy)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $author = $this->userRepo->findOneBy(["id"=>$evaluation->getAuthor()->getId()]);
        $html = $this->renderView('evaluation/pdf.html.twig', array(
            'evaluation' => $evaluation,
            'author' => $author
        ));
        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $evaluation->getSequence()->getWording() . '_' . $evaluation->getClassRoom()->getName() . '_' . $evaluation->getId() . '.pdf"',
            )
        );
    }
}
