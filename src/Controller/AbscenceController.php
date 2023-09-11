<?php

namespace App\Controller;

use App\Entity\Abscence;
use App\Form\AbscenceType;
use App\Repository\AbscenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/abscence')]
class AbscenceController extends AbstractController
{
    #[Route('/', name: 'app_abscence_index', methods: ['GET'])]
    public function index(AbscenceRepository $abscenceRepository): Response
    {
        return $this->render('abscence/index.html.twig', [
            'abscences' => $abscenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_abscence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $abscence = new Abscence();
        $form = $this->createForm(AbscenceType::class, $abscence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abscence);
            $entityManager->flush();

            return $this->redirectToRoute('app_abscence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abscence/new.html.twig', [
            'abscence' => $abscence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abscence_show', methods: ['GET'])]
    public function show(Abscence $abscence): Response
    {
        return $this->render('abscence/show.html.twig', [
            'abscence' => $abscence,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abscence_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abscence $abscence, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbscenceType::class, $abscence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_abscence_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abscence/edit.html.twig', [
            'abscence' => $abscence,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_abscence_delete', methods: ['POST'])]
    public function delete(Request $request, Abscence $abscence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abscence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abscence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abscence_index', [], Response::HTTP_SEE_OTHER);
    }
}
