<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use App\Entity\PaymentPlan;
use App\Entity\Installment;
use App\Form\PaymentPlanType;
use App\Repository\ClassRoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Repository\PaymentPlanRepository;
use App\Repository\PaymentRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SchoolYearService;

/**
 * ClassRoom controller.
 *
 * @Route("/admin/paymentPlans")
 */
class PaymentPlanController extends AbstractController
{
    private $em;
    private $clRepo;
    private $scRepo;
    private $repo;
    private SchoolYearService $schoolYearService;

    public function __construct(
        EntityManagerInterface $em,
        PaymentPlanRepository $repo,
        SchoolYearRepository $scRepo,
        ClassRoomRepository $clRepo,
        SchoolYearService $schoolYearService
    ) {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->clRepo = $clRepo;
        $this->schoolYearService = $schoolYearService;

    }
    /**
     * @Route("/", name="admin_paymentPlans")
     */
    public function index(PaymentPlanRepository $paymentPlanRepository): Response
    {
        // Utilisez le PaymentRepository pour récupérer tous les paiements
       
        $year = $this->schoolYearService->sessionYearById();
        $rooms = $this->clRepo->findAll(array('id' => 'ASC'));
        
        return $this->render('paymentPlan/index.html.twig', [
            'year' => $year,
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("/create", name="admin_paymentPlans_new", methods={"GET","POST"})
     */
    public function newPaymentPlan(Request $request): Response
    {
        // Créez une nouvelle instance de PaymentPlan
        $paymentPlan = new PaymentPlan();
        $paymentPlan->setSchoolYear($this->schoolYearService->sessionYearById());
        $installment=null;
        foreach ($request->request->all() as $key => $value) {
            if(strstr($key, 'tranche_class')){
                    $installment = new Installment();
                    $segments = explode("_", $key);
                    $nbSegments = count($segments);
                    $roomId = $segments[$nbSegments - 1];
                    $order = $segments[$nbSegments - 2];
                    $installment->setPaymentPlan($paymentPlan);
                    $installment->setClassRoom($this->clRepo->findOneBy(array('id' => $roomId)));
                    $installment->setRank($order);
                    $installment->setAmount(intval($request->request->get($key)));
                    $this->em->persist($installment);
            } else if(strstr($key, 'deadline_class')) {
                    if($installment!=null)  {
                        $installment->setDeadline(new \DateTime($request->request->get($key)));
                        $paymentPlan->addInstallment($installment);
                        $this->em->persist($installment);
                    } else {
                        continue;
                    }
             }
             $paymentPlan->addInstallment($installment);
             
        }
        $this->addFlash('info', 'Payment plan succesfully created');
        $this->em->persist($paymentPlan);
        $this->em->flush();
        return $this->redirectToRoute('admin_paymentPlans');
       
    }

    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_paymentPlans_edit", requirements={"id"="\d+"}, methods={"GET", "PUT"})
     * @Template()
     */
    public function edit(Request $request): Response
    {
        $paymentPlan = $this->repo->findOneBy(array("id" => $request->attributes->get('id')));
        $rooms = $this->clRepo->findAll(array('id' => 'ASC'));
        $installments = array(); 
        foreach ($paymentPlan->getInstallments() as $installment) {
            $installments[$installment->getClassRoom()->getId()][$installment->getRank()]=$installment;
        }
        $form = $this->createForm(PaymentPlanType::class, $paymentPlan, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($request->request->all() as $key => $value) {
                if(strstr($key, 'tranche_class')){
                        $segments = explode("_", $key);
                        $nbSegments = count($segments);
                        $roomId = $segments[$nbSegments - 1];
                        $order = $segments[$nbSegments - 2];
                        $installments[$roomId][$order]->setAmount(intval($request->request->get($key)));
                        $this->em->persist($installment);
                } else if(strstr($key, 'deadline_class')) {
                        if($installment!=null)  {
                            $installments[$roomId][$order]->setDeadline(new \DateTime($request->request->get($key)));
                        } else {
                            continue;
                        }
                 }
                 $this->em->persist($installment);
                 
            }
            $this->addFlash('success', 'Payment plan succesfully updated');
            $this->em->flush();
            return $this->redirect($this->generateUrl('admin_paymentPlans'));
        }
        return $this->render('paymentPlan/edit.html.twig', [
            'paymentPlan' => $paymentPlan,
            'rooms' => $rooms,
            'installments' => $installments,
            'form' => $form->createView()
        ]);
    }
}
