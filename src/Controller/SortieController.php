<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\DesistementType;
use App\Form\InscriptionType;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="app_sortie_index", methods={"GET"})
     */
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="create_sortie", methods={"GET", "POST"})
     */
    public function new(Request $request,
                        SortieRepository $sortieRepository,
                        ManagerRegistry $doctrine
                        ): Response
    {
        $pseudo=$this->getUser()->getUserIdentifier();
        $organisateur = $doctrine->getRepository(Participant::class)->findOneBySomeField($pseudo);
        $campus = $doctrine->getRepository(Campus::class)->findOneBySomeField($organisateur->getCampus()->getNom());
        $etat = $doctrine->getRepository(Etat::class)->findOneBySomeField('Créée');
        $sortie = new Sortie();
        $sortie->setOrganisateur($organisateur);
        $sortie->setEtat($etat);
        $sortie->setCampus($campus);
        $sortie->addParticipant($organisateur);
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
            'org' => $organisateur
        ]);
    }

    /**
     * @Route("/{id}", name="app_sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        dump($sortie);
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_sortie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_sortie_delete", methods={"POST"})
     */
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/inscription", name="sortie_inscription", methods={"GET", "POST"})
     */
    public function inscription(ManagerRegistry $doctrine, Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $pseudo=$this->getUser()->getUserIdentifier();
        $participant = $doctrine->getRepository(Participant::class)->findOneBySomeField($pseudo);
        $form = $this->createForm(InscriptionType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->addParticipant($participant);
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sortie/Inscription.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/desistement", name="sortie_desistement", methods={"GET","POST"})
     */
    public function desistement(ManagerRegistry $doctrine, Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        $pseudo = $this->getUser()->getUserIdentifier();
        $participant = $doctrine->getRepository(Participant::class)->findOneBySomeField($pseudo);
        $form = $this->createForm(DesistementType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->removeParticipant($participant);
            $participant->removeParticipe($sortie);
            $sortieRepository->add($sortie, true);

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('sortie/Desistement.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

}
