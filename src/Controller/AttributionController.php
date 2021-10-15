<?php

namespace App\Controller;

use App\Entity\Attribution;
use App\Form\AttributionType;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AttributionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Attribution controller.
 *
 * @Route("/admin_attributions")
 */
class AttributionController extends Controller {

    private $em;
    private $repo;
    private $scRepo;

    public function __construct(EntityManagerInterface $em, AttributionRepository $repo, SchoolYearRepository $scRepo)
    {
        $this->em = $em;
        $this->repo= $repo;
        $this->scRepo = $scRepo;
    }
    /**
     * Lists all Attribution entities.
     *
     * @Route("/", name="admin_attributions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $year = $this->scRepo->findOneBy(array("activated" => true));
        $entities = $this->repo->findAllThisYear($year);

        return $this->render('attribution/index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Attribution entity.
     *
     * @Route("/{id}/show", name="admin_attributions_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Attribution $attribution) {
        $deleteForm = $this->createDeleteForm($attribution->getId(), 'admin_attributions_delete');

        return $this->render('attribution/show.html.twig', array(
                    'attribution' => $attribution,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Attribution entity.
     *
     * @Route("/undo", name="admin_attributions_undo")
     * @Method("GET")
     * @Template()
     */
    public function undoAction() {
          
         $year = $this->scRepo->findOneBy(array("activated" => true));
        $entities = $repo->findAllThisYear($year);   
        foreach ($entities as $attribution){
             $attribution->getCourse()->setAttributed(FALSE);
                $this->em->remove($attribution);
        }
         $em->flush();
         return $this->redirect($this->generateUrl('admin_attributions'));
    }

  /**
     * Creates a new Section entity.
     *
     * @Route("/create", name="admin_attributions_new")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $attribution = new Attribution();
        $form = $this->createForm(AttributionType::class, $attribution);
        $form->handleRequest($request);
    	if($form->isSubmitted() && $form->isValid()) {
            $year = $this->scRepo->findOneBy(array("activated" => true));
            $attribution->setSchoolYear($year);
            $this->em->persist($attribution);
            $this->em->flush();

            return $this->redirect($this->generateUrl('admin_attributions'));
        }

        return $this->render('attribution/new.html.twig'
        , ['form'=>$form->createView()]
        );
    }


   

    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_attributions_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request,Attribution $attribution): Response
    {
        $form = $this->createForm(AttributionType::class, $attribution, [
            'method'=> 'PUT'
        ]);
        $form->handleRequest($request);
     
        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success', 'Attribution succesfully updated');
            return $this->redirectToRoute('admin_attributions');
        }
        return $this->render('attribution/edit.html.twig'	, [
            'attribution'=>$attribution,
            'form'=>$form->createView()
        ]);
    }

   

    
       /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_attributions_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Attribution $attribution , Request $request):Response
    {
       // if($this->isCsrfTokenValid('sections_deletion'.$section->getId(), $request->request->get('crsf_token') )){
            $this->em->remove($attribution);
           
            $this->em->flush();
            $this->addFlash('info', 'Attribution succesfully deleted');
    //    }
       
        return $this->redirectToRoute('admin_attributions');
    }
  

}
