<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use App\Entity\PaymentPlan;
use App\Entity\Installment;
use App\Entity\ClassRoom;
use App\Form\PaymentPlanType;
use App\Repository\ClassRoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Repository\PaymentPlanRepository;
use App\Repository\PaymentRepository;
use App\Repository\InstallmentRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SchoolYearService;
use Knp\Snappy\Pdf;

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
        InstallmentRepository $instRepo,
        SchoolYearService $schoolYearService
    ) {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->clRepo = $clRepo;
        $this->instRepo = $instRepo;
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
                    $installment->setRanking($order);
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
            $installments[$installment->getClassRoom()->getId()][$installment->getRanking()]=$installment;
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
                        $installments[$roomId][$order]->setDeadline(new \DateTime($request->request->get($key)));
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

    
    #[Route('/{id}', name: 'admin_paymentPlans_delete', methods: ['POST'])]
    public function delete(Request $request, PaymentPlan $pp, EntityManagerInterface $entityManager): Response
    {
        dd($pp);
        if ($this->isCsrfTokenValid('delete'.$pp->getId(), $request->request->get('_token'))) {
            foreach($pp->getInstallments() as $p){
                $entityManager->remove($p);
            }
            $entityManager->remove($payment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_payment_index', [], Response::HTTP_SEE_OTHER);
    }

     /**
     * Displays a pdf of schedule of payment of a class or all the scholl.
     *
     * @Route("print/{id}", name="admin_payment_plan_print", defaults={"id"=0}  )
     * @Method("GET")
     * @Template()
     */
    public function print(Pdf $pdf, int $id=0): Response
    {
        $year = $this->schoolYearService->sessionYearById();
        $rooms = $this->clRepo->findAll();
        if($id > 0){
            $rooms = $this->clRepo->findBy(array("id" => $id));
            $installments = $this->instRepo->findBy(array("paymentPlan" => $year->getPaymentPlan(), "classRoom"=>$rooms[0]));
        } else {
            $installments = $this->instRepo->findBy(array("paymentPlan" => $year->getPaymentPlan()), array( "classRoom"=>"ASC"));
        }
        $html = $this->renderView('paymentPlan/pdf.html.twig', array(
            'plan' => $year->getPaymentPlan(),
            "rooms"=>$rooms, 
            "installments" => $installments
        ));

        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="plan_scolarite_'.$year->getCode() . "_". ( count($rooms)==1 ?  $rooms[0]->getName():"") . '.pdf"'
            )
        );
    }
}
