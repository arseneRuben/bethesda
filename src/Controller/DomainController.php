<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DomainRepository;
use App\Entity\Domain;
use App\Form\DomainType;

/**
 * SchoolYear controller.
 *
 * @Route("/admin/domains")
 */
class DomainController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Lists all Programme entities.
     *
     * @Route("/", name="admin_domains")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(DomainRepository $repo)
    {

        $domains = $repo->findAll();

        return $this->render('domain/index.html.twig', compact("domains"));
    }




    /**
     * @Route("/create",name="admin_domains_new", methods={"GET","POST"})
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
        $domain = new Domain();
        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($domain);
            $this->em->flush();
            $this->addFlash('success', 'Domain succesfully created');
            return $this->redirectToRoute('admin_domains');
        }
        return $this->render(
            'domain/new.html.twig',
            ['form' => $form->createView()]
        );
    }


    /**
     * Finds and displays a Domain entity.
     *
     * @Route("/{id}/show", name="admin_domains_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Domain $domain)
    {

        return $this->render('domain/show.html.twig', compact("domain"));
    }

    /**
     * Creates a new Domain entity.
     *
     * @Route("/create", name="admin_domains_create")
     * @Method("POST")
     * @Template("AppBundle:Domain:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $domain = new Domain();
        $form = $this->createForm(new DomainType(), $domain);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($domain);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_domains'));
        }

        return array(
            'domain' => $domain,
            'form'   => $form->createView(),
        );
    }


    /**
     * Displays a form to edit an existing Programme entity.
     *
     * @Route("/{id}/edit", name="admin_domains_edit", requirements={"id"="\d+"}, methods={"GET","PUT"})
     * @Template()
     */
    public function edit(Request $request, Domain $domain): Response
    {
        $form = $this->createForm(DomainType::class, $domain, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Domain succesfully updated');
            return $this->redirectToRoute('admin_domains');
        }
        return $this->render('domain/edit.html.twig', [
            'domain' => $domain,
            'form' => $form->createView()
        ]);
    }

    /**
     * Deletes a Programme entity.
     *
     * @Route("/{id}/delete", name="admin_domains_delete", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function delete(Domain $domain, Request $request): Response
    {
        // if($this->isCsrfTokenValid('domains_deletion'.$domain->getId(), $request->request->get('crsf_token') )){
        $this->em->remove($domain);

        $this->em->flush();
        $this->addFlash('info', 'Domain succesfully deleted');
        //    }

        return $this->redirectToRoute('admin_domains');
    }
}
