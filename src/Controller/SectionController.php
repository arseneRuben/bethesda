<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SectionRepository;
use App\Entity\Section;
use App\Form\SectionType;

/**
 * Section controller.
 *
 * @Route("/admin/sections")
 */
class SectionController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

     /**
     * Lists all Programme entities.
     *
     * @Route("/", name="admin_sections")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(SectionRepository $repo)
    {
       
        $sections = $repo->findAll();
        
       return $this->render('section/index.html.twig', compact("sections"));
    }


   

    /**
     * @Route("/create",name="admin_sections_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $section = new Section();
    	$form = $this->createForm(SectionType::class, $section);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($section);
            $this->em->flush();
            $this->addFlash('success', 'Section succesfully created');
            return $this->redirectToRoute('admin_sections');
    	}
    	 return $this->render('section/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

   
    /**
     * Finds and displays a Section entity.
     *
     * @Route("/{id}/show", name="admin_sections_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Section $section)
    {
        
        return $this->render('section/show.html.twig', compact("section"));
    }

    /**
     * Creates a new Section entity.
     *
     * @Route("/create", name="admin_sections_create")
     * @Method("POST")
     * @Template("AppBundle:Section:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $section = new Section();
        $form = $this->createForm(new SectionType(), $section);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($section);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_sections'));
        }

        return array(
            'section' => $section,
            'form'   => $form->createView(),
        );
    }

   
    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_sections_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Section $section): Response
    {
        $form = $this->createForm(SectionType::class, $section, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Section succesfully updated');
            return $this->redirectToRoute('admin_sections');
        }
        return $this->render('section/edit.html.twig'	, [
            'section'=>$section,
            'form'=>$form->createView()
        ]);
    }

       /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_sections_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Section $section, Request $request):Response
    {
       // if($this->isCsrfTokenValid('sections_deletion'.$section->getId(), $request->request->get('crsf_token') )){
            $this->em->remove($section);
           
            $this->em->flush();
            $this->addFlash('info', 'Section succesfully deleted');
    //    }
       
        return $this->redirectToRoute('admin_sections');
    }
}
