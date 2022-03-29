<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Evaluation;
use App\Filter\PropertySearch;
use App\Form\EvaluationType;
use App\Form\PropertySearchType;
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
    private $seqRepo;
    private $attrRepo;

    private $markRepo;

    public function __construct(
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

        $this->clRepo = $clRepo;
        $this->crsRepo = $crsRepo;
        $this->seqRepo = $seqRepo;
        $this->markRepo = $markRepo;
    }



    /**
     * Lists all Evaluationme entities.
     *
     * @Route("/", name="admin_evaluations")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(PaginatorInterface $paginator, Request $request, EvaluationRepository $repo)
    {
        $search = new PropertySearch();
        $searchForm =  $this->createForm(PropertySearchType::class, $search);
        $year = $this->scRepo->findOneBy(array("activated" => true));

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
    public function showAction(Evaluation $evaluation)
    {
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($evaluation->getClassRoom(), $year);
        return $this->render('evaluation/show.html.twig', ['studentEnrolled' => $studentsEnrolledInClass, 'evaluation' => $evaluation]);
    }



    /**
     * @Route("/new",name= "admin_evaluations_new", methods={"GET"})
     */
    public function new(Request $request): Response
    {
        $evaluation = new Evaluation();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentsEnrolledInClass = $this->stdRepo->findNotEnrolledStudentsThisYear($year);
        $form = $this->createForm(EvaluationType::class, $evaluation);
        //$form->handleRequest($request);
        /*if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($evaluation);
            $this->em->flush();
            $this->addFlash('success', 'Evaluation succesfully created');
            return $this->redirectToRoute('admin_evaluations');
    	}*/
        return $this->render('evaluation/new.html.twig', array(
            // 'students' => $studentsEnrolledInClass,
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
    public function create(Request $request)
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
            $instant = $request->request->get('instant');
            $idcourse = $request->request->get('idcourse');
            $idsequence = $request->request->get('idsequence');
            $competence = $request->request->get('competence');
            $year = $this->scRepo->findOneBy(array("activated" => true));
            $classRoom = $this->clRepo->findOneBy(array("id" => $room));
            $course = $this->crsRepo->findOneBy(array("id" => $idcourse));
            $sequence = $this->seqRepo->findOneBy(array("id" => $idsequence));
            /* $attributions = $this->attrRepo->findAll(array("course" => $course,"schoolYear" => $year ));
		  if(count($attributions) != 1) {
               if(count($attributions)==0 ) {
                    $this->addFlash('warning', 'Cours non attribue!');
                }

                if(count($attributions)>1 ) {
                    $this->addFlash('warning', 'Cours  attribue plusieurs fois la meme annee!');
                }
            }
            //$evaluation->setInstant($instant);*/
            //if($course->getAttributed()) {
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
                $this->em->persist($mark);
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

            //  return $this->redirect($this->generateUrl('admin_evaluations_pdf', array('id' => $evaluation->getId())));
        }
        return $this->redirect($this->generateUrl('admin_evaluations_new'));
    }



    /**
     * Displays a form to edit an existing Evaluationme entity.
     *
     * @Route("/{id}/edit", name="admin_evaluations_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Evaluation $evaluation): Response
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
        $marks = $this->markRepo->findBy(array("evaluation" => $evaluation));
        $notes  = array();
        $year = $this->scRepo->findOneBy(array("activated" => true));

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
        return $this->render('evaluation/edit.html.twig', [
            'marks' => $notes,
            'students' => $studentsEnrolledInClass,
            'evaluation' => $evaluation,
            'edit_form' => $form->createView()
        ]);
    }


    /**
     * Edits an existing Evaluation entity.
     *
     * @Route("/{id}/update", name="prof_evaluations_update", requirements={"id"="\d+"})
     * @Method("PUT")
     
     */
    public function updateAction(Evaluation $evaluation, Request $request)
    {

        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($evaluation->getClassRoom(), $year);
        $marks = $this->markRepo->findBy(array("evaluation" => $evaluation));
        if ($content = $request->getContent()) {
            $evaluation->setFailluresF(0);
            $evaluation->setFailluresH(0);
            $evaluation->setSuccessF(0);
            $evaluation->setSuccessH(0);
            $evaluation->setAbscent(0);
            $notes  = array();
            $effectif = 0;
            $total = 0;
            $pos = 0;
            foreach ($studentsEnrolledInClass as $std) {
                $note = $_POST[$std->getMatricule() . "note"];
                $appr = $_POST[$std->getMatricule() . "appr"];
                $weight = $_POST[$std->getMatricule() . "weight"];

                foreach ($marks as $mark) {
                    if ($mark->getStudent()->getId() == $std->getId()) {
                        $this->em->remove($mark);
                        break;
                    }
                }


                $mark = new Mark();
                $mark->setValue($note);
                $mark->setWeight($weight);
                $mark->setAppreciation($appr);
                $mark->setEvaluation($evaluation);
                $mark->setStudent($std);
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
                $evaluation->addMark($mark);
                $notes[$pos++] = $mark; // Construction d'un arrayList pour trie
                $this->em->persist($mark);
            }
        }
        // disposition des rang dans les notes
        usort($notes, function ($a, $b) {
            if ($a->getValue() == $b->getValue()) {
                return 0;
            }
            return ($a->getValue() < $b->getValue()) ? -1 : 1;
        });
        foreach ($notes as $mark) {
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


    /**
     * Finds and displays a Evaluation entity.
     *
     * @Route("/{id}/pdf", name="admin_evaluations_pdf", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function pdfAction(Evaluation $evaluation, Pdf $snappy)
    {
        $html = $this->renderView('evaluation/pdf.html.twig', array(
            'evaluation' => $evaluation,
        ));

        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $evaluation->getSequence()->getWording() . '_' . $evaluation->getClassRoom()->getName() . '.pdf"',
            )
        );
    }
}
