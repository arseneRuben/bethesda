<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Repository\EvaluationRepository;
use App\Repository\SequenceRepository;
use App\Repository\MarkRepository;
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
 * Studentme controller.
 *
 * @Route("/admin/students")
 */
class StudentController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;
    private $seqRepo;
    private $subRepo;
    private $markRepo;
    private $evalRepo;


    public function __construct(EntityManagerInterface $em, SubscriptionRepository $subRepo, MarkRepository $markRepo, EvaluationRepository $evalRepo, StudentRepository $repo, SequenceRepository $seqRepo, SchoolYearRepository $scRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->markRepo = $markRepo;
        $this->seqRepo = $seqRepo;
        $this->evalRepo = $evalRepo;
        $this->subRepo = $subRepo;
    }

    /**
     * Lists all Studentme entities.
     *
     * @Route("/", name="admin_students")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        // $year = $this->scRepo->findOneBy(array("activated" => true));
        $students = $this->repo->findEnrolledStudentsThisYear2();

        return $this->render('student/list.html.twig', compact("students"));
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
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $seq = $this->seqRepo->findOneBy(array("activated" => true));
        $sub = $this->subRepo->findOneBy(array("student" => $student, "schoolYear" => $year));

        $evals = [];
        $evalSeqs = [];
        $seqs = $this->seqRepo->findSequenceThisYear($year);
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
        // dd($filename);
        $file_exists = file_exists($filename);
        // dd($file_exists);
        $results['student'] = $student;
        $results['file_exists'] = $file_exists;
        $results['cours'] = json_encode($courses);

        foreach ($seqs as $seq) {
            $results[strtolower($seq->getWording())] = json_encode($averageSeqs[$seq->getId()]);
        }


        return $this->render('student/show.html.twig', $results);
    }

    /**
     * @Route("/create",name= "admin_students_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);

        $numero = $this->repo->getNumeroDispo();
        $student->setMatricule($numero);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($student);
            $this->em->flush();
            $this->addFlash('success', 'Student succesfully created');
            return $this->redirectToRoute('admin_students');
        }
        return $this->render(
            'student/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Studentme entity.
     *
     * @Route("/{id}/edit", name="admin_students_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Student $student): Response
    {
        $form = $this->createForm(StudentType::class, $student, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Student succesfully updated');
            return $this->redirectToRoute('admin_students_show', ['id' => $student->getId()]);
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
        if ($this->isCsrfTokenValid('students_deletion' . $student->getId(), $request->request->get('csrf_token'))) {
            $this->em->remove($student);

            $this->em->flush();
            $this->addFlash('info', 'Student succesfully deleted');
        }

        return $this->redirectToRoute('admin_students');
    }
}
