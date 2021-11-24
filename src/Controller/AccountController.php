<?php

namespace App\Controller;

use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

      /**
     * @Route("/account", name="app_account")
     */
    public function index(): Response
    {
       
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        } else {
           // $this->getUser()->getRoles();
           // $this->em->persist($this->getUser());
           // $this->em->flush();
            if(!$this->getUser()->isVerified())
            {
                $this->addFlash('warning', 'You need to have a verified account');
                return $this->redirectToRoute('app_home');
            }
            else
            {
            
                $hasAccess = $this->isGranted('ROLE_ADMIN');
                if(!$hasAccess){
                    return $this->redirectToRoute('app_home');
                }else
                {
                    return $this->render('account/profile.html.twig');
                }
            }
        }
    }


      /**
     * @Route("/edit", name="admin_account_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
      
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
    	{
            $this->em->flush();
            $this->addFlash('success', 'Account successfully modified');
            return $this->redirectToRoute('app_account');
        }


        return $this->render('account/edit.html.twig'	, [
            'form'=>$form->createView()
        ]);
    }


     /*
     * @Route("/changepwd", name="admin_account_changepwd", methods={"GET","POST"})
    */
    public function changePwd(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if(!$this->getUser())
        {
            $this->addFlash('warning', 'You need login first!');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_required' => true,
        ]);
        $form->handleRequest($request);
        $user = $this->getUser();

        //$form = $this->createForm(UserFormType::class, $user);
      

        if($form->isSubmitted() && $form->isValid())
    	{
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $this->em->flush();
            $this->addFlash('success', 'Account successfully modified');
            return $this->redirectToRoute('app_account');
        }


        return $this->render('account/changepwd.html.twig'	, [
            'form'=>$form->createView()
        ]);
    }
   
}
