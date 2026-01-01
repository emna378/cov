<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Form\TrajetType;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/trajet')]
final class TrajetController extends AbstractController
{
    #[Route(name: 'app_trajet_index', methods: ['GET'])]
    public function index(TrajetRepository $trajetRepository): Response
    {
        return $this->render('trajet/index.html.twig', [
            'trajets' => $trajetRepository->findAll(),
        ]);
    }

   #[Route('/new', name: 'app_trajet_new', methods: ['GET','POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $trajet = new Trajet();

    $chauffeur = $this->getUser();
    if (!$chauffeur instanceof \App\Entity\Chauffeur) {
        throw $this->createAccessDeniedException('Vous devez être un chauffeur pour créer un trajet.');
    }
    $trajet->setChauffeur($chauffeur);

    $form = $this->createForm(TrajetType::class, $trajet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($trajet);
        $entityManager->flush();

        return $this->redirectToRoute('app_chauffeur_show', [
            'id' => $chauffeur->getId(),
        ]);
    }

    return $this->render('trajet/new.html.twig', [
        'trajet' => $trajet,
        'form' => $form->createView(),
    ]);
}

    #[Route('/{id}', name: 'app_trajet_show', methods: ['GET'])]
    public function show(Trajet $trajet): Response
    {
        return $this->render('trajet/show.html.twig', [
            'trajet' => $trajet,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trajet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrajetType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trajet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajet/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trajet_delete', methods: ['POST'])]
    public function delete(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trajet);
            $entityManager->flush();
        }
$chauffeur = $this->getUser();

    if (!$chauffeur instanceof \App\Entity\Chauffeur) {
        throw $this->createAccessDeniedException();
    }

    // Redirection vers le dashboard du chauffeur
    return $this->redirectToRoute('app_chauffeur_show', [
        'id' => $chauffeur->getId(),
    ]);
}
}
