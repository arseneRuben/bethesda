<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Course;
use AppBundle\Form\Type\CourseType;

/**
 * Course controller.
 *
 * @Route("/prof/courses")
 */
class CourseController extends Controller
{
    /**
     * Lists all Course entities.
     *
     * @Route("/", name="prof_courses")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Course')->findAll();
       
        return $this->render('course/index.html.twig', array(
            'entities'  => $entities,
        ));
    }

    /**
     * Finds and displays a Course entity.
     *
     * @Route("/{id}/show", name="prof_courses_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Course $course)
    {
        $deleteForm = $this->createDeleteForm($course->getId(), 'prof_courses_delete');

       return $this->render('course/show.html.twig', array(
            'course' => $course,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Course entity.
     *
     * @Route("/new", name="prof_courses_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $course = new Course();
        $form = $this->createForm(new CourseType(), $course);

       return $this->render('course/new.html.twig', array(
            'course' => $course,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Course entity.
     *
     * @Route("/create", name="prof_courses_create")
     * @Method("POST")
     * @Template("AppBundle:Course:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $course = new Course();
        $form = $this->createForm(new CourseType(), $course);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();

            return $this->redirect($this->generateUrl('prof_courses_show', array('id' => $course->getId())));
        }

        return array(
            'course' => $course,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Course entity.
     *
     * @Route("/{id}/edit", name="prof_courses_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Course $course)
    {
        $editForm = $this->createForm(new CourseType(), $course, array(
            'action' => $this->generateUrl('prof_courses_update', array('id' => $course->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($course->getId(), 'prof_courses_delete');

       return $this->render('course/edit.html.twig', array(
            'course' => $course,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Course entity.
     *
     * @Route("/{id}/update", name="prof_courses_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("AppBundle:Course:edit.html.twig")
     */
    public function updateAction(Course $course, Request $request)
    {
        $editForm = $this->createForm(new CourseType(), $course, array(
            'action' => $this->generateUrl('prof_courses_update', array('id' => $course->getId())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('prof_courses'));
        }
        $deleteForm = $this->createDeleteForm($course->getId(), 'prof_courses_delete');

        return array(
            'course' => $course,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Course entity.
     *
     * @Route("/{id}/delete", name="prof_courses_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Course $course, Request $request)
    {
        $form = $this->createDeleteForm($course->getId(), 'prof_courses_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($course);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('prof_courses'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
