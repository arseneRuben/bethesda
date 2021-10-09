<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SchoolCrontrollerController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('school/index.html.twig');
    }

     /**
     * @Route("/admin", name="dashboard")
     */
    public function adminAction(Request $request)
    {
      
        $effectif = $this->container->get('app_service.stat');
        $em = $this->getDoctrine()->getManager();
        $year = $em->getRepository('AppBundle:SchoolYear')->findOneBy(array("activated" => true));
       
        return $this->render('default/index.html.twig', array(
            'year' => $year,
            
        ));
    }
}
