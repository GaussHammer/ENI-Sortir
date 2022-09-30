<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/myProfile", name="MyProfile", methods={"GET"})
     */
    public function MonProfil(ParticipantRepository $participantRepository, SortieRepository $sortieRepository): Response
    {
        $pseudo = $this->getUser()->getUserIdentifier();
        return $this->render('participant/monProfil.html.twig', [
            'monProfil' => $participantRepository->findBy(['pseudo'=>$pseudo]),
            //'mesSorties'=> $sortieRepository->findAll()
            ]);
    }

    /**
     * @Route("/{id}", name="user_profile", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_profile", methods={"GET", "POST"})
     */
    public function edit(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participantRepository->add($participant, true);
            //TODO:add flash message
            return $this->redirectToRoute('main_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="profile_delete", methods={"POST"})
     */
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('main_home', [], Response::HTTP_SEE_OTHER);
    }
}
