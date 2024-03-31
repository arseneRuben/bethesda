<?php

namespace App\Controller;
use App\Entity\MainTeacher;
use App\Entity\Attribution;
use App\Form\AttributionType;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AttributionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\SchoolYearService;

/**
 * Attribution controller.
 *
 * @Route("/admin/attributions")
 */
class AttributionController extends AbstractController
{

    private $em;
    private $repo;
    private $scRepo;
    private SessionInterface $session;
    private SchoolYearService $schoolYearService;


    public function __construct(SchoolYearService $schoolYearService,EntityManagerInterface $em, AttributionRepository $repo, SchoolYearRepository $scRepo, SessionInterface $session)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
        $this->session = $session;
        $this->schoolYearService = $schoolYearService;

    }
    /**
     * Lists all Attribution entities.
     *
     * @Route("/", name="admin_attributions")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $year = $this->schoolYearService->sessionYearById();
        $entities = $this->repo->findAllThisYear($year);
        return $this->render('attribution/index.html.twig', array(
            'entities' => $entities,
            'year' => $year,

        ));
    }


    public function setAttributionAction( )
    {
        $em = $this->getDoctrine()->getManager();
        $year = $this->schoolYearService->sessionYearByCode();
        $entities = $this->repo->findAllThisYear($year);
        foreach ($entities as $attribution) {
            if ($attribution->getCourse()->getAttributions()->contains($attribution)) {
                $attribution->getCourse()->setAttributed(True);
                $em->persist($attribution->getCourse());
            }
        }
        $em->flush();
    }
    /**
     * Finds and displays a Attribution entity.
     *
     * @Route("/{id}/show", name="admin_attributions_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Attribution $attribution)
    {
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
    public function undoAction()
    {
        $entities = $this->repo->findAllThisYear( $this->schoolYearService->sessionYearByCode());
        foreach ($entities as $attribution) {
            $attribution->getCourse()->setAttributed(FALSE);
            $this->em->remove($attribution);
        }
        $this->em->flush();
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
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $attribution = new Attribution();
        $form = $this->createForm(AttributionType::class, $attribution);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $year = $this->schoolYearService->sessionYearById();
            $attribution->setSchoolYear($year);
            $attribution->getTeacher()->addAttribution($attribution);
            $attribution->getCourse()->addAttribution($attribution);
            if($attribution->isHeadTeacher()){
                $mainTeacher = new MainTeacher();
                $mainTeacher->setTeacher($attribution->getTeacher());
                $mainTeacher->setClassRoom($attribution->getCourse()->getModule()->getRoom());
                $mainTeacher->setSchoolYear($year);
                $attribution->getCourse()->getModule()->getRoom()->addMainTeacher($mainTeacher);
                $this->em->persist($mainTeacher);
            }
            $this->em->persist($attribution);
            $this->em->flush();
            return $this->redirect($this->generateUrl('admin_attributions'));
        }

        return $this->render(
            'attribution/new.html.twig',
            ['form' => $form->createView()]
        );
    }




    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_attributions_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Attribution $attribution): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(AttributionType::class, $attribution, [
            'method' => 'PUT'
        ]);
        $old_attribution =  $attribution;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($old_attribution->getTeacher()->getId() !== $attribution->getTeacher()->getId()) {
                $old_attribution->getTeacher()->removeAttribution($old_attribution);
                $attribution->getTeacher()->addAttribution($attribution);
                $this->em->persist($attribution->getTeacher());
            }
            if ($old_attribution->getCourse()->getId() !== $attribution->getCourse()->getId()) {
                $old_attribution->getCourse()->setAttributed(false);
                $attribution->getCourse()->setAttributed(true);
                $this->em->persist($attribution->getCourse());
            }


            $this->em->persist($attribution);


            $this->em->flush();
            $this->addFlash('success', 'Attribution succesfully updated');

            return $this->redirectToRoute('admin_attributions');
        }
        return $this->render('attribution/edit.html.twig', [
            'attribution' => $attribution,
            'form' => $form->createView()
        ]);
    }




    /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_attributions_delete", requirements={"id"="\d+"}, methods={"GET","DELETE"})
     */
    public function delete(Attribution $attribution, Request $request): Response
    {
        // if($this->isCsrfTokenValid('sections_deletion'.$section->getId(), $request->request->get('crsf_token') )){
        $attribution->getCourse()->removeAttribution($attribution);
        $attribution->getTeacher()->removeAttribution($attribution);
        $attribution->getCourse()->setAttributed(false);
        $this->em->persist($attribution->getCourse());
        $this->em->persist($attribution->getTeacher());
        $this->em->remove($attribution);

        $this->em->flush();
        $this->addFlash('info', 'Attribution succesfully deleted');
        //    }

        return $this->redirectToRoute('admin_attributions');
    }
}
