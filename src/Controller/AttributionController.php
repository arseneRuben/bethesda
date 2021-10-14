<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Attribution;
use AppBundle\Form\Type\AttributionType;

/**
 * Attribution controller.
 *
 * @Route("/admin_attributions")
 */
class AttributionController extends Controller {

    /**
     * Lists all Attribution entities.
     *
     * @Route("/", name="admin_attributions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
        $entities = $em->getRepository('AppBundle:Attribution')->findAllThisYear($year);

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
          $em = $this->getDoctrine()->getManager();
         $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
        $entities = $em->getRepository('AppBundle:Attribution')->findAllThisYear($year);   
        foreach ($entities as $attribution){
             $attribution->getCourse()->setAttributed(FALSE);
                $em->remove($attribution);
        }
         $em->flush();
         return $this->redirect($this->generateUrl('admin_attributions'));
    }

    /**
     * Displays a form to create a new Attribution entity.
     *
     * @Route("/new", name="admin_attributions_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $attribution = new Attribution();
        $form = $this->createForm(new AttributionType(), $attribution);

        return $this->render('attribution/new.html.twig', array(
                    'attribution' => $attribution,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Attribution entity.
     *
     * @Route("/create", name="admin_attributions_create")
     * @Method("POST")
     * @Template("AppBundle:Attribution:new.html.twig")
     */
    public function createAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
        $attribution = new Attribution();
        $form = $this->createForm(new AttributionType(), $attribution);
        $attribution->setSchoolYear($year);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $course = $attribution->getCourse();
            $course->addAttribution($attribution);
            $course->setAttributed(TRUE);
            $em->persist($attribution);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_attributions'));
        }

        return array(
            'attribution' => $attribution,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Attribution entity.
     *
     * @Route("/{id}/edit", name="admin_attributions_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Attribution $attribution) {
        $editForm = $this->createForm(new AttributionType(), $attribution, array(
            'action' => $this->generateUrl('admin_attributions_update', array('id' => $attribution->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($attribution->getId(), 'admin_attributions_delete');

        return $this->render('attribution/edit.html.twig', array(
                    'attribution' => $attribution,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Attribution entity.
     *
     * @Route("/{id}/update", name="admin_attributions_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("AppBundle:Attribution:edit.html.twig")
     */
    public function updateAction(Attribution $attribution, Request $request) {
        $editForm = $this->createForm(new AttributionType(), $attribution, array(
            'action' => $this->generateUrl('admin_attributions_update', array('id' => $attribution->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_attributions'));
        }
        $deleteForm = $this->createDeleteForm($attribution->getId(), 'admin_attributions_delete');

        return array(
            'attribution' => $attribution,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Attribution entity.
     *
     * @Route("/{id}/delete", name="admin_attributions_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Attribution $attribution, Request $request) {
        $form = $this->createDeleteForm($attribution->getId(), 'admin_attributions_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $attribution->getCourse()->setAttributed(FALSE);
            $em->remove($attribution);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_attributions'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route) {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
                        ->setAction($this->generateUrl($route, array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
