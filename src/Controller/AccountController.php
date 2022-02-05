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

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account", methods="GET")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig');
    }

    /**
     * @Route("/account/edit", name="app_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
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
     * @Route("/account/change-password", name="app_account_change_password", methods={"GET", "POST"})
     */
    public function changePassord(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);

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
