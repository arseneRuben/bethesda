<?php

namespace App\Controller;

use App\Entity\TuitionPayment;
use App\Form\TuitionPaymentType;
use App\Repository\TuitionPaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * TuitionPayment Controller
 * 
 * @Route("/tuition-payments")
 */
class TuitionPaymentController extends AbstractController
{
    /**
     * Liste des paiements (index) : Affiche la liste des paiements de scolarité.
     * @Route("/tuition-payments", name="tuition_payment_index", methods={"GET"})
     */
    public function index(TuitionPaymentRepository $tuitionPaymentRepository): Response
    {
        $payments = $tuitionPaymentRepository->findAll();
        return $this->render('tuition_payment/index.html.twig', ['payments' => $payments]);
    }

    /**
     * Détails d'un paiement (show) : Affiche les détails d'un paiement spécifique.
     * @Route("/tuition-payments/{id}", name="tuition_payment_show", methods={"GET"})
     */
    public function show(TuitionPayment $tuitionPayment): Response
    {
        return $this->render('tuition_payment/show.html.twig', ['payment' => $tuitionPayment]);
    }

    /**
     * Création d'un paiement (create) : Affiche le formulaire de création d'un nouveau paiement.
     * Traite la soumission du formulaire pour l'ajouter à la base de données.
     * @Route("/tuition-payments/new", name="tuition_payment_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $payment = new TuitionPayment();
        $form = $this->createForm(TuitionPaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($payment);
            $entityManager->flush();

            return $this->redirectToRoute('tuition_payment_index');
        }

        return $this->render('tuition_payment/new.html.twig', ['payment' => $payment, 'form' => $form->createView()]);
    }

    /**
     * Modification d'un paiement (edit) : Affiche le formulaire de modification d'un paiement existant.
     * Traite la soumission du formulaire pour mettre à jour les informations.
     * @Route("/tuition-payments/{id}/edit", name="tuition_payment_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TuitionPayment $tuitionPayment): Response
    {
        $form = $this->createForm(TuitionPaymentType::class, $tuitionPayment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tuition_payment_index');
        }

        return $this->render('tuition_payment/edit.html.twig', ['payment' => $tuitionPayment, 'form' => $form->createView()]);
    }

    /**
     * Suppression d'un paiement (delete) : Affiche un formulaire de confirmation de suppression.
     * Traite la demande de suppression d'un paiement.
     * @Route("/tuition-payments/{id}", name="tuition_payment_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TuitionPayment $tuitionPayment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuitionPayment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tuitionPayment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tuition_payment_index');
    }
}

?>