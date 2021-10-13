<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Form\Type\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * User controller.
 *
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:User')->findAll();
        
        return $this->render('user/index.html.twig', array(
            'entities'  => $entities,
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="admin_users_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(User $user)
    {
        return $this->render('account/show.html.twig', compact("user"));
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="admin_users_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);

        return array(
            'user' => $user,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/create", name="admin_users_create")
     * @Method("POST")
     * @Template("AppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        
        $form = $this->createForm(new UserType(), $user);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setAvatar($_POST['avatar']);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
        }

        return array(
            'user' => $user,
            'form'   => $form->createView(),
        );
    }

     /**
     * Displays a form to  an existing User entity.
     *
     * @Route("/{id}/pdf", name="admin_users_pdf", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function presentAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));

        return $this->render('user/present.html.twig', array(
            'user' => $user,
            'year' => $year,
        ));
    }

    /**
     * Displays a form to  an existing User entity.
     *
     * @Route("/{id}/", name="admin_users_", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template("user/.html.twig")
     */
    public function Action(User $user)
    {
        $Form = $this->createForm(new UserFormType(), $user, array(
            'action' => $this->generateUrl('admin_users_update', array('id' => $user->getId())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($user->getId(), 'admin_users_delete');

        return $this->render('user/.html.twig', array(
            'user' => $user,
            '_form'   => $Form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
      
    }

    /**
     * s an existing User entity.
     *
     * @Route("/{id}/update", name="admin_users_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("AppBundle:User:.html.twig")
     */
    public function updateAction(User $user, Request $request)
    {
        $Form = $this->createForm(new RegistrationType(), $user, array(
            'action' => $this->generateUrl('admin_users_update', array('id' => $user->getId())),
            'method' => 'PUT',
        ));
        if ($Form->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
        }
        return $this->redirect($this->generateUrl('admin_users_show', array('id' => $user->getId())));
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}/delete", name="admin_users_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(User $user, Request $request)
    {
        $form = $this->createDeleteForm($user->getId(), 'admin_users_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin_users'));
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
