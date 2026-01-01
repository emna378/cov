<?php

namespace App\Controller;

use App\Entity\Chauffeur;
use App\Form\ChauffeurType;
use App\Repository\ChauffeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/chauffeur')]
final class ChauffeurController extends AbstractController
{
    #[Route(name: 'app_chauffeur_index', methods: ['GET'])]
    public function index(ChauffeurRepository $chauffeurRepository): Response
    {
        return $this->render('chauffeur/index.html.twig', [
            'chauffeurs' => $chauffeurRepository->findAll(),
        ]);
    }

   #[Route('/new', name: 'app_chauffeur_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $chauffeur = new Chauffeur();
    $form = $this->createForm(ChauffeurType::class, $chauffeur);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // 🔹 HASH du mot de passe avant persist
        $hashedPassword = $passwordHasher->hashPassword($chauffeur, $chauffeur->getPassword());
        $chauffeur->setPassword($hashedPassword);

        // 🔹 Définir le rôle
        $chauffeur->setRoles(['ROLE_CHAUFFEUR']);

        $entityManager->persist($chauffeur);
        $entityManager->flush();

        return $this->redirectToRoute('app_login'); // redirection vers login
    }

    return $this->render('chauffeur/new.html.twig', [
        'chauffeur' => $chauffeur,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_chauffeur_show', methods: ['GET'])]
    public function show(Chauffeur $chauffeur): Response
    {
        return $this->render('chauffeur/show.html.twig', [
            'chauffeur' => $chauffeur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_chauffeur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Chauffeur $chauffeur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChauffeurType::class, $chauffeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

           return $this->redirectToRoute('app_chauffeur_show', [
            'id' => $chauffeur->getId(),
           ]);
        }

        return $this->render('chauffeur/edit.html.twig', [
            'chauffeur' => $chauffeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_chauffeur_delete', methods: ['POST'])]
    public function delete(Request $request, Chauffeur $chauffeur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chauffeur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($chauffeur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_chauffeur_index', [], Response::HTTP_SEE_OTHER);
    }
}
