<?php

namespace App\Controller;

use App\Entity\Sequence;

use App\Entity\Abscence;
use App\Entity\AbscenceSheet;
use App\Filter\AbscenceSearch;
use App\Form\AbscenceSheetSearchType;
use App\Form\AbscenceSheetType;
use App\Repository\AbscenceSheetRepository;
use App\Repository\AbscenceRepository;
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
    private $absRepo;
    private $seqRepo;
    private $yearRepo;
    private $clRepo;
    private $stdRepo;


    public function __construct(EntityManagerInterface $em, StudentRepository $stdRepo, AbscenceSheetRepository $repo, AbscenceRepository $absRepo, SchoolYearRepository $yearRepo, SequenceRepository $seqRepo, ClassRoomRepository $clRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->absRepo = $absRepo;
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
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
          
            $room = $this->clRepo->findOneBy(array("id" => $_GET['room']));
           
            $sequence = $this->seqRepo->findOneBy(array("id" => $_GET['sequence']));
           
            $entities = $this->repo->findBy(array("sequence" => $sequence, "classRoom" => $room));
          
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



    #[Route('/{id}/edit', name: 'admin_abscence_sheet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AbscenceSheet $abscenceSheet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbscenceSheetType::class, $abscenceSheet, array(
            'action' => $this->generateUrl('admin_abscence_sheet_update', array('id' => $abscenceSheet->getId())),
            'method' => 'PUT',
        ));
        $form->handleRequest($request);

        $notes  = array();
        $abscences = $this->absRepo->findBy(array("abscenceSheet" => $abscenceSheet));
        $year = $this->yearRepo->findOneBy(array("activated" => true));

        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($abscenceSheet->getClassRoom(), $year);

        foreach ($studentsEnrolledInClass as $std) {
            foreach ($abscences as $abs) {
                if ($abs->getStudent()->getId() == $std->getId()) {
                    $notes[$std->getMatricule()] = $abs;
                    break;
                }
            }
        }
        // dd($notes);
        return $this->render('abscence_sheet/edit.html.twig', [
            'abscences' => $notes,
            'students' => $studentsEnrolledInClass,
            'abscence_sheet' => $abscenceSheet,
            'edit_form' => $form->createView()
        ]);
    }

    /**
     * Edits an existing Evaluation entity.
     *
     * @Route("/{id}/update", name="admin_abscence_sheet_update", requirements={"id"="\d+"})
     * @Method("PUT")
     
     */
    public function updateAction(AbscenceSheet $sheet, Request $request)
    {

        $year = $this->yearRepo->findOneBy(array("activated" => true));
        $studentsEnrolledInClass = $this->stdRepo->findEnrolledStudentsThisYearInClass($sheet->getClassRoom(), $year);
        $abscences = $this->absRepo->findBy(array("abscenceSheet" => $sheet));
        $newStartDate = new \DateTime($request->request->get("abscence_sheet")["startDate"]);
        $newEndDate = new \DateTime($request->request->get("abscence_sheet")["endDate"]);
        $sequence = $this->seqRepo->findOneBy(array("id" => $request->request->get("abscence_sheet")['sequence']));
        if ($newStartDate >= $sequence->getStartDate() && $newEndDate <= $sequence->getEndDate()) {
            foreach ($studentsEnrolledInClass as $std) {
                $raison = $_POST[$std->getMatricule() . "raison"];
                $weight = $_POST[$std->getMatricule() . "weight"];
                foreach ($abscences as $abs) {
                    if ($abs->getStudent()->getMatricule() == $std->getMatricule()) {
                        $abs->setReason($raison);
                        $abs->setWeight($weight);
                        $this->em->persist($abs);
                        break;
                    }
                }
                $this->em->flush();
                $this->addFlash('success', 'Sheet succesfully updated');
            }
        } else {
            $this->addFlash('danger', 'Les dates ne sont pas incluse dans la session');
        }
        return $this->redirect($this->generateUrl('admin_abscences_sheet_index'));
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
            $classRoom = $this->clRepo->findOneBy(array("id" => $room));

            $idSequence = $request->request->get('idSequence');
            $sequence = $this->seqRepo->findOneBy(array("id" => $idSequence));

            $abscenceSheet->setClassRoom($classRoom);
            $abscenceSheet->setSequence($sequence);
            $abscenceSheet->setStartDate(new \DateTime($startDate));
            $abscenceSheet->setEndDate(new \DateTime($endDate));
            if ((new \DateTime($startDate) <= new \DateTime($endDate) || (new \DateTime($startDate) >= $sequence->getStartDate() && new \DateTime($endDate) <= $sequence->getEndDate()))) {
                foreach ($abscences as $record) {
                    $abscence = new Abscence();
                    $weight = $record["weight"];
                    $matricule = $record["matricule"];
                    $student = $this->stdRepo->findOneByMatricule($matricule);
                    $raison = $record["raison"];
                    $abscence->setWeight($weight);
                    if ($weight > 0 && $raison != "RAS") {
                        $abscence->setJustified(true);
                    }


                    $abscence->setStudent($student);
                    $abscence->setAbscenceSheet($abscenceSheet);
                    $abscence->setReason($raison);
                    $abscenceSheet->addAbscence($abscence);
                    $this->em->persist($abscence);
                }
                $this->em->persist($abscenceSheet);
                $this->em->flush();
            } else {
                if (new \DateTime($startDate) <= new \DateTime($endDate))
                    $this->addFlash('danger', 'Les dates ne sont pas incluse dans la session');
                else
                    $this->addFlash('danger', 'La date de debut doit etre anterieure a la date de fin');
            }
        }
        return $this->redirect($this->generateUrl('admin_abscence_sheet_new'));
    }


    #[Route('/{id}/delete', name: 'admin_abscences_sheet_delete')]
    public function delete(Request $request, AbscenceSheet $abscenceSheet): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('abscence_sheet_deletion' . $abscenceSheet->getId(), $request->request->get('csrf_token'))) {
            foreach ($abscenceSheet->getAbscences() as $abs) {
                $this->em->remove($abs);
            }
            $this->em->remove($abscenceSheet);
            $this->em->flush();
            $this->addFlash('success', 'Sheet succesfully deleted');
        }

        return $this->redirectToRoute('admin_abscences_sheet_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}', name: 'admin_abscence_sheet_show', methods: ['GET'])]
    public function show(AbscenceSheet $abscenceSheet): Response
    {
        return $this->render('abscence_sheet/show.html.twig', [
            'abscence_sheet' => $abscenceSheet,
        ]);
    }
    /**
     * Finds and displays a Evaluation entity.
     *
     * @Route("/{id}/pdf", name="admin_abscence_sheet_pdf", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function pdfAction(AbscenceSheet $abscenceSheet, \Knp\Snappy\Pdf $snappy)
    {
        $html = $this->renderView('abscence_sheet/pdf.html.twig', array(
            'abscences' => $abscenceSheet,
        ));

        return new Response(
            $snappy->getOutputFromHtml($html, array(
                'default-header' => false
            )),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $abscenceSheet->getSequence()->getWording() . '_' . $abscenceSheet->getClassRoom()->getName() . '_' . $abscenceSheet->getId() . '.pdf"',
            )
        );
    }
}
