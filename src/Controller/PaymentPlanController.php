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
    public function __construct(
        EntityManagerInterface $em,
        PaymentPlanRepository $repo,
        SchoolYearRepository $scRepo,
        ClassRoomRepository $clRepo,
    ) {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->clRepo = $clRepo;
    }
    /**
     * @Route("/", name="admin_paymentPlans")
     */
    public function index(PaymentPlanRepository $paymentPlanRepository): Response
    {
        // Utilisez le PaymentRepository pour récupérer tous les paiements
       
        $year = $this->scRepo->findOneBy(array("activated" => true));
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
        $paymentPlan->setSchoolYear($this->scRepo->findOneBy(array('activated' => true)));
        $installment=null;
        foreach ($request->request->all() as $key => $value) {
            if(strstr($key, 'tranche_class')){
                    $installment = new Installment();
                    $segments = explode("_", $key);
                    $nbSegments = count($segments);
                    $roomId = $segments[$nbSegments - 1];
                    $order = $segments[$nbSegments - 2];
                    $installment->setClassRoom($this->clRepo->findOneBy(array('id' => $roomId)));
                    $installment->setRank($order);
                    $installment->setAmount(intval($request->request->get($key)));
                    $installment->setPaymentPlan($paymentPlan);
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
        }
        $this->addFlash('info', 'Payment plan succesfully created');
        $this->em->persist($paymentPlan);
        $this->em->flush();
        return $this->redirectToRoute('admin_paymentPlans');
       
    }

       /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_paymentPlans_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, PaymentPlan $paymentPlan): Response
    {
        dd($paymentPlan);
        $form = $this->createForm(PaymentPlanType::class, $paymentPlan, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);
        return $this->render('program/edit.html.twig', [
            'paymentPlan' => $paymentPlan,
            'form' => $form->createView()
        ]);
    }
}
