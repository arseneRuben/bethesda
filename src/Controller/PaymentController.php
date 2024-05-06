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
use App\Repository\SubscriptionRepository;


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
    private SubscriptionRepository $subRepo;



    public function __construct(SubscriptionRepository    $subRepo,StudentRepository    $stdRepo,QuaterRepository $qtRepo, PaymentRepository $repo, SchoolYearService $schoolYearService, EntityManagerInterface $em,  ClassRoomRepository $clRepo)
    {
        $this->em                = $em;
        $this->repo              = $repo;
        $this->qtRepo              = $qtRepo;
        $this->clRepo            = $clRepo;
        $this->stdRepo            = $stdRepo;
        $this->subRepo            = $subRepo;
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
        $year = $this->schoolYearService->sessionYearById();

        if ($form->isSubmitted() && $form->isValid()) {
            $sub = $this->subRepo->findOneBy(array("student" => $payment->getStudent(), "schoolYear" => $year));
            if($sub != null){
                if($payment->isSubscription() ){
                    if( $payment->getAmount() > $year->getRate()) {
                        $subscriptionPayment = new Payment();
                        $subscriptionPayment->setAmount($year->getRate());
                        $subscriptionPayment->setStudent($payment->getStudent());
                        $subscriptionPayment->setSchoolYear($this->schoolYearService->sessionYearById());
                        $subscriptionPayment->setCode($this->schoolYearService->sessionYearById()->getCode().'_sub_'.$payment->getStudent()->getId());
                        $entityManager->persist($subscriptionPayment);
                        $payment->setAmount($payment->getAmount()-$year->getRate());
                    } else {
                        $this->addFlash('warning', 'The amount indicated is not enough to cover the registration');
                    }
                } 
                $payment->setSchoolYear($this->schoolYearService->sessionYearById());
                $payment->setCode($this->schoolYearService->sessionYearById()->getCode().'_'.$payment->getStudent()->getId().'_'.date("m_d_h_i_s"));
                $entityManager->persist($payment);
                $entityManager->flush();
                return $this->redirectToRoute('admin_student_receipt', ['id' => $payment->getStudent()->getId()]);
            } else {
                $this->addFlash('warning', 'This student is not yet registered. Please first assign him to a class ');
            }
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
