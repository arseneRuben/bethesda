<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaymentRepository;

class PaymentController extends AbstractController
{
    /**
     * @Route("admin/payments" , name="admin_payments")
     */
    public function index(PaymentRepository $paymentRepository): Response
    {
        // Récupérez tous les paiements à partir du repository
        $payments = $paymentRepository->findAll();

        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }
}
