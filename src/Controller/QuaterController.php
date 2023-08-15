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
    $schoolyear = new Quater();
    $form = $this->createForm(QuaterType::class, $schoolyear);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        
        // Désactiver tous les trimestres existants pour cette année scolaire
        $allQuaters = $quaterRepository->findAll();
        foreach ($allQuaters as $quater) {
            $quater->setActivated(false);
            $em->persist($quater);
        }
        
        // Activer le trimestre créé
        $schoolyear->setActivated(true);
        $em->persist($schoolyear);
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
        $em = $this->getDoctrine()->getManager();
        
        if ($quater->getActivated()) {
            $allQuaters = $quaterRepository->findAll();
            foreach ($allQuaters as $quat) {
                if (($quat->getId() != $quater->getId()) && ($quat->getActivated())) {
                    $quat->setActivated(false);
                    $em->persist($quat);
                }
            }
        }
        
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

            $this->em->remove($q);

            $this->em->flush();
            $this->addFlash('info', 'Quater succesfully deleted');
        }

        return $this->redirectToRoute('admin_quaters');
    }
}
