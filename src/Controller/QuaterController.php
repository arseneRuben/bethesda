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
 * Quater controller.
 *
 * @Route("/admin/quaters")
 */
class QuaterController extends AbstractController
{
    private $em;
    private $scRepo;
    private $repo;

    public function __construct(EntityManagerInterface $em, SchoolYearRepository $scRepo, QuaterRepository $repo)
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
        $quaters = $this->repo->findQuaterThisYear($year);

        return $this->render('quater/index.html.twig', compact("quaters"));
    }

    public function uniqueness(Quater $quater = null)
    {
        $allQuaters = ($quater != null) ? $this->repo->findAllExcept($quater) : $this->repo->findAll();
        if ($quater != null) {
            if ($quater->getActivated()) {
                foreach ($allQuaters as $qt) {
                    $qt->disable();
                }
                $quater->unable();
            } else {
                if ($this->repo->countActivatedExcept($quater)[0]["count"] == 0) {
                    $this->addFlash('warning', 'You cannot deactivate all the quaters, one must be activated at a time.');
                    return $this->redirectToRoute('admin_quaters');
                }
            }
        } else {
            foreach ($allQuaters as $qt) {
                $qt->disable();
            }
        }
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
    public function create(Request $request, QuaterRepository $quaterRepository): Response
    {
        $quater = new Quater();
        $form = $this->createForm(QuaterType::class, $quater);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Désactiver tous les trimestres existants pour cette année scolaire
            if ($quater->getActivated()) {
                $allQuaters = $quaterRepository->findAll(array("schoolYear" =>  $quater->getSchoolYear()));
                foreach ($allQuaters as $quat) {
                    $quat->setActivated(false);
                    $em->persist($quat);
                }
                $quater->unable();
            }

            // Activer le trimestre créé
            $em->persist($quater);
            $em->flush();

            $this->addFlash('success', 'Quater successfully created');
            return $this->redirectToRoute('admin_quaters');
        }

        return $this->render(
            'quater/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Quaterme entity.
     *
     * @Route("/{id}/edt", name="admin_quaters_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Quater $quater, QuaterRepository $quaterRepository): Response
    {
        $form = $this->createForm(QuaterType::class, $quater, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($quater->getActivated()) {
                $allQuaters = $this->repo->findAllExcept($quater);
                foreach ($allQuaters as $year) {
                    $year->disable();
                }
                $quater->unable();
            } else {
                if ($this->repo->countActivatedExcept($quater)[0]["count"] == 0) {
                    $this->addFlash('warning', 'You cannot deactivate all the quaters, one must be activated at a time.');
                    return $this->redirectToRoute('admin_quaters');
                }
            }
            $em = $this->getDoctrine()->getManager();
            $this->em->persist($quater);
            $em->flush();
            $this->addFlash('success', 'Quater successfully updated');
            return $this->redirectToRoute('admin_quaters');
        }

        return $this->render('quater/edit.html.twig', [
            'quater' => $quater,
            'form' => $form->createView()
        ]);
    }



    /**
     * Deletes a Quater entity.
     *
     * @Route("/{id}/delete", name="admin_quaters_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     
     */
    public function delete(Request $request, Quater $q): Response
    {

        if ($this->isCsrfTokenValid('quaters_deletion' . $q->getId(), $request->request->get('csrf_token'))) {
            if ($this->repo->countActivatedExcept($q)[0]["count"] == 0) {
                $this->addFlash('warning', 'You cannot delete all quaters, one must be activated at a time.');
                return $this->redirectToRoute('admin_quaters');
            }
            $this->em->remove($q);
            $this->em->flush();
            $this->addFlash('info', 'Quater succesfully deleted');
        }

        return $this->redirectToRoute('admin_quaters');
    }
}
