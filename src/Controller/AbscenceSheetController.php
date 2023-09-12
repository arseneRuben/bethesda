<?php

namespace App\Controller;

use App\Entity\Abscence;
use App\Entity\AbscenceSheet;
use App\Filter\AbscenceSearch;
use App\Form\AbscenceSheetSearchType;
use App\Form\AbscenceSheetType;
use App\Repository\AbscenceSheetRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\StudentRepository;
use App\Repository\SequenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * AbscenceSheet controller.
 *
 * @Route("/admin/abscence_sheet")
 */
class AbscenceSheetController extends AbstractController
{
    private $em;
    private $repo;
    private $seqRepo;
    private $yearRepo;
    private $clRepo;
    private $stdRepo;


    public function __construct(EntityManagerInterface $em, StudentRepository $stdRepo, AbscenceSheetRepository $repo, SchoolYearRepository $yearRepo, SequenceRepository $seqRepo, ClassRoomRepository $clRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->seqRepo = $seqRepo;
        $this->stdRepo = $stdRepo;
        $this->yearRepo = $yearRepo;
        $this->clRepo = $clRepo;
    }


    /**
     * Lists all ClassRoomme entities.
     *
     * @Route("/", name="admin_abscences_sheet_index", methods={"GET"})
     * @Template()
     */
    public function index(PaginatorInterface $paginator, Request $request, AbscenceSheetRepository $abscenceSheetRepository): Response
    {
        $search = new AbscenceSearch();
        $searchForm =  $this->createForm(AbscenceSheetSearchType::class, $search);
        $year = $this->yearRepo->findOneBy(array("activated" => true));
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $room = $this->clRepo->findOneBy(array("id" => $_GET['room']));
            $sequence = $this->seqRepo->findOneBy(array("id" => $_GET['sequence']));
            $entities = $this->repo->findAll();
        } else {
            $entities = $this->repo->findAll();
        }

        $sheets = $paginator->paginate($entities, $request->query->get('page', 1), AbscenceSheet::NUM_ITEMS_PER_PAGE);
        $sheets->setCustomParameters([
            'position' => 'centered',
            'size' => 'large',
            'rounded' => true,
        ]);

        return $this->render('abscence_sheet/index.html.twig', ['pagination' => $sheets, 'searchForm' => $searchForm->createView()]);
    }

    #[Route('/new', name: 'admin_abscence_sheet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abscenceSheet = new AbscenceSheet();
        $form = $this->createForm(AbscenceSheetType::class, $abscenceSheet);
        return $this->render('abscence_sheet/new.html.twig', array(
            'abscence_sheet' => $abscenceSheet,
            'response' => null,
            'form' => $form->createView(),
        ));
    }

    #[Route('/{id}', name: 'admin_abscence_sheet_show', methods: ['GET'])]
    public function show(AbscenceSheet $abscenceSheet): Response
    {
        return $this->render('abscence_sheet/show.html.twig', [
            'abscence_sheet' => $abscenceSheet,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_abscence_sheet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AbscenceSheet $abscenceSheet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbscenceSheetType::class, $abscenceSheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_abscence_sheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abscence_sheet/edit.html.twig', [
            'abscence_sheet' => $abscenceSheet,
            'form' => $form,
        ]);
    }

    /**
     * Displays a form to create a new Evaluation entity.
     *
     * @Route("/fiche", name="admin_abscence_list_students",  options = { "expose" = true })
     * @Method("POST")
     * @Template()
     */
    public function listStudentsFicheAction(Request $request)
    {
        if ($_POST["idClassRoom"]) {
            $idClassRoom = $_POST["idClassRoom"];

            if ($idClassRoom != null) {

                $year = $this->yearRepo->findOneBy(array("activated" => true));
                $classRoom = $this->clRepo->findOneById($idClassRoom);
                // Liste des élèves inscrit dans la salle de classe sélectionnée
                $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($classRoom, $year);

                if ($studentsEnrolledInClass != null) {
                    return $this->render('abscence_sheet/liststudents.html.twig', array('students' => $studentsEnrolledInClass));
                }
            }
        }
        return new Response("No Students");
    }


    /**
     * Creates a new Evaluation entity.
     *
     * @Route("/create", name="admin_abscences_sheet_create")
     * @Method({"POST"})
     * @Template()
     */
    public function create(Request $request)
    {

        /* if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        } */
        $abscenceSheet = new AbscenceSheet();

        if ($content = $request->getContent()) {
            $abscences = json_decode($_POST['abscences'], true);
            $endDate = json_decode($_POST['endDate'], true);
            $startDate = json_decode($_POST['startDate'], true);

            $room = $request->request->get('idRoom');
            $idSequence = $request->request->get('idSequence');
            $classRoom = $this->clRepo->findOneBy(array("id" => $room));
            $sequence = $this->seqRepo->findOneBy(array("id" => $idSequence));

            $abscenceSheet->setClassRoom($classRoom);
            $abscenceSheet->setSequence($sequence);
            $abscenceSheet->setStartDate(new \DateTime($startDate));
            $abscenceSheet->setEndDate(new \DateTime($endDate));

            foreach ($abscences as $record) {
                $abscence = new Abscence();
                $weight = $record["weight"];
                $matricule = $record["matricule"];
                $student = $this->stdRepo->findOneByMatricule($matricule);
                $raison = $record["raison"];
                $justified = $record["justified"];
                $abscence->setWeight($weight);


                $abscence->setStudent($student);
                $abscence->setAbscenceSheet($abscenceSheet);
                $abscence->setReason($raison);
                $abscence->setJustified($justified);
                $abscenceSheet->addAbscence($abscence);
                $this->em->persist($abscence);
            }

            $this->em->persist($abscenceSheet);
            $this->em->flush();
        }
        return $this->redirect($this->generateUrl('admin_abscence_sheet_new'));
    }


    #[Route('/{id}', name: 'admin_abscence_sheet_delete', methods: ['POST'])]
    public function delete(Request $request, AbscenceSheet $abscenceSheet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abscenceSheet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abscenceSheet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_abscence_sheet_index', [], Response::HTTP_SEE_OTHER);
    }
}
