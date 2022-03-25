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
use App\Repository\SequenceRepository;
use App\Repository\EvaluationRepository;
use App\Repository\StudentRepository;
use App\Repository\QuaterRepository;
use App\Repository\MarkRepository;
use App\Entity\ClassRoom;
use App\Form\ClassRoomType;
use App\Entity\Sequence;
use App\Entity\Quater;


/**
 * SchoolYear controller.
 *
 * @Route("/admin/rooms")
 */
class ClassRoomController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;
    private $stdRepo;
    private $seqRepo;
    private $evalRepo;
    private $qtRepo;
    private $markRepo;

    public function __construct(MarkRepository $markRepo, QuaterRepository $qtRepo,StudentRepository $stdRepo, EvaluationRepository $evalRepo, SchoolYearRepository $scRepo, SequenceRepository $seqRepo, ClassRoomRepository $repo,EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->seqRepo = $seqRepo;
        $this->evalRepo = $evalRepo;
        $this->stdRepo = $stdRepo;
        $this->qtRepo = $qtRepo;
        $this->markRepo = $markRepo;
    }

     /**
     * Lists all ClassRoomme entities.
     *
     * @Route("/", name="admin_classrooms")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(  SequenceRepository $seqRepo)
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
        foreach($studentEnrolled as $std) {
        $filename = "assets/images/student/".$std->getMatricule().".jpg";
         $fileExists[] = file_exists($filename);
        }
    
      
        // Extraction de donnees pour les graphes
        $seqs = $this->seqRepo->findSequenceThisYear($year);
        $evals=[];
        $evalSeqs=[];

        foreach($seqs as $seq) {
            $evalSeqs[$seq->getId()] = $this->evalRepo->findBy(array("classRoom" => $classroom,"sequence" => $seq ));
        }

        $courses = [];
        $averageSeqs= [];
        $seqs = $this->seqRepo->findSequenceThisYear($year);
        
        // Traitements de donnees pour les graphes
       
        foreach($evalSeqs[$seq->getId()] as $eval) {
            $courses[] = $eval->getCourse()->getWording();
        }
       
        foreach($seqs as $seq) {
            $average=[];
            foreach($evalSeqs[$seq->getId()]  as $eval) {
                 $average[] = $eval->getMoyenne();
            }
           
            $averageSeqs[$seq->getId()]= $average;
        }
       
        
       
        
        foreach ($classroom->getModules() as $module ) {
            foreach ($module->getCourses() as $course) {
               // dd($course->getAttributions());
                if($course->getAttributions()[$year->getId()-1]){
                    $attributions[$course->getId()]=$course->getAttributions()[$year->getId()-1]->getTeacher()->getFullName();
                }
            }
        }
        $results['classroom' ]=$classroom;
        $results['attributions' ]=$attributions;
        $results['modules' ]= $classroom->getModules();
        $results['studentEnrolled' ]=$studentEnrolled;
        $results['cours' ]= json_encode($courses);
        $results['fileExists'] = $fileExists;

        foreach($seqs as $seq) {
                    $results[strtolower($seq->getWording())]= json_encode($averageSeqs[$seq->getId()]);
        }
        
        //dd(json_encode($results));
        return $this->render('classroom/show.html.twig',$results);
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
        
       
        return $this->render('classroom/show.html.twig', array(
                   
        ));
    }


    /**
     * Finds and displays a ClassRoom entity.
     *
     * @Route("/{id}/reportCardsYear", name="admin_classrooms_reportcards_year", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reportCardsYearAction(ClassRoom $classroom, SchoolYear  $year=null)
    {
        set_time_limit(600);
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ6 AS
                     SELECT DISTINCT  eval.id as eval,crs.id as crs, room.id as room,year.id as year, std.matricule as matricule, std.profileImagePath as profileImagePath,  std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender,std.birthplace as birthplace , teach.username as teacher    , modu.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as valeur, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   JOIN  Student    std     ON  m.student_id        =   std.id
                                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                                        JOIN  Class_Room room    ON   eval.classroom_id     =   room.id
                                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                                        JOIN  Attribution att    ON  att.course_id      =   crs.id
                                        JOIN  Utilisateur  teach ON  att.teacher_id  =   teach.id
                                        JOIN  Module     modu    ON  modu.id       =   crs.module_id
                                        JOIN  Sequence   seq     ON  seq.id     =   eval.sequence_id
                                        JOIN  Quater   quat     ON  seq.quater_id     =   quat.id
                                        JOIN  School_year   year     ON  quat.school_year_id     =   year.id
                          WHERE    room.id = ? AND eval.sequence_id = 6
                          ORDER BY room.id, std.matricule, crs.module_id, crs.wording; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ1 AS
                     SELECT DISTINCT  eval.id as eval,crs.id as crs, room.id as room,year.id as year, std.matricule as matricule, std.profileImagePath as profileImagePath,  std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender,std.birthplace as birthplace , teach.username as teacher    , modu.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as valeur, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   JOIN  Student    std     ON  m.student_id        =   std.id
                                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                                        JOIN  Class_Room room    ON   eval.classroom_id     =   room.id
                                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                                        JOIN  Attribution att    ON  att.course_id      =   crs.id
                                        JOIN  Utilisateur  teach ON  att.teacher_id  =   teach.id
                                        JOIN  Module     modu    ON  modu.id       =   crs.module_id
                                        JOIN  Sequence   seq     ON  seq.id     =   eval.sequence_id
                                        JOIN  Quater   quat     ON  seq.quater_id     =   quat.id
                                        JOIN  School_year   year     ON  quat.school_year_id     =   year.id
                          WHERE    room.id = ?  AND eval.sequence_id = 1
                          ORDER BY room.id, std.matricule, crs.module_id, crs.wording; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ2 AS
                     SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   
                        JOIN  Student    std     ON  m.student_id        =   std.id
                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                        WHERE  eval.classroom_id = ? AND eval.sequence_id = 2
                        ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ3 AS
                     SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   
                        JOIN  Student    std     ON  m.student_id        =   std.id
                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                        WHERE  eval.classroom_id =? AND eval.sequence_id = 3
                        ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ4 AS
                     SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   
                        JOIN  Student    std     ON  m.student_id        =   std.id
                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                        WHERE  eval.classroom_id = ? AND eval.sequence_id = 4
                        ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
        $statement = $connection->prepare(
            "CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ5 AS
                     SELECT DISTINCT  crs.id as crs, eval.id as eval, std.matricule as matricule, m.value as valeur, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   
                        JOIN  Student    std     ON  m.student_id        =   std.id
                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                        WHERE  eval.classroom_id = ? AND eval.sequence_id = 5
                        ORDER BY matricule,eval; "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->execute();
         $dataYear = $em->getConnection()->executeQuery("select *  from data")->fetchAll();

        $this->get('knp_snappy.pdf')->setTimeout(600);

        $html = $this->renderView('classroom/reportcardYear.html.twig', array(
            'year' => $year,
            'data' => $dataYear,
            'room' => $classroom,
            'year' => $year,
            'students' => $studentEnrolled,
        ));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
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
     * @Route("/{id}/reportCardsApcYear", name="admin_classrooms_reportcards_apc_year", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function reportCards2YearAction(ClassRoom $classroom, SchoolYear  $year=null)
    {
        set_time_limit(600);
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $sequences = $this->seqRepo->findSequencesBySchoolYear($year );
        

        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

       
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_DATA AS
                     SELECT DISTINCT   room.id as room,year.id as year, std.matricule as matricule, std.profileImagePath as profileImagePath,  std.lastname as lastname, std.firstname as firstname, std.birthday as birthday, std.gender as gender,std.birthplace as birthplace 
                                        FROM  Student    std     
                                        JOIN  inscription sub    ON   std.id     =   sub.student_id
                                        JOIN  Class_Room room    ON   sub.classroom_id     =   room.id
                                        JOIN  School_year   year     ON  sub.year_id     =   year.id
                          WHERE    room.id = ? 
                          AND    year.id = ? 
                          ORDER BY room.id, std.matricule "
        );
        $statement->bindValue(1, $classroom->getId());
        $statement->bindValue(2, $year->getId());
        $statement->execute();
        $i=1;
        foreach ($sequences as $seq) {
           

            $statement = $connection->prepare(
                "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_SEQ".$i." AS
                     SELECT DISTINCT  crs.id as crs, room.id as room, std.matricule as matricule,  teach.username as teacher    , modu.name as module , crs.wording as wording, crs.coefficient as coefficient,m.value as value, m.weight as weight, m.appreciation as appreciation
                        FROM  Mark  m   JOIN  Student    std     ON  m.student_id        =   std.id
                                        JOIN  Evaluation eval    ON  m.evaluation_id     =   eval.id
                                        JOIN  Class_Room room    ON   eval.classroom_id     =   room.id
                                        JOIN  Course     crs     ON  eval.course_id      =   crs.id
                                        JOIN  Attribution att    ON  att.course_id      =   crs.id
                                        JOIN  Utilisateur  teach ON  att.teacher_id  =   teach.id
                                        JOIN  Module     modu    ON  modu.id       =   crs.module_id
                                        JOIN  Sequence   seq     ON  seq.id     =   eval.sequence_id
                                       
                                       
                          WHERE    room.id = ?  AND eval.sequence_id = ? AND att.year_id = ?
                          ORDER BY  std.matricule; "
            );
           
            $statement->bindValue(1, $classroom->getId());
            $statement->bindValue(2, $seq->getId());
            $statement->bindValue(3, $year->getId());
            $statement->execute();
            $i++;
        }
        
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_QUATER1 AS
                    SELECT DISTINCT  seq1.matricule as matricule , seq1.wording as course, seq1.module as module,seq1.teacher as teacher, (seq1.value*seq1.weight + seq2.value*seq2.weight)/(seq1.weight+seq2.weight)  as value, greatest(seq1.weight , seq2.weight ) as weight
                    FROM V_STUDENT_MARK_DATA_SEQ1 seq1
                    JOIN  V_STUDENT_MARK_DATA_SEQ2   seq2  
                    ON  seq1.matricule    =   seq2.matricule 
                    WHERE seq1.crs = seq2.crs
                    ORDER BY seq1.matricule"
        );
       
        $statement->execute();
        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_QUATER2 AS
                    SELECT DISTINCT  seq3.matricule as matricule ,seq3.wording as course, seq3.module as module, (seq3.value*seq3.weight + seq4.value*seq4.weight)/(seq3.weight+seq4.weight)  as value, greatest(seq3.weight , seq4.weight ) as weight
                    FROM V_STUDENT_MARK_DATA_SEQ3    seq3
                    JOIN  V_STUDENT_MARK_DATA_SEQ4   seq4  
                    ON  seq3.matricule    =   seq4.matricule
                    WHERE seq3.crs = seq4.crs 
                    ORDER BY seq3.matricule"
        );
      
        $statement->execute();

        $statement = $connection->prepare(
            "  CREATE OR REPLACE VIEW V_STUDENT_MARK_DATA_QUATER3 AS
                    SELECT DISTINCT  seq5.matricule as matricule ,seq5.wording as course, seq5.module as module, (seq5.value*seq5.weight + seq6.value*seq6.weight)/(seq5.weight+seq6.weight)  as value, greatest(seq5.weight , seq6.weight ) as weight
                    FROM V_STUDENT_MARK_DATA_SEQ5    seq5
                    JOIN  V_STUDENT_MARK_DATA_SEQ6   seq6  
                    ON  seq5.matricule    =   seq6.matricule
                    WHERE seq5.crs = seq6.crs 
                    ORDER BY seq5.matricule"
        );
      
        $statement->execute();

        $statement = $connection->prepare(
            "  CREATE OR REPLACE TABLE ANNUAL_DATA AS
                    SELECT DISTINCT  vdata.matricule as matricule , vdata.room as room, vdata.year as year,  vdata.profileImagePath as profileImagePath, 
                     vdata.lastname as lastname, vdata.firstname as firstname, vdata.birthday as birthday, vdata.gender as gender,vdata.birthplace as birthplace , 
                     quat1.course as course, quat1.module as module,quat1.teacher as teacher,
                    quat1.value as value1, quat1.weight as weight1,   quat2.weight as weight2, quat2.value as value2 ,   quat3.weight as weight3, quat3.value as value3 
                    FROM V_STUDENT_DATA vdata,  V_STUDENT_MARK_DATA_QUATER1   quat1 , V_STUDENT_MARK_DATA_QUATER2   quat2   , V_STUDENT_MARK_DATA_QUATER3   quat3 
                    WHERE  vdata.matricule    =   quat1.matricule
                    AND  quat1.matricule    =   quat2.matricule
                    AND  quat1.matricule    =   quat3.matricule
                    AND  quat1.course    =   quat2.course
                    AND  quat1.course    =   quat3.course
                    ORDER BY vdata.matricule
                  "
        );
      
        $statement->execute();

        $dataYear = $connection->executeQuery("select *  from ANNUAL_DATA")->fetchAll()
           ;

       
        $this->get('knp_snappy.pdf')->setTimeout(600);

        $html = $this->renderView('classroom/reportcardYearApc.html.twig', array(
            'year' => $year,
            'data' => $dataYear,
            'room' => $classroom,
            'year' => $year,
            'students' => $studentEnrolled,
        ));
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
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
    public function recapitulatifAction(ClassRoom $room, Sequence $seq,\Knp\Snappy\Pdf $snappy)
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
                    'default-header' => false)),
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
            'year'=> $year,
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
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $schoolyear = new ClassRoom();
    	$form = $this->createForm(ClassRoomType::class, $schoolyear);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($schoolyear);
            $this->em->flush();
            $this->addFlash('success', 'ClassRoom succesfully created');
            return $this->redirectToRoute('admin_classrooms');
    	}
    	 return $this->render('classroom/new.html.twig'
    	 	, ['form'=>$form->createView()]
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
                $evaluation = $em->getRepository('AppBundle:Evaluation')->findOneBy(array("classRoom" => $classroom,
                    "sequence" => $seq, "course" => $course));
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
    public function edit(Request $request,ClassRoom $room): Response
    {
        $form = $this->createForm(ClassRoomType::class, $room, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'ClassRoom succesfully updated');
            return $this->redirectToRoute('admin_classrooms');
        }
        return $this->render('classroom/edit.html.twig'	, [
            'room'=>$room,
            'form'=>$form->createView()
        ]);
    }

    

    /**
     * Deletes a ClassRoom entity.
     *
     * @Route("/{id}/delete", name="admin_classrooms_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     
     */
    public function delete(ClassRoom $q, Request $request):Response
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
    public function fichesiplmeAction(ClassRoom $classroom,\Knp\Snappy\Pdf $snappy)
    {
        $em = $this->getDoctrine()->getManager();

        // Année scolaire en cours
        $year = $this->scRepo->findOneBy(array("activated" => true));          
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);
       
        

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
          ); */
        return new Response(
            $snappy->getOutputFromHtml($html, array(
                    'default-header' => false)),
            200,
            array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $classroom->getName() . '.pdf"',
                )
        );

        //return new Response($html);
    }

     /**
     * LISTE DES ELEVES DE LA CLASSE DANS UNE FICHE DE PRESENTATION.
     *
     * @Route("/{id}/presentation", name="admin_classrooms_presentation", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function presentationAction(ClassRoom $classroom)
    {
        // Année scolaire en cours
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);

        $html = $this->renderView('classroom/list.html.twig', array(
            'year' => $year,
            'room' => $classroom,
            'students' => $studentEnrolled,
        ));
       
       /* return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array(
                    'default-header' => false)),
            200,
            array(
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $classroom->getName() . '.pdf"',
                )
        );*/

        return new Response($html);
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
            $dql = 	"SELECT SUM(evaluation.moyenne * course.coefficient)/SUM(course.coefficient)  FROM App\Entity\Evaluation evaluation , App\Entity\Course  course
            WHERE evaluation.course= 		course.id AND evaluation.sequence=?2 AND evaluation.classRoom=?1 ";
            $avg_seq1 = $this->em->createQuery($dql)
                        ->setParameter(1, $room->getId())
                        ->setParameter(2, $seq->getId())
                        ->getSingleScalarResult();
           return round($avg_seq1 ,2);
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
        $dql = 	"SELECT SUM(evaluation.moyenne * course.coefficient)/SUM(course.coefficient)  FROM App\Entity\Evaluation evaluation , App\Entity\Course  course
            WHERE evaluation.course= 		course.id AND evaluation.sequence=?2 AND evaluation.classRoom=?1 ";
       
        $avg_seq =0;
        foreach($quater->getSequences() as $seq) {
            $avg_seq += $this->em->createQuery($dql)
                            ->setParameter(1, $room->getId())
                            ->setParameter(2, $seq->getId())
                            ->getSingleScalarResult();
        }
        return round($avg_seq/2 ,2);
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
                    'default-header' => false)),
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
        $totalNtCoef=0;
        $totalCoef=0;
        $em = $this->getDoctrine()->getManager();
       
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $sequence = $this->seqRepo->findOneBy(array("activated" => true));
        $evaluations = $this->evalRepo->findSequantialExamsOfRoom(  $classroom->getId(), $sequence->getId());  
             
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
            'moyenneGen' => ($totalNtCoef/$totalCoef),
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
    public function reportCardsTrimAction(ClassRoom $classroom, Pdf $pdf)
    {
       // dd();
        set_time_limit(600);
        $em = $this->getDoctrine()->getManager();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $quater = $this->qtRepo->findOneBy(array("activated" => true));
        $sequences = $this->seqRepo->findBy(array("quater" => $quater));
        $studentEnrolled = $this->stdRepo->findEnrolledStudentsThisYearInClass($classroom, $year);
        $dataSeq1 = $this->markRepo->findMarksBySequenceAndClassOrderByStd2($sequences[0], $classroom);
        $dataSeq2 = $this->markRepo->findMarksBySequenceAndClassOrderByStd2($sequences[1], $classroom);
       
       $pdf->setTimeout(600);
   //     if($classroom->getApc()){
        $html = $this->renderView('classroom/reportcardTrimApc.html.twig', array(
            'year' => $year,
            'dataseq1' => $dataSeq1,
            'dataseq2' => $dataSeq2,
            'sequence1' => $sequences[0],
            'sequence2' => $sequences[1],
            'room' => $classroom,
            'quater' => $quater,
            'avg' => $this->generalQuatAverage($classroom,$quater),
            'students' => $studentEnrolled,
            
        ));
       
  /*  } else {
        $html = $this->renderView('classroom/reportcardTrim.html.twig', array(
            'year' => $year,
            'dataseq1' => $dataSeq1,
            'dataseq2' => $dataSeq2,
            'sequence1' => $sequences[0],
            'sequence2' => $sequences[1],
            'room' => $classroom,
            'quater' => $quater,
            'students' => $studentEnrolled,
        ));
   
         return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            200,
             $classroom->getName() . '.pdf'
        );  }*/

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="'.$classroom->getName().'.pdf"'
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
       $datas=null;
       foreach ($data1 as $students) {
           
           foreach ($students as $m) {
               
               $n = new Mark();
               $n->setStudent($m->getStudent());
               $n->setRank($m->getEvaluation()->getCourse()->getCoefficient());
               $n->setAppreciation($m->getEvaluation()->getCourse()->getWording());
               $n->setValue($this->average($data1[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()],$data2[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()]));
              
               $w = $data1[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()]->getWeight() + $data2[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getId()]->getWeight();
               if($w!=0)
                   $n->setWeight($w);
               else 
                   $n->setWeight(0);
               $datas[$m->getStudent()->getMatricule()][$m->getEvaluation()->getCourse()->getWording()]=$n;
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


}
