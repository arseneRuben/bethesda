<?php

namespace App\Controller;

use App\Entity\Quater;
use App\Form\QuaterType;
use App\Repository\QuaterRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * SchoolYear controller.
 *
 * @Route("/admin/quaters")
 */
class QuaterController extends AbstractController
{
    private $em;
    private $scRepo;
    private $repo;

    public function __construct(EntityManagerInterface $em, SchoolYearRepository $scRepo,QuaterRepository $repo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
    }

   

     /**
     * Lists all Quaterme entities.
     *
     * @Route("/", name="admin_quaters")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
       
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $quaters = $this->repo->findQuaterThisYear( $year);
        
       return $this->render('quater/index.html.twig', compact("quaters"));
    }

    /**
     * Finds and displays a Quaterme entity.
     *
     * @Route("/{id}/show", name="admin_quaters_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Quater $quater)
    {
        
        return $this->render('quater/show.html.twig', compact("quater"));
    }

  /**
     * @Route("/create",name= "admin_quaters_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if(!$this->getUser()->isVerified())
        {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $schoolyear = new Quater();
    	$form = $this->createForm(QuaterType::class, $schoolyear);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($schoolyear);
            $this->em->flush();
            $this->addFlash('success', 'Quater succesfully created');
            return $this->redirectToRoute('admin_quaters');
    	}
    	 return $this->render('quater/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Quaterme entity.
     *
     * @Route("/{id}/edt", name="admin_quaters_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Quater $quater): Response
    {
        $form = $this->createForm(QuaterType::class, $quater, [
            'method' => 'GET',
         ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Quater succesfully updated');
            return $this->redirectToRoute('admin_quaters');
        }
        return $this->render('quater/edit.html.twig'	, [
            'quater'=>$quater,
            'form'=>$form->createView()
        ]);
    }

    

    /**
     * Deletes a Quater entity.
     *
     * @Route("/{id}/delete", name="admin_quaters_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     
     */
    public function delete(Request $request, Quater $q):Response
    {
      
        if($this->isCsrfTokenValid('quaters_deletion'.$q->getId(), $request->request->get('csrf_token') )){

            $this->em->remove($q);
           
            $this->em->flush();
            $this->addFlash('info', 'Quater succesfully deleted');
        }
       
        return $this->redirectToRoute('admin_quaters');
    }


    

}
