<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Snappy\Pdf;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Form\Type\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\SchoolYearService;
use App\Repository\AttributionRepository;
use App\Repository\MainTeacherRepository;


/**
 * User controller.
 *
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    private $em;

    private SchoolYearService $schoolYearService;
    private AttributionRepository $attRepo;
    private MainTeacherRepository $mainTeacherRepo;
    private UserRepository $repo;

    public function __construct( UserRepository $repo,MainTeacherRepository $mainTeacherRepo,AttributionRepository $attRepo,SchoolYearService $schoolYearService,EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->schoolYearService = $schoolYearService;
        $this->attRepo = $attRepo;
        $this->mainTeacherRepo = $mainTeacherRepo;
        $this->repo = $repo;
    }
    /**
     * Lists all Programme entities.
     *
     * @Route("/", name="admin_users")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $users = $this->repo->findAll();

        return $this->render('user/list.html.twig', compact("users"));
    }
    /**
     * Lists all Programme entities.
     *
     * @Route("/print/", name="admin_teacher_list")
     * @Method("GET")
     * @Template()
     */
    public function listAction(Pdf $pdf)
    {
        $year = $this->schoolYearService->sessionYearById();
        $users = $this->repo->findAllOfCurrentYear($year);

        $html = $this->renderView('user/teachers.html.twig', array(
            'year' => $year,
            'users' => $users,
        ));
        return new Response(
            $pdf->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'inline; filename="teacher_list.pdf"'
            )
        );
    }




    /**
     * @Route("/create",name="admin_users_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'User succesfully created');
            return $this->redirectToRoute('admin_users');
        }
        return $this->render(
            'user/new.html.twig',
            ['form' => $form->createView()]
        );
    }


    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/show", name="app_users_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function appShowAction(User $user)
    {
        return $this->render('user/app_show.html.twig', compact("user"));
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
        $mainTeacher = $this->mainTeacherRepo->findOneBy(array("teacher"=> $user, "schoolYear"=> $this->schoolYearService->sessionYearById()));
        return $this->render('account/show.html.twig', compact("user", "mainTeacher"));
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
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_users_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserFormType::class, $user, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'User succesfully updated');
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

     /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/toggle", name="admin_users_toggle", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function toggleIsVerified(Request $request, User $user)
    {
       

       $user->toggleIsVerified();

       $entityManager = $this->getDoctrine()->getManager();
       $entityManager->persist($user);
       $entityManager->flush();
      // dd($user);
       return $this->redirectToRoute('admin_users');
        
    }

    /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_users_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(User $user, Request $request): Response
    {
        if ($this->isCsrfTokenValid('users_deletion' . $user->getId(), $request->request->get('crsf_token'))) {
            $this->em->remove($user);

            $this->em->flush();
            $this->addFlash('info', 'User succesfully deleted');
        }

        return $this->redirectToRoute('admin_users');
    }
}
