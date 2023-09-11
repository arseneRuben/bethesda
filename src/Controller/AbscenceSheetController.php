<?php

namespace App\Controller;

use App\Entity\AbscenceSheet;
use App\Filter\AbscenceSearch;
use App\Form\AbscenceSheetSearchType;
use App\Form\AbscenceSheetType;
use App\Repository\AbscenceSheetRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
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

    public function __construct(EntityManagerInterface $em, AbscenceSheetRepository $repo, SchoolYearRepository $yearRepo, SequenceRepository $seqRepo, ClassRoomRepository $clRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->seqRepo = $seqRepo;
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

        $evaluations = $paginator->paginate($entities, $request->query->get('page', 1), AbscenceSheet::NUM_ITEMS_PER_PAGE);
        $evaluations->setCustomParameters([
            'position' => 'centered',
            'size' => 'large',
            'rounded' => true,
        ]);



        return $this->render('abscence_sheet/index.html.twig', [
            'abscence_sheets' => $abscenceSheetRepository->findAll(), 'searchForm' => $searchForm->createView()
        ]);
    }

    #[Route('/new', name: 'admin_abscence_sheet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abscenceSheet = new AbscenceSheet();
        $form = $this->createForm(AbscenceSheetType::class, $abscenceSheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abscenceSheet);
            $entityManager->flush();

            return $this->redirectToRoute('admin_abscence_sheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abscence_sheet/new.html.twig', [
            'abscence_sheet' => $abscenceSheet,
            'form' => $form,
        ]);
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
