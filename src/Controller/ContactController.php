<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Email;
use App\Form\EmailType;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer): Response
    {
        $enquiry = new Email();
        $form = $this->createForm(EmailType::class, $enquiry);
        $form->handleRequest($request);

   
         //dd($form->getErrors());
         if ($form->isSubmitted() && $form->isValid()) {
                       $enquiry->setSender($this->getUser());
                        $message = (new \Swift_Message($enquiry->getSubject()))
                                ->setFrom($this->getUser()->getEmail())
                                ->setTo('isbbethesda@gmail.com')
                                ->setBody($enquiry->getContent());
                        $mailer->send($message);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($enquiry);
                        $entityManager->flush();
                        $this->addFlash('primary', 'Correspondance bien transmis. Nous vous repondrons dans les plus bref delais!');
         }

            return $this->render('contact/form.html.twig', array(
                        'form' => $form->createView()
            ));
        
        
    }
}
