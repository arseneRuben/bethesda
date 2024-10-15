<?php

namespace App\Controller;

use App\Entity\Student;
use App\Entity\ClassRoom;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
//use App\Form\Subscription2Type;
use App\Form\Subscription2Type;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\SchoolYearService;

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
    private SessionInterface $session;
    private SchoolYearService $schoolYearService;


    public function __construct(SchoolYearService $schoolYearService,EntityManagerInterface $em, SubscriptionRepository $repo, SchoolYearRepository $scRepo, SessionInterface $session)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->session = $session;
        $this->schoolYearService = $schoolYearService;

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
        $year = $this->schoolYearService->sessionYearById();
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
        if ($form->isSubmitted() && $form->isValid()) {
            $student = $subscription->getStudent();
            $student->addSubscription($subscription);
            $student->setEnrolled(true);
            // $subscription->setInstant(new \DateTime());
            $this->em->persist($subscription);



            $this->em->persist($subscription);
            $this->em->flush();
            $this->addFlash('success', 'Subscription succesfully created');
            return $this->redirectToRoute('admin_subscriptions');
        }
        return $this->render(
            'subscription/new.html.twig',
            ['form' => $form->createView()]
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
        $form = $this->createForm(new SubscriptionType(), $subscription, ['entityManager' => $this->getDoctrine()->getManager(),]);
        if ($form->isSubmitted() && $form->isValid()) {
            $student = $subscription->getStudent();


            $student->addSubscription($subscription);

            $student->setEnrolled(true);

            $subscription->setInstant(new \DateTime());
            $this->em->persist($subscription);
            $this->em->persist($student);

            $this->em->flush();
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
        $year = $this->schoolYearService->sessionYearById();
        $payments = $em->getRepository('AppBundle:Payment')->findAnnualPaymentsOfStudent($std, $year);

        $total = 0;
        foreach ($payments as $p) {
            if ($p->getSchoolYear()->getId() == $year->getId()) {
                $total += $p->getAmount();
            }
        }
        $inscription =   $room->getLevel()->getInscription();
        if ($inscription == null) {
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
    public function edit(Request $request, Subscription $subscription): Response
    {
        $form = $this->createForm(Subscription2Type::class, $subscription, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Subscription succesfully updated');
            return $this->redirectToRoute('admin_subscriptions');
        }
        return $this->render('subscription/edit.html.twig', [
            'subscription' => $subscription,
            'form' => $form->createView()
        ]);
    }


    /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_subscriptions_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Subscription $subscription, Request $request): Response
    {
        if ($this->isCsrfTokenValid('subscriptions_deletion' . $subscription->getId(), $request->request->get('csrf_token'))) {
            $this->em->remove($subscription);

            $this->em->flush();
            $this->addFlash('info', 'Subscription succesfully deleted');
        }

        return $this->redirectToRoute('admin_subscriptions');
    }
}
