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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * AbscenceSheet controller.
 *
 * @Route("/admin/abscence")
 */
class AbscenceController extends AbstractController
{
    /**
     * Lists all Course entities.
     *
     * @Route("/", name="admin_abscences")
     * @Method("GET")
     * @Template()
     */
    public function index(AbscenceRepository $abscenceRepository): Response
    {
        return $this->render('abscence/index.html.twig', [
            'abscences' => $abscenceRepository->findAll(),
        ]);
    }

    /**
     * Creates a new Course entity.
     *
     * @Route("/create", name="admin_abscence_create")
     * @Method("POST")
    
     */
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

    /**
     * Finds and displays a Course entity.
     *
     * @Route("/{id}/show", name="admin_abscences_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function show(Abscence $abscence): Response
    {
        return $this->render('abscence/show.html.twig', [
            'abscence' => $abscence,
        ]);
    }

    /**
     * Displays a form to edit an existing Abscence entity.
     *
     * @Route("/{id}/edit", name="admin_abscences_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
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
        if ($this->isCsrfTokenValid('delete' . $abscence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($abscence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abscence_index', [], Response::HTTP_SEE_OTHER);
    }
}
