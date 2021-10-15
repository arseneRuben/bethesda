<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Form\Subscription2Type;
use App\Repository\SchoolYearRepository;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Subscription controller.
 *
 * @Route("/admin/subscriptions")
 */
class SubscriptionController extends AbstractController
{
    private $em;
    private $repo;
    private $scRepo;

    public function __construct(EntityManagerInterface $em, SubscriptionRepository $repo, SchoolYearRepository $scRepo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
    }

    /**
     * Lists all Subscription entities.
     *
     * @Route("/", name="admin_subscriptions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
       
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $subscriptions = $this->repo->findEnrollementThisYear($year);
        return $this->render('subscription/index.html.twig', compact("subscriptions"));
      
    }


    /**
     * Finds and displays a subscription entity.
     *
     * @Route("/{id}/show", name="admin_subscriptions_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Subscription $subscription)
    {
        return $this->render('subscription/show.html.twig', compact("subscription"));
    }

    /**
     * @Route("/create",name="admin_subscriptions_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $subscription = new Subscription();
    	$form = $this->createForm(SubscriptionType::class, $subscription);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($subscription);
            $this->em->flush();
            $this->addFlash('success', 'Subscription succesfully created');
            return $this->redirectToRoute('admin_subscriptions');
    	}
    	 return $this->render('subscription/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

    /**
     * Creates a new Subscription entity.
     *
     * @Route("/create", name="admin_subscriptions_create")
     * @Method("POST")
     * @Template("AppBundle:Subscription:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $subscription = new Subscription();
        $form = $this->createForm(new Subscription2Type(), $subscription, ['entityManager' => $this->getDoctrine()->getManager(),]);
        if ($form->handleRequest($request)->isValid()) {
            $student = $subscription->getStudent();
        
            if (! $student->getEnrolled()) {
                $em = $this->getDoctrine()->getManager();
                $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
               
                $student->addSubscription($subscription);
                 
                foreach ($student->getSubscriptions() as $s) { // On vérifie d'abord que parmis les inscriptions de l'élève, figure celle de l'année scolaire en cours
                    if ($s->getSchoolYear()->getId() == $year->getId()) {
                        // On vérifie ensuite que l'élève a versé au moins les inscription pour le compte de l'année scolaire en cours
                        if ($subscription->getFinanceHolder()) {
                            if ($this->situation($student, $subscription->getClassRoom())) {
                                $student->setEnrolled(true);
                                break;
                            }
                        }else{
                            $student->setEnrolled(true);
                            break;
                        }
                        return $this->redirect($this->generateUrl('admin_subscriptions'));
                    }
                }
                $subscription->setInstant(new \DateTime());
                $em->persist($subscription);
            }
           
            $em->flush();
            return $this->redirect($this->generateUrl('admin_subscriptions'));
        }
        return $this->render('subscription/edit.html.twig', array(
                    'subscription' => $subscription,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Retourne vrai ou faux selon que l'élève a déja versé les frais d'inscription pour le compte de l'année
     */
    public function situation(Student $std, ClassRoom $room)
    {
        $em = $this->getDoctrine()->getManager();
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
        $payments = $em->getRepository('AppBundle:Payment')->findAnnualPaymentsOfStudent($std, $year);
          
        $total =0;
        foreach ($payments as $p) {
            if ($p->getSchoolYear()->getId() == $year->getId()) {
                $total += $p->getAmount();
            }
        }
        $inscription =   $room->getLevel()->getInscription();
        if ($inscription==null) {
            return;
        }
  
        return ($total >= $inscription) ?  true : false;
    }

   
    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_subscriptions_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Subscription $subscription): Response
    {
        $form = $this->createForm(SubscriptionType::class, $subscription, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Subscription succesfully updated');
            return $this->redirectToRoute('admin_subscriptions');
        }
        return $this->render('subscription/edit.html.twig'	, [
            'subscription'=>$subscription,
            'form'=>$form->createView()
        ]);
    }

    
       /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_subscriptions_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Subscription $subscription , Request $request):Response
    {
       // if($this->isCsrfTokenValid('sections_deletion'.$section->getId(), $request->request->get('crsf_token') )){
            $this->em->remove($subscription);
           
            $this->em->flush();
            $this->addFlash('info', 'Subscription succesfully deleted');
    //    }
       
        return $this->redirectToRoute('admin_subscriptions');
    }

   
}
