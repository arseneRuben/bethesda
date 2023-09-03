<?php

namespace App\Controller;

use App\Entity\TuitionPlan;
use App\Form\TuitionPlanType;
use App\Repository\TuitionPlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TuitionPlanController extends AbstractController
{
    /**
     * Liste des plans de paiement (index) : Affiche la liste des plans de paiement par tranche.
     * @Route("/tuition-plans", name="tuition_plan_index", methods={"GET"})
     */
    public function index(TuitionPlanRepository $tuitionPlanRepository): Response
    {
        $plans = $tuitionPlanRepository->findAll();
        return $this->render('tuition_plan/index.html.twig', ['plans' => $plans]);
    }

    /**
     * Détails d'un plan de paiement (show) : Affiche les détails d'un plan de paiement spécifique.
     * @Route("/tuition-plans/{id}", name="tuition_plan_show", methods={"GET"})
     */
    public function show(TuitionPlan $tuitionPlan): Response
    {
        return $this->render('tuition_plan/show.html.twig', ['plan' => $tuitionPlan]);
    }

    /**
     * Création d'un plan de paiement (create) : Affiche le formulaire de création d'un nouveau plan de paiement par tranche.
     * Traite la soumission du formulaire pour l'ajouter à la base de données.
     * @Route("/tuition-plans/new", name="tuition_plan_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $plan = new TuitionPlan();
        $form = $this->createForm(TuitionPlanType::class, $plan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($plan);
            $entityManager->flush();

            return $this->redirectToRoute('tuition_plan_index');
        }

        return $this->render('tuition_plan/new.html.twig', ['plan' => $plan, 'form' => $form->createView()]);
    }

    /**
     * Modification d'un plan de paiement (edit) : Affiche le formulaire de modification d'un plan de paiement existant.
     * Traite la soumission du formulaire pour mettre à jour les informations.
     * @Route("/tuition-plans/{id}/edit", name="tuition_plan_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TuitionPlan $tuitionPlan): Response
    {
        $form = $this->createForm(TuitionPlanType::class, $tuitionPlan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tuition_plan_index');
        }

        return $this->render('tuition_plan/edit.html.twig', ['plan' => $tuitionPlan, 'form' => $form->createView()]);
    }

    /**
     * Suppression d'un plan de paiement (delete) : Affiche un formulaire de confirmation de suppression.
     * Traite la demande de suppression d'un plan de paiement.
     * @Route("/tuition-plans/{id}", name="tuition_plan_delete", methods={"DELETE"})
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


?>