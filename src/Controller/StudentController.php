<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\Subscription;
use App\Entity\ClassRoom;

use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Repository\EvaluationRepository;
use App\Repository\SequenceRepository;
use App\Repository\MarkRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use App\Repository\SchoolYearRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\PaymentRepository;
use App\Repository\QuaterRepository;
use App\Repository\InstallmentRepository;
use App\Repository\PaymentPlanRepository;
use App\Repository\MainTeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Service\SchoolYearService;


/**
 * Studentme controller.
 *
 * @Route("/prof/students")
 */
class StudentController extends AbstractController
{
    private EntityManagerInterface $em;
    private $repo;
    private $scRepo;
    private $seqRepo;
    private SubscriptionRepository $subRepo;
    private $markRepo;
    private $evalRepo;
    private $qtRepo;
    private  $snappy;
    private SchoolYearService      $schoolYearService;
    private PaymentPlanRepository $ppRepo;
    private InstallmentRepository $instRepo;
    private PaymentRepository $pRepo;
    private MainTeacherRepository $mainTeacherRepo;


    public function __construct(PaymentRepository $pRepo, InstallmentRepository $instRepo, PaymentPlanRepository $ppRepo,SchoolYearService $schoolYearService,EntityManagerInterface $em, SubscriptionRepository $subRepo, MarkRepository $markRepo, EvaluationRepository $evalRepo, StudentRepository $repo, SequenceRepository $seqRepo, SchoolYearRepository $scRepo, QuaterRepository $qtRepo,MainTeacherRepository $mainTeacherRepo, Pdf $snappy)
    {
        $this->em       = $em;
        $this->repo     = $repo;
        $this->scRepo   = $scRepo;
        $this->markRepo = $markRepo;
        $this->seqRepo  = $seqRepo;
        $this->evalRepo = $evalRepo;
        $this->subRepo  = $subRepo;
        $this->qtRepo   = $qtRepo;
        $this->snappy   = $snappy;
        $this->ppRepo   = $ppRepo;
        $this->pRepo    = $pRepo;
        $this->instRepo = $instRepo;
        $this->mainTeacherRepo = $mainTeacherRepo;
        $this->schoolYearService = $schoolYearService;
    }

      /**
     * @Route("/create",name= "admin_students_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $student = new Student();

        $form = $this->createForm(StudentType::class, $student);

        $numero = $this->repo->getNumeroDispo();
        $student->setMatricule($numero);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($student->getEntryClass()!=NULL){
                $sub = new Subscription();
                $sub->setStudent($student);
                $sub->setClassRoom($student->getEntryClass());
                $sub->setSchoolYear($this->schoolYearService->sessionYearById());
                $this->em->persist($sub);
            }

            $this->em->persist($student);
            $this->em->flush();
            $this->addFlash('success', 'Student succesfully created');
            return $this->redirectToRoute('admin_students', [
                'type' =>"new_students_not_yet_registered_checkbox",
            ]);
        }
        return $this->render(
            'student/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Lists all Studentme entities.
     *
     * @Route("/{type}", name="admin_students")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($type)
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
       
       
        switch ($type) {
            case "new_students_not_yet_registered_checkbox":
                $students = $this->repo->findNewStudents($year);
                break;
            case "new_registered_students_checkbox":
                $students =  $this->repo->findNewRegisteredStudents($year);
                break;
            case "registered_former_students_checkbox":
                $students =  $this->repo->findFormerRegisteredStudents($year);
                break;
            case "complete_registered_students_checkbox":
                    $students =  $this->repo->findEnrolledStudentsThisYear2($year);
                break;
            default:
                $students = $this->repo->findEnrolledStudentsThisYear2();
                break;
        }
        

        return $this->render('student/list.html.twig', compact("students"));
    }

   /**
     * @Route("/{id}/unregister/{room_id}", name="admin_students_unregister", requirements={"id"="\d+", "room_id"="\d+"})
     * @ParamConverter("std", options={"mapping": {"id": "id"}})
     * @ParamConverter("room", options={"mapping": {"room_id": "id"}})
     */
    public function unregisterAction(Student $std, ClassRoom $room)
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
        $sub = $this->subRepo->findOneBy(array("student"=>$std, "classRoom"=>$room, "schoolYear"=>$year));
        $this->em->remove($sub);
        $this->em->flush();
        return $this->redirectToRoute('admin_classrooms_show', ["id"=>$room->getId()]);
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
        // Année scolaire, seuquence, inscrption de l'eleve pour l'annee en cours
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $year = $this->schoolYearService->sessionYearById();
        $seq = $this->seqRepo->findOneBy(array("activated" => true));
        
        $sub = $this->subRepo->findOneBy(array("student" => $student, "schoolYear" => $year));
        $results['student'] = $student;
        $results['cours'] = null;
        $results['session1'] = null;
        $results['session2'] = null;
        $results['session3'] = null;
        $results['session4'] = null;
        $results['session5'] = null;
        $results['session6'] = null;

        $evals = [];
        $evalSeqs = [];
        $payments = $this->pRepo->findBy(array( "schoolYear"=> $year, "student"=> $student), array('updatedAt' => 'ASC'));
        $paymentPlan = $this->ppRepo->findOneBy(array( "schoolYear"=> $year));
        if($sub!=null){
            $installments = $this->instRepo->findBy(array( "paymentPlan"=> $paymentPlan, "classRoom"=> $sub->getClassRoom()));
        } else {
            $installments = $this->instRepo->findBy(array( "paymentPlan"=> $paymentPlan));
        }
        $seqs = $this->seqRepo->findSequenceThisYear($year);
        if ($sub != null) {
            foreach ($seqs as $seq) {
                $evalSeqs[$seq->getId()] = $this->evalRepo->findBy(array("classRoom" => $sub->getClassRoom(), "sequence" => $seq));
            }

            $courses = [];
            $averageSeqs = [];
            // Traitements de donnees pour les graphes

            foreach ($evalSeqs[$seq->getId()] as $eval) {
                $courses[] = $eval->getCourse()->getWording();
            }

            foreach ($seqs as $seq) {
                $average = [];
                foreach ($evalSeqs[$seq->getId()]  as $eval) {
                    if ($this->markRepo->findOneBy(array("student" => $student, "evaluation" => $eval)))
                        $average[] = $this->markRepo->findOneBy(array("student" => $student, "evaluation" => $eval))->getValue();
                }

                $averageSeqs[$seq->getId()] = $average;
            }


            $filename = "assets/images/student/" . $student->getMatricule() . ".jpg";
            $file_exists = file_exists($filename);
            $results['payments'] = $payments;
            $results['payment_plan'] = $paymentPlan;
            $results['installments'] = $installments;
            $results['sub'] = $sub;
            $results['file_exists'] = $file_exists;
            $results['cours'] = json_encode($courses);
            
            foreach ($seqs as $seq) {
                $results[strtolower($seq->getWording())] = json_encode($averageSeqs[$seq->getId()]);
            }
        }
        return $this->render('student/show.html.twig', $results);
    }

  

    /**
     * Displays a form to edit an existing Studentme entity.
     *
     * @Route("/{id}/edit", name="admin_students_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Student $student): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(StudentType::class, $student, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Student succesfully updated');
            //return $this->redirectToRoute('admin_students_show', ['id' => $student->getId()]);
            /*return $this->redirectToRoute('admin_students', [
                'type' =>"new_students_not_yet_registered_checkbox",
            ]);*/
            $year = $this->schoolYearService->sessionYearById();
            $sub = $this->subRepo->findOneBy(array("student"=>$student,"schoolYear"=>$year));
            return $this->redirectToRoute('admin_classrooms_show', ["id"=>$sub->getClassRoom()->getId()]);
        }
        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView()
        ]);
    }



    /**
     * Deletes a Studentme entity.
     *
     * @Route("/{id}/delete", name="admin_students_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Student $student, Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('students_deletion' . $student->getId(), $request->request->get('csrf_token'))) {
            $this->em->remove($student);
            $this->em->flush();
            $this->addFlash('info', 'Student succesfully deleted');
        }
        return $this->redirectToRoute('admin_students', [
            'type' =>"new_students_not_yet_registered_checkbox",
        ]);
    }
    /**
     * Build student's school certificate
     *
     * @Route("/{id}/certificate", name="admin_student_certificate", requirements={"id"="\d+"})
     */
    public function schoolCertificate(Pdf $pdf, Student $std): Response
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
        $sub = $this->subRepo->findOneBy(array("student" => $std, "schoolYear" => $year));
        $html = $this->renderView('student/school_certificate.html.twig', array(
            'year' => $year,
            'std'  => $std,
            'sub' => $sub
        ));
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="certif_'.$std->getMatricule()  . '.pdf"'
            )
        );
    }

     /**
     * Build student's school certificate
     *
     * @Route("/{id}/receipt", name="admin_student_receipt", requirements={"id"="\d+"})
     */
    public function tuitionReceiptAction(Pdf $pdf, Student $std): Response
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
        $sub = $this->subRepo->findOneBy(array("student" => $std, "schoolYear" => $year));
        $payments = $this->pRepo->findBy(array( "schoolYear"=> $year, "student"=> $std), array('updatedAt' => 'DESC'));
        $paymentPlan = $this->ppRepo->findOneBy(array( "schoolYear"=> $year));
        $installments = $this->instRepo->findBy(array( "paymentPlan"=> $paymentPlan, "classRoom"=> $sub->getClassRoom()));
        $html = $this->renderView('student/tuition_receipt.html.twig', array(
            'year' => $year,
            'std'  => $std,
            'sub' => $sub,
            'payments' => $payments,
            'payment_plan' => $paymentPlan,
            'installments' => $installments
        ));
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="recu_'.$std->getMatricule()  . '.pdf"'
            )
        );
    }

    /**
     * Build student's school certificate
     *
     * @Route("/{id}/badge", name="admin_student_badge", requirements={"id"="\d+"})
     */
    public function schoolBadge(Pdf $pdf, Student $std): Response
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
        $sub = $this->subRepo->findOneBy(array("student" => $std, "schoolYear" => $year));
        $filename = "assets/images/student/" . $std->getMatricule() . ".jpg";
        $fileExist = file_exists($filename);
        $html = $this->renderView('student/badge.html.twig', array(
            'sub' => $sub,
            'fileExist' => $fileExist

        ));
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="badge_'.$std->getMatricule()  . '.pdf"'
            )
        );
    }



     /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardTrim2024", name="admin_students_reportcards_quat_2024", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reporCardTrimAction2024(Pdf $pdf, Student $std){
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $connection = $this->em->getConnection();
        $year = $this->schoolYearService->sessionYearById();
        $sub = $this->subRepo->findOneBy(array("student" => $std, "schoolYear" => $year));
        $quater = $this->qtRepo->findOneBy(array("activated" => true));
        $students = $this->repo->findEnrolledStudentsThisYearInClass($sub->getClassRoom(), $year);
        $mainTeacher = $this->mainTeacherRepo->findOneBy(array("classRoom" => $sub->getClassRoom(), "schoolYear" => $year))-> getTeacher();


        $filename = "assets/images/student/" . $std->getMatricule() . ".jpg";
        $fileExist = file_exists($filename);
        $query =  "  SELECT DISTINCT sequence.id as sequence, course.id as course_id ,course.wording , course.coefficient, mark.value, mark.weight, mark.rank2, evaluation.mini as mini, evaluation.maxi as maxi, evaluation.competence, attribution.teacher_id, school_year.id, user.full_name
        FROM sequence 
        JOIN evaluation ON evaluation.sequence_id = sequence.id
        JOIN course ON evaluation.course_id = course.id
        JOIN attribution on attribution.course_id = course.id
        JOIN user ON user.id = attribution.teacher_id
        JOIN mark ON evaluation.id = mark.evaluation_id
        JOIN quater ON sequence.quater_id = quater.id
        JOIN school_year on quater.school_year_id= school_year.id and school_year.id = attribution.year_id
        WHERE quater.id = :quater_id AND   mark.student_id=:student_id
        ORDER BY course.id,sequence.id; ";
        $params = [
            'quater_id' => $quater->getId(),       
            'student_id' => $std->getId(), // Remplace :city
        ];

        $result = $connection->executeQuery($query, $params);
        $data = $result->fetchAllAssociative();

        $html = $this->renderView('student/reportcard/quaterly_2024.html.twig', array(
            'year' => $year,
            'quater' => $quater,
            'mainTeacher'=>$mainTeacher,
            'data' => $data,
            'std'  => $std,
            'students' => $students,
            'room' => $sub->getClassRoom(),
            'fileExist' => $fileExist
        ));
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="bull_' .  $quater->getId().'_'.$std->getMatricule()  . '.pdf"'
            )
        );
    }

    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardTrim", name="admin_students_reportcards_quat", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reporCardTrimAction(Pdf $pdf, Student $std)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $connection = $this->em->getConnection();
        $year = $this->schoolYearService->sessionYearById();
        $sub = $this->subRepo->findOneBy(array("student" => $std, "schoolYear" => $year));
        $quater = $this->qtRepo->findOneBy(array("activated" => true));
        $sequences = $this->seqRepo->findBy(array("quater" => $quater));
        $filename = "assets/images/student/" . $std->getMatricule() . ".jpg";
        $fileExist = file_exists($filename);
        
        $i = 1;
        foreach ($sequences as $seq) {
            /*******************************************************************************************************************/
            /***************CREATION DE la VIEW DES NOTES  SEQUENTIELLES, TRIMESTRIELLES ET ANNUELLES DE L'ELEVE**************/
            /*******************************************************************************************************************/
            // CAS DES NOTES SEQUENTIELLES
            $statement = $connection->prepare(
                "  CREATE OR REPLACE VIEW V_STUDENT_MARK_SEQ" . $i . " AS
                    SELECT DISTINCT  eval.id as eval,crs.id as crs, room.id as room,  teach.full_name as teacher    , modu.id as module,m.value as value, m.weight as weight
                    FROM  mark  m   JOIN  student    std     ON  m.student_id        =   std.id
                    JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
                    JOIN  class_room room    ON   eval.class_room_id     =   room.id
                    JOIN  course     crs     ON  eval.course_id      =   crs.id
                    JOIN  attribution att    ON  att.course_id      =   crs.id
                    JOIN  user  teach ON  att.teacher_id  =   teach.id
                    JOIN  module     modu    ON  modu.id       =   crs.module_id
                    JOIN  sequence   seq     ON  seq.id     =   eval.sequence_id
                    WHERE   std.id = ?  AND eval.sequence_id =?
                    ORDER BY crs.id; "
            );

            $statement->bindValue(1, $std->getId());
            $statement->bindValue(2, $seq->getId());
            $statement->execute();
            $i++;
        }
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER AS
            SELECT DISTINCT    seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight , seq1.weight as weight1,seq2.weight as weight2, seq1.value as value1,seq2.value as value2,   seq1.teacher as teacher, seq1.module as   module, seq1.room as room
            FROM V_STUDENT_MARK_SEQ1 seq1
            JOIN  V_STUDENT_MARK_SEQ2 seq2  
            ON  (seq1.crs = seq2.crs)
            ORDER BY seq1.crs"
        );
        $statement->execute();
        
       
        
        $dataQuater = $connection->executeQuery("SELECT *  FROM V_STUDENT_MARK_QUATER ")->fetchAll();
        $html = $this->renderView('student/reportcardTrimApc.html.twig', array(
            'year' => $year,
            'quater' => $quater,
            'data' => $dataQuater,
            'sequences' => $sequences,
            'std'  => $std,
            'room' => $sub->getClassRoom(),
            'fileExist' => $fileExist
        ));
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="bull_' .  $quater->getId().'_'.$std->getMatricule()  . '.pdf"'
            )
        );
    }

    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardYear", name="admin_students_reportcards_year", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reporCardYear(Student $std)
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $connection = $this->em->getConnection();
        $year = $this->schoolYearService->sessionYearById();
        $sequences = $this->seqRepo->findSequenceThisYear($year);
        $sub = $this->subRepo->findOneBy(array("student" => $std, "schoolYear" => $year));
        $filename = "assets/images/student/" . $std->getMatricule() . ".jpg";
        $fileExist = file_exists($filename);
        $i = 1;
        foreach ($sequences as $seq) {
            /*******************************************************************************************************************/
            /***************CREATION DE la VIEW DES NOTES  SEQUENTIELLES, TRIMESTRIELLES ET ANNUELLES DE L'ELEVE**************/
            /*******************************************************************************************************************/
            // CAS DES NOTES SEQUENTIELLES
            $statement = $connection->prepare(
                "  CREATE OR REPLACE VIEW V_STUDENT_MARK_SEQ" . $i . " AS
                    SELECT DISTINCT  eval.id as eval,crs.id as crs, room.id as room,year.id as year,  teach.id as teacher    , modu.id as module,m.value as value, m.weight as weight
                    FROM  mark  m   JOIN  student    std     ON  m.student_id        =   std.id
                    JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
                    JOIN  class_room room    ON   eval.class_room_id     =   room.id
                    JOIN  course     crs     ON  eval.course_id      =   crs.id
                    JOIN  attribution att    ON  att.course_id      =   crs.id
                    JOIN  user  teach ON  att.teacher_id  =   teach.id
                    JOIN  module     modu    ON  modu.id       =   crs.module_id
                    JOIN  sequence   seq     ON  seq.id     =   eval.sequence_id
                    JOIN  quater   quat     ON  seq.quater_id     =   quat.id
                    JOIN  school_year   year ON  quat.school_year_id     =   year.id
                    WHERE   std.id = ? AND  room.id = ? AND eval.sequence_id =?
                    ORDER BY crs.id; "
            );

            $statement->bindValue(1, $std->getId());
            $statement->bindValue(2, $sub->getClassRoom()->getId());
            $statement->bindValue(3, $seq->getId());
            $statement->execute();
            $i++;
        }
        // CAS DES NOTES TRIMESTRIELLES
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER1 AS
            SELECT DISTINCT    seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   modu, seq1.room as room
            FROM V_STUDENT_MARK_SEQ1 seq1
            JOIN  V_STUDENT_MARK_SEQ2 seq2  
            ON  (seq1.crs = seq2.crs)
            ORDER BY seq1.crs"
        );
        $statement->execute();
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER2 AS
            SELECT DISTINCT    seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   modu, seq1.room as room
            FROM V_STUDENT_MARK_SEQ3 seq1
            JOIN  V_STUDENT_MARK_SEQ4 seq2  
            ON  (seq1.crs = seq2.crs)
            ORDER BY seq1.crs"
        );
        $statement->execute();
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER3 AS
            SELECT DISTINCT    seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   modu, seq1.room as room
            FROM V_STUDENT_MARK_SEQ5 seq1
            JOIN  V_STUDENT_MARK_SEQ6 seq2  
            ON  (seq1.crs = seq2.crs)
            ORDER BY seq1.crs"
        );
        $statement->execute();
       // dd($dataYear);
        // CAS DES NOTES ANNUELLES

        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW ANNUAL_DATA AS
            SELECT DISTINCT 
            course.wording as course, course.coefficient as coef, 
            module.name as module,
            user.full_name as teacher,
            quat1.value as value1, quat1.weight as weight1,  
            quat2.value as value2,  quat2.weight as weight2,  
            quat3.value as value3,quat3.weight as weight3,
            ( quat1.value*quat1.weight+ quat2.value*quat2.weight + quat3.value*quat3.weight) /(quat1.weight+quat2.weight+quat3.weight) as value
             
            FROM V_STUDENT_MARK_QUATER1  quat1 
            JOIN  class_room ON class_room.id = quat1.room
            JOIN  course    ON course.id = quat1.crs
            JOIN  module    ON course.module_id = quat1.modu
            JOIN user ON user.id = quat1.teacher
            JOIN   V_STUDENT_MARK_QUATER2   quat2  ON   quat1.crs = quat2.crs
            JOIN 
            V_STUDENT_MARK_QUATER3   quat3  ON  quat2.crs = quat3.crs
            ORDER BY  module
            "
        );
        $statement->execute();

        $dataYear = $connection->executeQuery("SELECT *  FROM ANNUAL_DATA ")->fetchAll();
        
        $html = $this->renderView('student/reportcardYearApc.html.twig', array(
            'year' => $year,
            'data' => $dataYear,
            'std'  => $std,
            'room' => $sub->getClassRoom(),
            'fileExist' => $fileExist

        ));

        return new Response(
            $this->snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="BUL_ANN_' . $std->getMatricule() . '.pdf"',
            )
        );
    }
}
