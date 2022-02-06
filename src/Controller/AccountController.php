<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("", name="app_account", methods="GET")
     */
    public function show(): Response
    {
        if (!$this->getUser()) 
        {
            $this->addFlash('error', 'You need to log in first');
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/show.html.twig');
    }

    /**
     * @Route("/edit", name="app_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) 
        {
            $this->addFlash('error', 'You need to log in first');
            
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);

        //$user pour prépopuler notre form avec les donnees de l'user

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            $this->addFlash('success', 'Account updated successfully!');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/change-password", name="app_account_change_password", methods={"GET", "POST"})
     */
    public function changePassord(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        if (!$this->getUser()) 
        {
            $this->addFlash('error', 'You need to log in first');
            
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class, null,
            [
                'current_password_is_required' => true
            ]
        );

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($userPasswordHasherInterface->hashPassword($user, $form['plainPassword']->getData()) ); 

            $em->flush();

            $this->addFlash('success', 'Password updated successfully!');

            return $this->redirectToRoute('app_account');
        } 

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
