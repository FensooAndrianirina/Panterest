<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**   
     * @Route("/", name="app_home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**   
     * @Route("/pins/create", name="app_pins_create", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {

        $pin = new Pin();

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) 
        { 
    
            $pin->setUser($this->getUser());

            $em->persist($pin);

            $em->flush();

            $this->addFlash('success', 'Pin successfully created!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig',  
        [ 'formulaire' => $form->createView() ]);
    }

    /**   
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods="GET")
     */
    public function show(Pin $pin): Response
    {
         return $this->render('pins/show.html.twig', compact('pin'));
    }


    /**   
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET", "POST"})
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        //req de type PUT
        // $form = $this->createForm(PinType::class, $pin, [
        //     'method' => 'PUT'
        // ]);
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash('success', 'Pin successfully updated!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin'=> $pin,
            'formulaire' => $form->createView() ]);
    }

    /**   
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods={"POST"})
     */
    public function delete(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'. $pin->getId(), $request->request->get('_token'))) {
        
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'Pin successfully deleted!');

        }
        return $this->redirectToRoute('app_home');
    }
}
