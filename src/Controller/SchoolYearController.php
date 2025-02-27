<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SchoolYearRepository;
use App\Entity\SchoolYear;
use App\Form\SchoolYearType;


/**
 * SchoolYear controller.
 *
 * @Route("/admin/years")
 */
class SchoolYearController extends AbstractController
{
    private $em;
    private $scRepo;

    public function __construct(EntityManagerInterface $em, SchoolYearRepository $scRepo)
    {
        $this->scRepo = $scRepo;
        $this->em = $em;
    }

    /**
     * Lists all SchoolYearme entities.
     *
     * @Route("/", name="admin_school_years")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(SchoolYearRepository $repo)
    {

        $schoolyears = $repo->findAll();

        return $this->render('school_year/index.html.twig', compact("schoolyears"));
    }

    /**
     * Finds and displays a SchoolYearme entity.
     *
     * @Route("/{id}/show", name="admin_schoolyears_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */

    public function showAction(SchoolYear $school_year, SchoolYearRepository $schoolYearRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        return $this->render('school_year/show.html.twig', compact("school_year"));
    }

    public function uniqueness(SchoolYear $schoolyear = null)
    {
        $allSchoolYears = ($schoolyear != null) ? $this->scRepo->findAllExcept($schoolyear) : $this->scRepo->findAll();
        if ($schoolyear != null) {
            if ($schoolyear->getActivated()) {
                foreach ($allSchoolYears as $year) {
                    $year->disable();
                }
                $schoolyear->unable();
            } else {
                if ($this->scRepo->countActivatedExcept($schoolyear)[0]["count"] == 0) {
                    $this->addFlash('warning', 'You cannot deactivate all the solar years, one must be activated at a time.');
                    return $this->redirectToRoute('admin_school_years');
                }
            }
        } else {
            foreach ($allSchoolYears as $year) {
                $year->disable();
            }
        }
    }

    /**
     * @Route("/create",name= "admin_schoolyears_new", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('warning', 'You need to have a verified account!');
            return $this->redirectToRoute('app_login');
        }
        $schoolyear = new SchoolYear();
        $form = $this->createForm(SchoolYearType::class, $schoolyear);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if(($schoolyear->getStartDate() <= $schoolyear->getEndDate()) 
                && ($schoolyear->getStartDate() <= $schoolyear->getRegistrationDeadline())
                && ($schoolyear->getRegistrationDeadline() <= $schoolyear->getEndDate()))
            {
                $this->uniqueness();
                $this->em->persist($schoolyear);
                $this->em->flush();
                $this->addFlash('success', 'SchoolYear succesfully created');
                return $this->redirectToRoute('admin_school_years');
            } else {
                if($schoolyear->getStartDate() > $schoolyear->getEndDate()){
                    $this->addFlash('warning', 'The start date is greater than the end date');
                }
                if($schoolyear->getRegistrationDeadline() > $schoolyear->getEndDate()){
                    $this->addFlash('warning', 'The registration deadline occurs after the end date');
                }
                if($schoolyear->getRegistrationDeadline() < $schoolyear->getStartDate()){
                    $this->addFlash('warning', 'The deadline for registrations occurs before the end date');
                }
            }
        }
        return $this->render(
            'school_year/new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Displays a form to edit an existing SchoolYearme entity.
     *
     * @Route("/{id}/edt", name="admin_schoolyears_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, SchoolYear $schoolyear): Response
    {
        $form = $this->createForm(SchoolYearType::class, $schoolyear, [
            'method' => 'GET',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->uniqueness($schoolyear);
            $this->em->persist($schoolyear);
            $this->em->flush();
            $this->addFlash('success', 'SchoolYear succesfully updated');
            return $this->redirectToRoute('admin_school_years');
        }
        return $this->render('school_year/edit.html.twig', [
            'schoolyear' => $schoolyear,
            'form' => $form->createView()
        ]);
    }



    /**
     * Deletes a SchoolYearme entity.
     *
     * @Route("/{id}/delete", name="admin_schoolyears_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(SchoolYear $schoolyear, Request $request): Response
    {
        if ($this->isCsrfTokenValid('school_years_deletion' . $schoolyear->getId(), $request->request->get('csrf_token'))) {
            if ($this->scRepo->countActivatedExcept($schoolyear)[0]["count"] == 0) {
                $this->addFlash('warning', 'You cannot deactivate all the solar years, one must be activated at a time.');
                return $this->redirectToRoute('admin_school_years');
            }
            $this->em->remove($schoolyear);
            $this->em->flush();
            $this->addFlash('info', 'SchoolYear succesfully deleted');
        }
        return $this->redirectToRoute('admin_school_years');
    }
}
