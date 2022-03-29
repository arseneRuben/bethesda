<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Sequence;
use App\Form\SequenceType;
use App\Repository\SequenceRepository;
use App\Repository\SchoolYearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * SchoolYear controller.
 *
 * @Route("/admin/sequences")
 */
class SequenceController extends AbstractController
{
    private $em;
    private $scRepo;
    private $repo;

    public function __construct(EntityManagerInterface $em, SchoolYearRepository $scRepo, SequenceRepository $repo)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->scRepo = $scRepo;
    }

    /**
     * Lists all Sequenceme entities.
     *
     * @Route("/", name="admin_sequences")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {

        $year = $this->scRepo->findOneBy(array("activated" => true));
        $sequences = $this->repo->findSequenceThisYear($year);

        return $this->render('sequence/index.html.twig', compact("sequences"));
    }

    /**
     * Finds and displays a Sequenceme entity.
     *
     * @Route("/{id}/show", name="admin_sequences_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Sequence $sequence, Request $request, PaginatorInterface $paginator)
    {
        $evaluations = $paginator->paginate($sequence->getEvaluations(), $request->query->get('page', 1), Evaluation::NUM_ITEMS_PER_PAGE);
        $evaluations->setCustomParameters([
            'position' => 'centered',
            'size' => 'large',
            'rounded' => true,
        ]);
        return $this->render('sequence/show.html.twig', ['pagination' => $evaluations, 'sequence' => $sequence]);
    }

    /**
     * @Route("/create",name= "admin_sequences_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $schoolyear = new Sequence();
        $form = $this->createForm(SequenceType::class, $schoolyear);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($schoolyear);
            $this->em->flush();
            $this->addFlash('success', 'Sequence succesfully created');
            return $this->redirectToRoute('admin_sequences');
        }
        return $this->render(
            'sequence/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing Sequenceme entity.
     *
     * @Route("/{id}/edt", name="admin_sequences_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Sequence $sequence): Response
    {
        $form = $this->createForm(SequenceType::class, $sequence, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Sequence succesfully updated');
            return $this->redirectToRoute('admin_sequences');
        }
        return $this->render('sequence/edit.html.twig', [
            'sequence' => $sequence,
            'form' => $form->createView()
        ]);
    }



    /**
     * Deletes a Sequence entity.
     *
     * @Route("/{id}/delete", name="admin_sequences_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     
     */
    public function delete(Sequence $seq, Request $request): Response
    {

        if ($this->isCsrfTokenValid('sequences_deletion' . $seq->getId(), $request->request->get('csrf_token'))) {
            $this->em->remove($seq);

            $this->em->flush();
            $this->addFlash('info', 'Sequence succesfully deleted');
        }

        return $this->redirectToRoute('admin_sequences');
    }
}
