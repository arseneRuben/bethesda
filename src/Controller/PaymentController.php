<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use App\Repository\ClassRoomRepository;
use App\Repository\QuaterRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SchoolYearService;
use Knp\Component\Pager\PaginatorInterface;
use App\Filter\PaymentSearch;
use App\Form\Filter\PaymentSearchType;


/**
 * Course controller.
 *
 * @Route("/admin/payments")
 */
class PaymentController extends AbstractController
{
    private SchoolYearService      $schoolYearService;
    private EntityManagerInterface $em;
    private PaymentRepository      $repo;
    private ClassRoomRepository    $clRepo;
    private QuaterRepository    $qtRepo;
    private StudentRepository    $stdRepo;



    public function __construct(StudentRepository    $stdRepo,QuaterRepository $qtRepo, PaymentRepository $repo, SchoolYearService $schoolYearService, EntityManagerInterface $em,  ClassRoomRepository $clRepo)
    {
        $this->em                = $em;
        $this->repo              = $repo;
        $this->qtRepo              = $qtRepo;
        $this->clRepo            = $clRepo;
        $this->stdRepo            = $stdRepo;
        $this->schoolYearService = $schoolYearService;
    }

    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator,Request $request): Response
    {
        $search = new PaymentSearch();
        $searchForm =  $this->createForm(PaymentSearchType::class, $search);
        $searchForm->handleRequest($request);
        $year = $this->schoolYearService->sessionYearById();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $room =  intval($_GET['room']);
            $quater = intval( $_GET['quater']);
            $student =  intval($_GET['student']);
            $startDate = \DateTime::createFromFormat('Y-m-d', $_GET['startDate']); 
            $endDate = \DateTime::createFromFormat('Y-m-d', $_GET['endDate']); 

         
           if($startDate && $endDate){
             $entities = $this->repo->findPayments($year, $room, $quater, $student,$startDate,$endDate  );
           } else {
              $entities = $this->repo->findPayments($year, $room, $quater, $student );
           }


        } else {
           
            $entities = $this->repo->findBy(array( "schoolYear"=> $year), array('updatedAt' => 'ASC'));
        }

        $payments = $paginator->paginate($entities, $request->query->get('page', 1), Payment::NUM_ITEMS_PER_PAGE);

        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
            'searchForm' => $searchForm->createView()
        ]);
    }

    #[Route('/new', name: 'app_payment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $payment->setSchoolYear($this->schoolYearService->sessionYearById());
            $payment->setCode($this->schoolYearService->sessionYearById()->getCode().'_'.$payment->getStudent()->getId().'_'.date("m_d_h_i_s"));
            $entityManager->persist($payment);
            $entityManager->flush();
            return $this->redirectToRoute('admin_student_receipt', ['id' => $payment->getStudent()->getId()]);

        }

        return $this->renderForm('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_payment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_payment_delete', methods: ['POST'])]
    public function delete(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }
}
