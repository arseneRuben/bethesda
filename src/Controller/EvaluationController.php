<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\CourseRepository;
use App\Repository\StudentRepository;
use App\Repository\SequenceRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\EvaluationRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * Evaluationme controller.
 *
 * @Route("/admin/evaluations")
 */
class EvaluationController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;
    private $stdRepo;
    private $clRepo;
    private $crsRepo;

    public function __construct(EntityManagerInterface $em,EvaluationRepository $repo,StudentRepository $stdRepo,
    CourseRepository $crsRepo, SchoolYearRepository $scRepo, ClassRoomRepository $clRepo, SequenceRepository $seqRepo)
    {
        $this->em = $em;
        $this->scRepo = $scRepo;
        $this->stdRepo = $stdRepo;
        $this->repo = $repo;
        $this->clRepo = $clRepo;
        $this->crsRepo = $crsRepo;
    }

     /**
     * Lists all Evaluationme entities.
     *
     * @Route("/", name="admin_evaluations")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(PaginatorInterface $paginator,Request $request,EvaluationRepository $repo)
    {
        
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $entities = $this->repo->findAnnualEvaluations($year->getId());
        $evaluations = $paginator->paginate($entities,$request->query->get('page', 1),Evaluation::NUM_ITEMS_PER_PAGE);
        $evaluations->setCustomParameters([
            'position' => 'centered',
            'size' => 'large',
            'rounded' => true,
        ]);
       return $this->render('evaluation/index.html.twig', ['pagination' => $evaluations]);
    }

    /**
     * Finds and displays a Evaluationme entity.
     *
     * @Route("/{id}/show", name="admin_evaluations_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Evaluation $evaluation)
    {
        
        return $this->render('evaluation/show.html.twig', compact("evaluation"));
    }

  /**
     * @Route("/create",name= "admin_evaluations_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $evaluation = new Evaluation();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentsEnrolledInClass = $this->stdRepo->findNotEnrolledStudentsThisYear($year);
    	$form = $this->createForm(EvaluationType::class, $evaluation);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($evaluation);
            $this->em->flush();
            $this->addFlash('success', 'Evaluation succesfully created');
            return $this->redirectToRoute('admin_evaluations');
    	}
    	 return $this->render('evaluation/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

      /**
     * Creates a new Evaluation entity.
     *
     * @Route("/create", name="prof_evaluations_create")
     * @Method({"POST"})
   
     */
    public function createAction(Request $request)
    {

      
        $evaluation = new Evaluation();

        $form = $this->createForm(new EvaluationType(), $evaluation);

        if ($content = $request->getContent()) {
            $marks = json_decode($_POST['marks'], true);
            $notes = array();
            $effectif = 0;
            $total = 0;
            $pos = 0;
            $room = $request->request->get('idroom');
            $instant = $request->request->get('instant');
            $idcourse = $request->request->get('idcourse');
            $idsequence = $request->request->get('idsequence');
            $competence = $request->request->get('competence');

            $classRoom = $this->scRepo->findOneBy(array("id" => $room));
            $course = $this->crsRepo->findOneBy(array("id" => $idcourse));
            $sequence = $this->seqRepo->findOneBy(array("id" => $idsequence));

            //$evaluation->setInstant($instant);
            $evaluation->setCourse($course);
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
                $em->persist($mark);
                $evaluation->addMark($mark);
            }
            // disposition des rang dans les notes
            usort($notes, function ($a, $b) {
                if ($a->getValue() == $b->getValue()) {
                    return 0;
                }
                return ($a->getValue() < $b->getValue()) ? -1 : 1;
            });

            foreach ($notes as $mark) {
                $mark->setRank($pos);
                $pos--;
            }
            if ($effectif != 0) {
                $evaluation->setMoyenne($total / $effectif);
            } else {
                $evaluation->setMoyenne(0);
            }
            $em->persist($evaluation);

            $em->flush();

            // return $this->redirect($this->generateUrl('prof_evaluations_pdf', array('id' => $evaluation->getId())));
        }
        return $this->render('evaluation/new.html.twig', array(
            'evaluation' => $evaluation,
            'form' => $form->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing Evaluationme entity.
     *
     * @Route("/{id}/edit", name="admin_evaluations_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Evaluation $evaluation): Response
    {
        $form = $this->createForm(EvaluationType::class, $evaluation, [
            'method'=> 'PUT'
        ]);

        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Evaluation succesfully updated');
            return $this->redirectToRoute('admin_evaluations');
        }
        return $this->render('evaluation/edit.html.twig'	, [
            'evaluation'=>$evaluation,
            'form'=>$form->createView()
        ]);
    }

    

    /**
     * Deletes a Evaluationme entity.
     *
     * @Route("/{id}/delete", name="admin_evaluations_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Evaluation $evaluation, Request $request):Response
    {
       // if($this->isCsrfTokenValid('evaluations_deletion'.$evaluation->getId(), $request->request->get('crsf_token') )){
            $this->em->remove($evaluation);
           
            $this->em->flush();
            $this->addFlash('info', 'Evaluation succesfully deleted');
      // }
       
        return $this->redirectToRoute('admin_evaluations');
    }

     /**
     * Displays a form to create a new Evaluation entity.
     *
     * @Route("/fiche", name="prof_classroom_students",  options = { "expose" = true })
     * @Method("POST")
     * @Template()
     */
    public function listStudentsFicheAction(Request $request)
    {
        if ($_POST["idclassroom"]) {
            $idclassroom = $_POST["idclassroom"];

            if ($idclassroom != null) {
               
                $year = $this->scRepo->findOneBy(array("activated" => true));
                $classRoom = $this->clRepo->findOneById($idclassroom);
                $courses = $this->crsRepo->findProgrammedCoursesInClass($classRoom);
                // Liste des élèves inscrit dans la salle de classe sélectionnée
                $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($classRoom, $year);

                if ($studentsEnrolledInClass != null) {
                    return $this->render('evaluation/liststudents.html.twig', array('students' => $studentsEnrolledInClass, 'courses' => $courses));
                }
            }
        }
        return new Response("No Students");
    }

}
