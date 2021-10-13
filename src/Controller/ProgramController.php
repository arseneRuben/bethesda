<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProgramRepository;
use App\Entity\Program;
use App\Form\ProgramType;


/**
 * Programme controller.
 *
 * @Route("/admin/programs")
 */
class ProgramController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

     /**
     * Lists all Programme entities.
     *
     * @Route("/", name="admin_programs")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(ProgramRepository $repo)
    {
       
        $programs = $repo->findAll();
        
       return $this->render('program/index.html.twig', compact("programs"));
    }

    /**
     * Finds and displays a Programme entity.
     *
     * @Route("/{id}/show", name="admin_programs_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Program $program)
    {
        
        return $this->render('program/show.html.twig', compact("program"));
    }

  /**
     * @Route("/create",name= "admin_programs_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $program = new Program();
    	$form = $this->createForm(ProgramType::class, $program);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($program);
            $this->em->flush();
            $this->addFlash('success', 'Program succesfully created');
            return $this->redirectToRoute('admin_programs');
    	}
    	 return $this->render('program/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_programs_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Program $program): Response
    {
        $form = $this->createForm(ProgramType::class, $program, [
            'method'=> 'PUT'
        ]);

        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Program succesfully updated');
            return $this->redirectToRoute('admin_programs');
        }
        return $this->render('program/edit.html.twig'	, [
            'program'=>$program,
            'form'=>$form->createView()
        ]);
    }

    

    /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_programs_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Program $program, Request $request):Response
    {
       // if($this->isCsrfTokenValid('programs_deletion'.$program->getId(), $request->request->get('crsf_token') )){
            $this->em->remove($program);
           
            $this->em->flush();
            $this->addFlash('info', 'Program succesfully deleted');
      // }
       
        return $this->redirectToRoute('admin_programs');
    }

}
