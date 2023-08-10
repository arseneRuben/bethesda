<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Knp\Snappy\Pdf;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\QuaterRepository;
use App\Repository\SequenceRepository;
use App\Repository\EvaluationRepository;
use App\Repository\StudentRepository;

use App\Repository\MarkRepository;
use App\Entity\ClassRoom;
use App\Entity\SchoolYear;
use App\Form\ClassRoomType;
use App\Entity\Sequence;
use App\Entity\Quater;
use App\Repository\SubscriptionRepository;

/**
 * ClassRoom controller.
 *
 * @Route("/admin/rooms")
 */
class ClassRoomController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;
    private $stdRepo;
    private $subRepo;
    private $seqRepo;
    private $evalRepo;
    private $qtRepo;
    private $markRepo;
    private  $snappy;

    public function __construct(MarkRepository $markRepo, QuaterRepository $qtRepo, StudentRepository $stdRepo, EvaluationRepository $evalRepo, SchoolYearRepository $scRepo, SequenceRepository $seqRepo, ClassRoomRepository $repo,  SubscriptionRepository $subRepo,  EntityManagerInterface $em, Pdf $snappy)
    {

        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->seqRepo = $seqRepo;
        $this->evalRepo = $evalRepo;
        $this->stdRepo = $stdRepo;
        $this->qtRepo = $qtRepo;
        $this->subRepo = $subRepo;
        $this->markRepo = $markRepo;
        $this->snappy = $snappy;
    }

    /**
     * Lists all ClassRoomme entities.
     *
     * @Route("/", name="admin_classrooms")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(SequenceRepository $seqRepo)
    {

        $classrooms = $this->repo->findAll();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $seq = $this->seqRepo->findOneBy(array("activated" => true));

        return $this->render('classroom/index.html.twig', array(
            'classrooms' => $classrooms,
            'year' => $year,
            'seq' => $seq->getId(),
        ));

        return $this->render('classroom/index.html.twig', compact("classrooms"));
    }

    /** 
     * Finds and displays a ClassRoomme entity.
     *
     * @Route("/{id}/show", name="admin_classrooms_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ClassRoom $classroom, StudentRepository $stdRepo)
    {
        // Année scolaire et seuquence en cours
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $seq = $this->seqRepo->findOneBy(array("activated" => true));
        // Elèves inscrits
        $attributions = null;
        $studentEnrolled = $stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);
        $fileExists = [];
        foreach ($studentEnrolled as $std) {
            $filename = "assets/images/student/" . $std->getMatricule() . ".jpg";
            $fileExists[] = file_exists($filename);
        }
        // Liste des resulats au examens officiels
        $officialExamResults = $this->subRepo->countByMention($year, $classroom);
        $mentionCategories = [];
        $mentionCountCategories = [];
        foreach ($officialExamResults as $exam) {

            switch ($exam["officialExamResult"]) {
                case  "0":
                    $mentionCategories[] = "ECHEC";
                    break;
                case  "1p":
                    $mentionCategories[] = "PASSABLE";
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



        // Extraction de donnees pour les graphes
        $seqs = $this->seqRepo->findSequenceThisYear($year);
        $evalSeqs = [];

        foreach ($seqs as $seq) {
            $evalSeqs[$seq->getId()] = $this->evalRepo->findBy(array("classRoom" => $classroom, "sequence" => $seq));
        }

        $courses = [];
        $averageSeqs = [];

        // Traitements de donnees pour les graphes de notes sequentielles

        foreach ($evalSeqs[$seq->getId()] as $eval) {
            $courses[] = $eval->getCourse()->getWording();
        }

        foreach ($seqs as $seq) {
            $average = [];
            foreach ($evalSeqs[$seq->getId()]  as $eval) {
                $average[] = $eval->getMoyenne();
            }

            $averageSeqs[$seq->getId()] = $average;
        }




        foreach ($classroom->getModules() as $module) {
            foreach ($module->getCourses() as $course) {
                // dd($course->getAttributions());
                if ($course->getAttributions()[$year->getId() - 1]) {
                    $attributions[$course->getId()] = $course->getAttributions()[$year->getId() - 1]->getTeacher()->getFullName();
                }
            }
        }
        $results['classroom'] = $classroom;
        $results['attributions'] = $attributions;
        $results['modules'] = $classroom->getModules();
        $results['studentEnrolled'] = $studentEnrolled;
        $results['cours'] = json_encode($courses);
        $results['fileExists'] = $fileExists;
        $results['sessions'] = json_encode($seqs);
        $results['mentionCategories'] = json_encode($mentionCategories);
        $results['mentionCountCategories'] = json_encode($mentionCountCategories);

        foreach ($seqs as $seq) {
            $results[strtolower($seq->getWording())] = json_encode($averageSeqs[$seq->getId()]);
        }


        return $this->render('classroom/show.html.twig', $results);
    }

    /** 
     * Finds and displays a ClassRoomme entity.
     *
     * @Route("/{id}/stat", name="admin_classrooms_stat", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function statAction(ClassRoom $classroom)
    {


        return $this->render('classroom/show.html.twig', array());
    }


    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardsYear", name="admin_classrooms_reportcards_year", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reportCardsYearAction(ClassRoom $classroom)
    {

        set_time_limit(600);
        $connection = $this->em->getConnection();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ1 AS
            SELECT DISTINCT  eval.id as eval,crs.id as crs, room.id as room,year.id as year, std.matricule as matricule, std.image_name as profileImagePath,  std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender,std.birthplace as birthplace , teach.full_name as teacher    , modu.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as valeur, m.weight as weight, m.appreciation as appreciation
            FROM  mark  m   JOIN  student    std     ON  m.student_id        =   std.id
            JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
            JOIN  class_room room    ON   eval.class_room_id     =   room.id
            JOIN  course     crs     ON  eval.course_id      =   crs.id
            JOIN  attribution att    ON  att.course_id      =   crs.id
            JOIN  user  teach ON  att.teacher_id  =   teach.id
            JOIN  module     modu    ON  modu.id       =   crs.module_id
            JOIN  sequence   seq     ON  seq.id     =   eval.sequence_id
            JOIN  quater   quat     ON  seq.quater_id     =   quat.id
            JOIN  school_year   year     ON  quat.school_year_id     =   year.id
            WHERE    room.id = ? AND eval.sequence_id =1
          "
        );

        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ2 AS
            SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
            FROM  mark  m   
            JOIN  student    std     ON  m.student_id        =   std.id
            JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
            JOIN  course     crs     ON  eval.course_id      =   crs.id
            WHERE  eval.class_room_id = ? AND eval.sequence_id = 2
            ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ3 AS
            SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
            FROM  mark  m   
            JOIN  student    std     ON  m.student_id        =   std.id
            JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
            JOIN  course     crs     ON  eval.course_id      =   crs.id
            WHERE  eval.class_room_id =? AND eval.sequence_id = 3
            ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ4 AS
            SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
            FROM  mark  m   
            JOIN  student    std     ON  m.student_id        =   std.id
            JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
            JOIN  course     crs     ON  eval.course_id      =   crs.id
            WHERE  eval.class_room_id = ? AND eval.sequence_id = 4
            ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ5 AS
            SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
            FROM  mark  m   
            JOIN  student    std     ON  m.student_id        =   std.id
            JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
            JOIN  course     crs     ON  eval.course_id      =   crs.id
            WHERE  eval.class_room_id = ? AND eval.sequence_id = 5
            ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ6 AS
            SELECT DISTINCT  eval.id as eval,crs.id as crs, std.matricule as matricule,  m.value as valeur, m.weight as weight, m.appreciation as appreciation
            FROM  mark  m   JOIN  student    std     ON  m.student_id        =   std.id
            JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
            JOIN  class_room room    ON   eval.class_room_id     =   room.id
            JOIN  course     crs     ON  eval.course_id      =   crs.id
            WHERE    room.id = ? AND eval.sequence_id = 6
            ORDER BY std.matricule"
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $dataYear = $this->em->getConnection()->executeQuery("select *  from V_STUDENT_MARK_DATA_SEQ1 
            INNER JOIN  V_STUDENT_MARK_DATA_SEQ2 ON  V_STUDENT_MARK_DATA_SEQ1.matricule = V_STUDENT_MARK_DATA_SEQ2.matricule 
            INNER JOIN  V_STUDENT_MARK_DATA_SEQ3 ON  V_STUDENT_MARK_DATA_SEQ2.matricule = V_STUDENT_MARK_DATA_SEQ3.matricule 
            INNER JOIN  V_STUDENT_MARK_DATA_SEQ4 ON  V_STUDENT_MARK_DATA_SEQ3.matricule = V_STUDENT_MARK_DATA_SEQ4.matricule 
            INNER JOIN  V_STUDENT_MARK_DATA_SEQ5 ON  V_STUDENT_MARK_DATA_SEQ4.matricule = V_STUDENT_MARK_DATA_SEQ5.matricule 
            INNER JOIN  V_STUDENT_MARK_DATA_SEQ6 ON  V_STUDENT_MARK_DATA_SEQ5.matricule = V_STUDENT_MARK_DATA_SEQ6.matricule 

            ")->fetchAll();

        $this->snappy->setTimeout(600);

        $html = $this->renderView('classroom/reportcardYear.html.twig', array(
            'year' => $year,
            'data' => $dataYear,
            'room' => $classroom,
            'year' => $year,
            'students' => $studentEnrolled,
        ));
        return new Response(
            $this->snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="BUL_ANN_' . $classroom->getName() . '.pdf"',
            )
        );
    }

    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardsApcYearapc", name="admin_class_reportcards_apc_year", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reportCards2YearAction(ClassRoom $classroom)
    {
        set_time_limit(600);
        $connection = $this->em->getConnection();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $sequences  = $this->seqRepo->findSequenceThisYear($year);
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

        $i = 1;
        foreach ($sequences as $seq) {
            /*******************************************************************************************************************/
            /***************CREATION DE la VIEW DES NOTES  SEQUENTIELLES, TRIMESTRIELLES ET ANNUELLES DE LA CLASSE**************/
            /*******************************************************************************************************************/
            // CAS DES NOTES SEQUENTIELLES
            $statement = $connection->prepare(
                "  CREATE OR REPLACE VIEW V_STUDENT_MARK_SEQ" . $i . " AS
                SELECT DISTINCT  eval.id as eval,crs.id as crs, room.id as room,year.id as year, std.id as std,  teach.full_name as teacher    , modu.id as module,m.value as value, m.weight as weight
                FROM  mark  m   JOIN  student    std     ON  m.student_id        =   std.id
                JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
                JOIN  class_room room    ON   eval.class_room_id     =   room.id
                JOIN  course     crs     ON  eval.course_id      =   crs.id
                JOIN  attribution att    ON  att.course_id      =   crs.id  
                JOIN  user  teach        ON  att.teacher_id  =   teach.id
                JOIN  module     modu    ON  modu.id       =   crs.module_id
                JOIN  sequence   seq     ON  seq.id     =   eval.sequence_id
                JOIN  quater   quat      ON  seq.quater_id     =   quat.id
                JOIN  school_year   year ON  quat.school_year_id     =   year.id
                WHERE att.year_id =? AND  room.id = ? AND eval.sequence_id =?  
                ORDER BY room.id,modu.id ,  std; "
            );

            $statement->bindValue(1, $year->getId());
            $statement->bindValue(2, $classroom->getId());
            $statement->bindValue(3, $seq->getId());

            $statement->execute();
            $i++;
        }
        // CAS DES NOTES TRIMESTRIELLES
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER1 AS
            SELECT DISTINCT   seq1.std as std , seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   modu, seq1.room as room
            FROM V_STUDENT_MARK_SEQ1 seq1
            JOIN  V_STUDENT_MARK_SEQ2 seq2  
            ON  (seq1.std    =   seq2.std  AND seq1.crs = seq2.crs)
            ORDER BY seq1.std"
        );

        $statement->execute();


        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER2 AS
            SELECT DISTINCT   seq1.std as std , seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   modu, seq1.room as room
            FROM V_STUDENT_MARK_SEQ3 seq1
            JOIN  V_STUDENT_MARK_SEQ4 seq2  
            ON  (seq1.std    =   seq2.std  AND seq1.crs = seq2.crs)
            ORDER BY seq1.std"
        );


        $statement->execute();


        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER3 AS
            SELECT DISTINCT   seq1.std as std , seq1.crs as crs,  (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   modu, seq1.room as room
            FROM V_STUDENT_MARK_SEQ5 seq1
            JOIN  V_STUDENT_MARK_SEQ6 seq2  
            ON  (seq1.std    =   seq2.std  AND seq1.crs = seq2.crs)
            ORDER BY seq1.std"
        );


        $statement->execute();

        // CAS DES NOTES ANNUELLES

        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW ANNUAL_DATA AS
            SELECT DISTINCT   student.id as idStd ,  student.matricule as matricule ,  student.image_name as profileImagePath, 
            student.lastname as lastname, student.firstname as firstname, student.birthday as birthday,
            student.gender as gender,student.birthplace as birthplace , 
            class_room.name as room_name,
            course.wording as course, course.coefficient as coef, 
            module.name as module,
            user.full_name as teacher,
            quat1.std,quat1.modu,
            quat1.value as value1, quat1.weight as weight1,  
            quat2.value as value2,  quat2.weight as weight2,  
            quat3.value as value3,quat3.weight as weight3,
            greatest(quat1.weight , quat2.weight, quat3.weight ) as weight,
            ( quat1.value*quat1.weight+ quat2.value*quat2.weight + quat3.value*quat3.weight) /(quat1.weight+quat2.weight+quat3.weight) as value
            FROM student  
            JOIN V_STUDENT_MARK_QUATER1  quat1 ON  student.id = quat1.std
            JOIN  class_room ON class_room.id = quat1.room
            JOIN  course    ON course.id = quat1.crs
            JOIN  module    ON course.module_id = quat1.modu
            JOIN user ON user.full_name = quat1.teacher
            JOIN   V_STUDENT_MARK_QUATER2   quat2  ON  quat1.std = quat2.std AND quat1.crs = quat2.crs
            JOIN 
            V_STUDENT_MARK_QUATER3   quat3  ON  quat1.std = quat3.std AND quat1.crs = quat3.crs
            ORDER BY  quat1.std, quat1.modu
        
            "
        );
        $statement->execute();
        $dataYear = $connection->executeQuery("SELECT *  FROM ANNUAL_DATA ")->fetchAll();
        // For calculating ranks
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_RANKS AS
            SELECT DISTINCT idStd , CAST( SUM(value*weight*coef) / sum(weight*coef) AS decimal(4,2)) as moyenne, sum(weight*coef) as totalCoef
            FROM ANNUAL_DATA 
            GROUP BY idStd
            ORDER BY SUM(value*weight*coef) DESC"
        );
        $statement->execute();
        $annualAvg = $connection->executeQuery("SELECT *  FROM V_STUDENT_RANKS ")->fetchAll();
        $annualAvgArray = [];
        $sumAvg = 0;
        $rank = 0;
        $rankArray = [];
        foreach ($annualAvg as $avg) {

            $annualAvgArray[$avg['idStd']] = $avg['moyenne'];
            $rankArray[$avg['idStd']] = ++$rank;
            $sumAvg += $avg['moyenne'];
        }

        $this->snappy->setTimeout(600);

        $html = $this->renderView('classroom/reportcardYear.html.twig', array(
            'year' => $year,
            'data' => $dataYear,
            'room' => $classroom,
            'students' => $studentEnrolled,
            'ranks' => $rankArray,
            'means' => $annualAvgArray,
            'genMean' => $sumAvg / sizeof($annualAvgArray),
        ));

        //return new Response($html);
        return new Response(
            $this->snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="BUL_ANN_' . $classroom->getName() . '.pdf"',
            )
        );
    }
    /**
     * Finds and displays a Evaluation entity.
     *
     * @Route("/{room}/{seq}/pdf", name="admin_classrooms_recapitulatif", requirements={"room"="\d+","seq"="\d+"})
     * @Method("GET")
     * @Template()
     * @return Response
     */
    public function recapitulatifAction(ClassRoom $room, Sequence $seq, \Knp\Snappy\Pdf $snappy)
    {
        // Année scolaire en cours
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($room, $year);
        $html = $this->renderView('classroom/recapitulatifseqvierge.html.twig', array(
            'room' => $room,
            'seq' => $seq,
            'students' => $studentEnrolled,
            'year' => $year,
        ));


        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false,
                'orientation' => 'landscape'
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $room->getName() . '.pdf"',
            )
        );
    }


    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/recapitulatifseq", name="admin_classrooms_recapitulatif_seq", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function recapSeqAction(ClassRoom $room)
    {
        // set_time_limit(600);
        $em = $this->getDoctrine()->getManager();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $seq = $this->seqRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($room, $year);


        $datas = $this->markRepo->findMarksBySequenceAndClassOrderByStd($seq, $room);

        $html = $this->renderView('classroom/recapitulatifseq.html.twig', array(
            'room' => $room,
            'datas' => $datas,
            'year' => $year,
            'seq' => $seq,
            'students' => $studentEnrolled,
        ));

        return new Response($html);
    }

    /**
     * @Route("/create",name= "admin_classrooms_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $schoolyear = new ClassRoom();
        $form = $this->createForm(ClassRoomType::class, $schoolyear);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($schoolyear);
            $this->em->flush();
            $this->addFlash('success', 'ClassRoom succesfully created');
            return $this->redirectToRoute('admin_classrooms');
        }
        return $this->render(
            'classroom/new.html.twig',
            ['form' => $form->createView()]
        );
    }


    /**
     * Rapport séquentiel d'enregistrement des notes.
     *
     * @Route("/{id}/evalrepport", name="admin_current_fulfilled_eval_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function currentFullfilledEvalAction(ClassRoom $classroom)
    {
        $em = $this->getDoctrine()->getManager();
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
        // Liste des séquences de l'année scolaire en cours
        $sequences = $em->getRepository('AppBundle:Sequence')->findSequencesBySchoolYear($year);
        // Liste des matières
        $courses = $em->getRepository('AppBundle:Course')->findProgrammedCoursesInClass($classroom);

        // Elèves inscrits
        foreach ($sequences as $seq) {
            // Lecture de chaque tableau de chaque ligne
            foreach ($courses as $course) {
                // Liste des évaluations
                $evaluation = $em->getRepository('AppBundle:Evaluation')->findOneBy(array(
                    "classRoom" => $classroom,
                    "sequence" => $seq, "course" => $course
                ));
                if ($evaluation != null) {
                    $evaluations[$seq->getId()][$course->getId()] = 1;
                } else {
                    $evaluations[$seq->getId()][$course->getId()] = 0;
                }
            }
        }

        return $this->render('classroom/eval_repport.html.twig', array(
            'evaluations' => $evaluations,
            'courses' => $courses,
            'room' => $classroom,
            'sequences' => $sequences,
        ));
    }


    /**
     * Displays a form to edit an existing ClassRoomme entity.
     *
     * @Route("/{id}/edt", name="admin_classrooms_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, ClassRoom $room): Response
    {
        $form = $this->createForm(ClassRoomType::class, $room, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'ClassRoom succesfully updated');
            return $this->redirectToRoute('admin_classrooms');
        }
        return $this->render('classroom/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView()
        ]);
    }



    /**
     * Deletes a ClassRoom entity.
     *
     * @Route("/{id}/delete", name="admin_classrooms_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     
     */
    public function delete(ClassRoom $q, Request $request): Response
    {
        //  dd($q);
        // if($this->isCsrfTokenValid('classrooms_deletion'.$schoolyear->getId(), $request->request->get('crsf_token') )){
        $this->em->remove($q);

        $this->em->flush();
        $this->addFlash('info', 'ClassRoom succesfully deleted');
        //    }

        return $this->redirectToRoute('admin_classrooms');
    }

    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/fichesimple", name="admin_classrooms_fichesimple", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function fichesiplmeAction(ClassRoom $classroom, \Knp\Snappy\Pdf $snappy)
    {
        $em = $this->getDoctrine()->getManager();

        // Année scolaire en cours
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

        //  dd($this->getParameter('kernel.project_dir'));

        $html = $this->renderView('classroom/fichesimple.html.twig', array(
            'year' => $year,
            'room' => $classroom,
            'students' => $studentEnrolled,
        ));
        /* return new Response(
          $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 200, array(
          'orientation' => 'Landscape',
          'default-header' => true,
          'Content-Type' => 'application/pdf',
          'Content-Disposition' => 'attachment; filename="'.$classroom->getName().'.pdf"',
          )
      );*/
        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $classroom->getName() . '.pdf"',
            )
        );

        //   return new Response($html);
    }

    /**
     * LISTE DES ELEVES DE LA CLASSE DANS UNE FICHE DE PRESENTATION.
     *
     * @Route("/{id}/presentation", name="admin_classrooms_presentation", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function presentationAction(ClassRoom $classroom, \Knp\Snappy\Pdf $snappy)
    {
        // Année scolaire en cours
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

        $html = $this->renderView('classroom/list.html.twig', array(
            'year' => $year,
            'room' => $classroom,
            'students' => $studentEnrolled,
        ));

        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $classroom->getName() . '.pdf"',
            )
        );

        //return new Response($html);
    }

    /**
     * MOYENNE GENERALE DE LA CLASSE A UNE SEQUENCE
     * @Route("/{id_room}/{id_seq}/sqavg", name="admin_classrooms_avg_seq", requirements={"id_room"="\d+", "id_seq"="\d+"})
     * @ParamConverter("room", options={"mapping": {"id_room" : "id"}})
     * @ParamConverter("seq", options={"mapping": {"id_seq"   : "id"}})
     * @Method("GET")
     * @Template()
     */
    public function generalSeqAverage(ClassRoom $room, Sequence $seq)
    {
        $dql =     "SELECT SUM(evaluation.moyenne * course.coefficient)/SUM(course.coefficient)  FROM App\Entity\Evaluation evaluation , App\Entity\Course  course
        WHERE evaluation.course= 		course.id AND evaluation.sequence=?2 AND evaluation.classRoom=?1 ";
        $avg_seq1 = $this->em->createQuery($dql)
            ->setParameter(1, $room->getId())
            ->setParameter(2, $seq->getId())
            ->getSingleScalarResult();
        return round($avg_seq1, 2);
    }

    /**
     * MOYENNE GENERALE DE LA CLASSE A UN TRIMESTRE
     * @Route("/{id_room}/{id_quat}/qtavg", name="admin_classrooms_avg_quat", requirements={"id_room"="\d+", "id_quat"="\d+"})
     * @ParamConverter("room", options={"mapping": {"id_room" : "id"}})
     * @ParamConverter("quater", options={"mapping": {"id_quat"   : "id"}})
     * @Method("GET")
     * @Template()
     */
    public function generalQuatAverage(ClassRoom $room, Quater $quater)
    {
        $dql =     "SELECT SUM(evaluation.moyenne * course.coefficient)/SUM(course.coefficient)  FROM App\Entity\Evaluation evaluation , App\Entity\Course  course
        WHERE evaluation.course= 		course.id AND evaluation.sequence=?2 AND evaluation.classRoom=?1 ";

        $avg_seq = 0;
        foreach ($quater->getSequences() as $seq) {
            $avg_seq += $this->em->createQuery($dql)
                ->setParameter(1, $room->getId())
                ->setParameter(2, $seq->getId())
                ->getSingleScalarResult();
        }
        return round($avg_seq / 2, 2);
    }


    /**
     * Finds and displays a Evaluation entity.
     *
     * @Route("/{room}/pdf", name="admin_classrooms_blanc_ann", requirements={"room"="\d+"})
     * @Method("GET")
     * @Template()
     * @return Response
     */
    public function annualSummaryAction(ClassRoom $room, \Knp\Snappy\Pdf $snappy)
    {
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($room, $year);
        $html = $this->renderView('classroom/blankAnnualForm.html.twig', array(
            'room' => $room,
            'students' => $studentEnrolled,
            'year' => $year,
        ));


        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $room->getName() . '.pdf"',
            )
        );

        /* return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
            'Content-Type' => 'application/pdf',
            'orientation'=>'Landscape',
                                         'default-header'=>true,
            'Content-Disposition' => 'attachment; filename="BUL_ANN_' . $room->getName() . '.pdf"',
                )
            );*/
        // return new Response($html);

    }

    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCards", name="admin_classrooms_reportcards", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reportCardsAction(ClassRoom $classroom)
    {
        set_time_limit(600);
        $totalNtCoef = 0;
        $totalCoef = 0;
        $em = $this->getDoctrine()->getManager();

        $year = $this->scRepo->findOneBy(array("activated" => true));
        $sequence = $this->seqRepo->findOneBy(array("activated" => true));
        $evaluations = $this->evalRepo->findSequantialExamsOfRoom($classroom->getId(), $sequence->getId());

        foreach ($evaluations as $ev) {
            $totalNtCoef += $ev->getMoyenne() * $ev->getCourse()->getCoefficient();
            $totalCoef += $ev->getCourse()->getCoefficient();
        }
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

        $datas = $this->getData($classroom, $sequence);
        $html = $this->renderView('classroom/reportcard.html.twig', array(
            'year' => $year,
            'datas' => $datas,
            'sequence' => $sequence,
            'quater' => $sequence->getQuater(),
            'moyenneGen' => ($totalNtCoef / $totalCoef),
            'room' => $classroom,
            'students' => $studentEnrolled,
        ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $classroom->getName() . '.pdf"',
            )
        );

        //return new Response($html);
    }


    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardsTrim", name="admin_classrooms_reportcards_trim", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reportCardsTrimAction(ClassRoom $room, Pdf $pdf,  Request $request)
    {

        set_time_limit(600);

        $connection = $this->em->getConnection();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $quater = $this->qtRepo->findOneBy(array("activated" => true));
        $sequences = $this->seqRepo->findBy(array("quater" => $quater));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($room, $year);

        $i = 1;
        foreach ($sequences as $seq) {
            /*******************************************************************************************************************/
            /***************CREATION DE la VIEW DES NOTES  SEQUENTIELLES, TRIMESTRIELLES ET ANNUELLES DE LA CLASSE**************/
            /*******************************************************************************************************************/
            // CAS DES NOTES SEQUENTIELLES
            $statement = $connection->prepare(
                "  CREATE OR REPLACE VIEW V_STUDENT_MARK_SEQ" . $i . " AS
                SELECT DISTINCT  eval.id as eval,crs.id as crs, crs.coefficient as coef, room.id as room,year.id as year, std.id as std,  teach.full_name as teacher    , modu.id as module,m.value as value, m.weight as weight
                FROM  mark  m   JOIN  student    std     ON  m.student_id        =   std.id
                JOIN  evaluation eval    ON  m.evaluation_id     =   eval.id
                JOIN  class_room room    ON   eval.class_room_id     =   room.id
                JOIN  course     crs     ON  eval.course_id      =   crs.id
                JOIN  attribution att    ON  att.course_id      =   crs.id  
                JOIN  user  teach        ON  att.teacher_id  =   teach.id
                JOIN  module     modu    ON  modu.id       =   crs.module_id
                JOIN  sequence   seq     ON  seq.id     =   eval.sequence_id
                JOIN  quater   quat      ON  seq.quater_id     =   quat.id
                JOIN  school_year   year ON  quat.school_year_id     =   year.id
                WHERE att.year_id =? AND  room.id = ? AND eval.sequence_id =?  
                ORDER BY room.id,modu.id ,  std; "
            );


            $statement->bindValue(2, $room->getId());
            $statement->bindValue(3, $seq->getId());
            $statement->bindValue(1, $year->getId());
            $statement->execute();
            $i++;
        }

        // CAS DES NOTES TRIMESTRIELLES
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_QUATER AS
            SELECT DISTINCT   seq1.std as std , seq1.crs as crs , seq1.coef as coef,  seq1.value as value1, seq1.weight as weight1,seq2.value as value2, seq2.weight as weight2,    (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight ,  seq1.teacher as teacher, seq1.module as   module, seq1.room as room
            FROM V_STUDENT_MARK_SEQ1 seq1
            JOIN  V_STUDENT_MARK_SEQ2 seq2  ON  (seq1.std    =   seq2.std  AND seq1.crs = seq2.crs )
            ORDER BY std , module"
        );
        $statement->execute();
        $dataQuater = $connection->executeQuery("SELECT *  FROM V_STUDENT_MARK_QUATER ")->fetchAll();
        // $this->snappy->setTimeout(600);
        // For calculating ranks
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_RANKS AS
        SELECT DISTINCT std , CAST( SUM(value*weight*coef) / sum(weight*coef) AS decimal(4,2)) as moyenne, sum(weight*coef) as totalCoef
        FROM V_STUDENT_MARK_QUATER 
        GROUP BY std
        ORDER BY SUM(value*weight*coef) DESC"
        );
        $statement->execute();
        $quaterAvg = $connection->executeQuery("SELECT *  FROM V_STUDENT_RANKS ")->fetchAll();
        $quaterAvgArray = [];
        $sumAvg = 0;
        $rank = 0;
        $rankArray = [];
        foreach ($quaterAvg as $avg) {

            $quaterAvgArray[$avg['std']] = $avg['moyenne'];
            $rankArray[$avg['std']] = ++$rank;
            $sumAvg += $avg['moyenne'];
        }

        $pdf->setTimeout(600);
        // dd($quater);
        $html = $this->renderView('classroom/newReportcardTrim.html.twig', array(
            'year' => $year,
            'data' => $dataQuater,
            'ranks' => $rankArray,
            'means' => $quaterAvgArray,
            'genMean' => $sumAvg / sizeof($quaterAvgArray),
            'room' => $room,
            'quater' => $quater,
            'sequences' => $sequences,
            'students' => $studentEnrolled,

        ));
        //dd($html);
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="' . $room->getName() . '.pdf"'
            )
        );
        // return new Response($html);
    }

    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/recapitulatiftrim", name="admin_classrooms_recapitulatif_trim", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function recapTrimAction(ClassRoom $room)
    {
        set_time_limit(600);
        $em = $this->getDoctrine()->getManager();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $quater = $this->qtRepo->findOneBy(array("activated" => true));
        $sequences = $this->seqRepo->findBy(array("quater" => $quater));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYear($room, $year->getId());
        $data1 = $this->markRepo->findMarksBySequenceAndClassOrderByStd($sequences[0], $room);
        $data1 = $this->doubleEntry($data1, $room);
        $data2 = $this->markRepo->findMarksBySequenceAndClassOrderByStd($sequences[1], $room);
        $data2 = $this->doubleEntry($data2, $room);
        $datas = null;
        foreach ($data1 as $students) {

            foreach ($students as $m) {

                $n = new Mark();
                $n->setStudent($m->getStudent());
                $n->setRank($m->getEvaluation()->getCourse()->getCoefficient());
                $n->setAppreciation($m->getEvaluation()->getCourse()->getWording());
                $n->setValue($this->average($data1[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()], $data2[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()]));

                $w = $data1[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()]->getWeight() + $data2[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()]->getWeight();
                if ($w != 0)
                    $n->setWeight($w);
                else
                    $n->setWeight(0);
                $datas[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getWording()] = $n;
            }
        }

        $this->get('knp_snappy.pdf')->setTimeout(600);
        //     if($classroom->getApc()){
        $html = $this->renderView('classroom/recapitulatiftrimWithMoy.html.twig', array(
            'year' => $year,
            'datas' => $datas,
            'room' => $room,
            'quater' => $quater,
            'students' => $studentEnrolled
        ));

        return new Response($html);
    }

    public function officialExam()
    {
        // Retrieve student categories from the corresponding repository
        $categoriesStudent = $this->getDoctrine()->getRepository(CategStudent::class)->findAll();

        // Initialize arrays for student categories, mentions, and counters
        $studentCategories = [];
        $mentionCategories = [];
        $studentCountCategories = [];
        $mentionCountCategories = [];

        // Fill the arrays with data from student categories
        foreach ($categoriesStudent as $category) {
            $studentCategories[] = $category->getName();
            $mentionCategories[] = $category->getMention();
            $studentCountCategories[] = $category->getCountStudent();
            $mentionCountCategories[] = $category->getCountMention();
        }

        // Render the Twig template and pass the data in JSON format
        return $this->render('admin/class_room/show.html.twig', [
            'studentCategories' => json_encode($studentCategories),
            'mentionCategories' => json_encode($mentionCategories),
            'studentCountCategories' => json_encode($studentCountCategories),
            'mentionCountCategories' => json_encode($mentionCountCategories),
        ]);
    }

    /**
     * @Route("/classroom/{id}", name="class_room_stats")
     */
    public function showClassRoomStats(ClassRoomRepository $classRoomRepository, int $id): Response
    {
        $classRoom = $classRoomRepository->find($id);
        if (!$classRoom) {
            throw $this->createNotFoundException('ClassRoom not found');
        }

        $successfulCount = $classRoomRepository->countSuccessfulStudentsForClass($classRoom);
        $unsuccessfulCount = $classRoomRepository->countUnsuccessfulStudentsForClass($classRoom);
        $mentionStatistics = $classRoomRepository->getMentionStatisticsForClass($classRoom);

        return $this->render('class_room/stats.html.twig', [
            'classRoom' => $classRoom,
            'successfulCount' => $successfulCount,
            'unsuccessfulCount' => $unsuccessfulCount,
            'mentionStatistics' => $mentionStatistics,
        ]);
    }
}
