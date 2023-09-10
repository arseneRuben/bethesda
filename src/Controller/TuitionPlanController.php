<?php 

// src/Controller/TuitionPlanController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TuitionPlan;
use App\Form\TuitionPlanType;

class TuitionPlanController extends AbstractController
{
    /**
     * @Route("/tuition-plans", name="tuition_plan_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tuitionPlans = $this->getDoctrine()->getRepository(TuitionPlan::class)->findAll();

        return $this->render('tuition_plan/index.html.twig', ['tuitionPlans' => $tuitionPlans]);
    }

    /**
     * @Route("/tuition-plan/new", name="tuition_plan_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $tuitionPlan = new TuitionPlan();
        $form = $this->createForm(TuitionPlanType::class, $tuitionPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tuitionPlan);
            $entityManager->flush();

            return $this->redirectToRoute('tuition_plan_index');
        }

        return $this->render('tuition_plan/new.html.twig', [
            'tuitionPlan' => $tuitionPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tuition-plan/{id}", name="tuition_plan_show", methods={"GET"})
     */
    public function show(TuitionPlan $tuitionPlan): Response
    {
        return $this->render('tuition_plan/show.html.twig', ['tuitionPlan' => $tuitionPlan]);
    }

    /**
     * @Route("/tuition-plan/{id}/edit", name="tuition_plan_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, TuitionPlan $tuitionPlan): Response
    {
        $form = $this->createForm(TuitionPlanType::class, $tuitionPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tuition_plan_index');
        }

        return $this->render('tuition_plan/edit.html.twig', [
            'tuitionPlan' => $tuitionPlan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/tuition-plan/{id}", name="tuition_plan_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TuitionPlan $tuitionPlan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuitionPlan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tuitionPlan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tuition_plan_index');
    }
}
