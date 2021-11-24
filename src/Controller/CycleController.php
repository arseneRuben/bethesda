<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CycleRepository;
use App\Entity\Cycle;
use App\Form\CycleType;

/**
 * Cycle controller.
 *
 * @Route("/admin/cycles")
 */
class CycleController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

     /**
     * Lists all Programme entities.
     *
     * @Route("/", name="admin_cycles")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(CycleRepository $repo)
    {
       
        $cycles = $repo->findAll();
        
       return $this->render('cycle/index.html.twig', compact("cycles"));
    }


   

    /**
     * @Route("/create",name="admin_cycles_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $cycle = new Cycle();
    	$form = $this->createForm(CycleType::class, $cycle);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($cycle);
            $this->em->flush();
            $this->addFlash('success', 'Cycle succesfully created');
            return $this->redirectToRoute('admin_cycles');
    	}
    	 return $this->render('cycle/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

   
    /**
     * Finds and displays a Cycle entity.
     *
     * @Route("/{id}/show", name="admin_cycles_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Cycle $cycle)
    {
        
        return $this->render('cycle/show.html.twig', compact("cycle"));
    }

    /**
     * Creates a new Cycle entity.
     *
     * @Route("/create", name="admin_cycles_create")
     * @Method("POST")
     * @Template("AppBundle:Cycle:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $cycle = new Cycle();
        $form = $this->createForm(new CycleType(), $cycle);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cycle);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_cycles'));
        }

        return array(
            'cycle' => $cycle,
            'form'   => $form->createView(),
        );
    }

   
    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_cycles_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Cycle $cycle): Response
    {
        $form = $this->createForm(CycleType::class, $cycle, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Cycle succesfully updated');
            return $this->redirectToRoute('admin_cycles');
        }
        return $this->render('cycle/edit.html.twig'	, [
            'cycle'=>$cycle,
            'form'=>$form->createView()
        ]);
    }

     /**
     * Deletes a Cycle entity.
     *
     * @Route("/{id}/delete", name="admin_cycles_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Cycle $cycle, Request $request):Response
    {
       // if($this->isCsrfTokenValid('cycles_deletion'.$cycle->getId(), $request->request->get('crsf_token') )){
            $this->em->remove($cycle);
           
            $this->em->flush();
            $this->addFlash('info', 'Cycle succesfully deleted');
    //    }
       
        return $this->redirectToRoute('admin_cycles');
    }
}
