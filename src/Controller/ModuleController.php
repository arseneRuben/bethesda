<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ModuleRepository;
use App\Entity\Module;
use App\Form\ModuleType;

/**
 * Module controller.
 *
 * @Route("/admin/modules")
 */
class ModuleController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

     /**
     * Lists all Programme entities.
     *
     * @Route("/", name="admin_modules")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(ModuleRepository $repo)
    {
       
        $modules = $repo->findAll();
        
       return $this->render('module/index.html.twig', compact("modules"));
    }


   

    /**
     * @Route("/create",name="admin_modules_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $module = new Module();
    	$form = $this->createForm(ModuleType::class, $module);
    	$form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->persist($module);
            $this->em->flush();
            $this->addFlash('success', 'Module succesfully created');
            return $this->redirectToRoute('admin_modules');
    	}
    	 return $this->render('module/new.html.twig'
    	 	, ['form'=>$form->createView()]
        );
    }

   
    /**
     * Finds and displays a Module entity.
     *
     * @Route("/{id}/show", name="admin_modules_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Module $module)
    {
        
        return $this->render('module/show.html.twig', compact("module"));
    }

    /**
     * Creates a new Module entity.
     *
     * @Route("/create", name="admin_modules_create")
     * @Method("POST")
     * @Template("AppBundle:Module:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $module = new Module();
        $form = $this->createForm(new ModuleType(), $module);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($module);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_modules'));
        }

        return array(
            'module' => $module,
            'form'   => $form->createView(),
        );
    }

   
    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_modules_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Module $module): Response
    {
        $form = $this->createForm(ModuleType::class, $module, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Module succesfully updated');
            return $this->redirectToRoute('admin_modules');
        }
        return $this->render('module/edit.html.twig'	, [
            'module'=>$module,
            'form'=>$form->createView()
        ]);
    }

       /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_modules_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Module $module, Request $request):Response
    {
        if($this->isCsrfTokenValid('modules_deletion'.$module->getId(), $request->request->get('csrf_token') )){
            $this->em->remove($module);
           
            $this->em->flush();
            $this->addFlash('info', 'Module succesfully deleted');
        }
       
        return $this->redirectToRoute('admin_modules');
    }
}
