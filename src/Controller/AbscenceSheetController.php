<?php

namespace App\Controller;

use App\Entity\AbscenceSheet;
use App\Form\AbscenceSheetType;
use App\Repository\AbscenceSheetRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\SchoolYearRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private $scRepo;
    private $stdRepo;
    private $clRepo;

    public function __construct(EntityManagerInterface $em, AbscenceSheetRepository $repo, SchoolYearRepository $scRepo, StudentRepository $stdRepo, ClassRoomRepository $clRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->stdRepo = $stdRepo;
        $this->clRepo = $clRepo;
    }


    /**
     * Lists all ClassRoomme entities.
     *
     * @Route("/", name="admin_abscences_sheet_index", methods={"GET"})
     * @Template()
     */
    public function index(AbscenceSheetRepository $abscenceSheetRepository): Response
    {
        return $this->render('abscence_sheet/index.html.twig', [
            'abscence_sheets' => $abscenceSheetRepository->findAll(),
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
