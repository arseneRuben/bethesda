<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use App\Entity\PaymentPlan;
use App\Form\PaymentPlanType;
use App\Repository\ClassRoomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
        $paymentPlans = $paymentPlanRepository->findAll();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $rooms = $this->clRepo->findAll(array('id' => 'ASC'));
      
        return $this->render('paymentPlan/index.html.twig', [
            'paymentPlans' => $paymentPlans,
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
        
        foreach ($request->request->all() as $key => $value) {
         
            $segments = explode("_", $key);
            $nbSegments = count($segments);
           
            if ($nbSegments >= 2) {
                $roomId = $segments[$nbSegments - 1];
                $tranche = $segments[$nbSegments - 2];
                $paymentPlan->setClassRoom($this->clRepo->findOneBy(array('id' => $roomId)));
              
            }    
          /*  if ($key != "submit") {
                $paymentPlan->setClassRoom($this->clRepo->findOneBy(array('id' => $key)));
                $paymentPlan->setSchoolYear($this->scRepo->findOneBy(array('activated' => true)));
                $paymentPlan->setAmount($value);
                $this->em->persist($paymentPlan);
                $this->em->flush();
            }*/
        }
       

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($paymentPlan);
            $entityManager->flush();

            // Redirigez l'utilisateur vers une autre page, affichez un message de confirmation, etc.
            return $this->redirectToRoute('admin_paymentPlans');
        

       
    }
}
