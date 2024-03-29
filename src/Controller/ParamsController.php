<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Repository\EvaluationRepository;
use App\Repository\SequenceRepository;
use App\Repository\MarkRepository;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use App\Repository\SchoolYearRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\QuaterRepository;
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
 * @Route("/admin/params")
 */
class ParamsController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;
    private $seqRepo;
    private $subRepo;
    private $markRepo;
    private $evalRepo;
    private $qtRepo;
    private  $snappy;

    public function __construct(EntityManagerInterface $em,  Pdf $snappy)
    {
        $this->em = $em;
        $this->snappy = $snappy;
    }

    /**
     * Parameters Settings.
     *
     * @Route("/", name="parameters")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        // $year = $this->scRepo->findOneBy(array("activated" => true));

        return $this->render('params/appsettings.html.twig');
    }

    /**
     * @Route("/create",name= "params_new", methods={"GET","POST"})
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
            return $this->redirectToRoute('params');
        }
        return $this->render(
            'student/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Studentme entity.
     *
     * @Route("/{id}/edit", name="params_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
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
            return $this->redirectToRoute('params_show', ['id' => $student->getId()]);
        }
        return $this->render('student/edit.html.twig', [
            'student' => $student,
            'form' => $form->createView()
        ]);
    }


}
