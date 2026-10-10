<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class TacheController extends AbstractController
{
    // Afficher toutes les tâches
    #[Route('/taches', name: 'app_taches')]
    public function index(TacheRepository $tacheRepository): Response
    {
        // Les tâches non terminées apparaissent en premier
        $taches = $tacheRepository->findBy([], [
            'terminee' => 'ASC',
            'dateCreation' => 'DESC',
        ]);

        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
        ]);
    }

    // Créer une tâche
    #[Route('/taches/ajouter', name: 'app_tache_ajouter')]
    public function ajouter(EntityManagerInterface $em): Response
    {
        $tache = new Tache();

        $tache->setTitre('Apprendre Symfony');
        $tache->setDescription('Réaliser le mini-projet To-Do List.');
        $tache->setTerminee(false);
        $tache->setDateCreation(new \DateTime());

        $em->persist($tache);
        $em->flush();

        return $this->redirectToRoute('app_taches');
    }

    // Afficher une tâche par son ID
    #[Route(
        '/taches/{id}',
        name: 'app_tache_detail',
        requirements: ['id' => '\d+']
    )]
    public function detail(Tache $tache): Response
    {
        return $this->render('tache/detail.html.twig', [
            'tache' => $tache,
        ]);
    }

    // BONUS : marquer une tâche comme terminée
    #[Route(
        '/taches/{id}/terminer',
        name: 'app_tache_terminer',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    public function terminer(
        Tache $tache,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        if (!$this->isCsrfTokenValid(
            'terminer'.$tache->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $tache->setTerminee(true);
        $em->flush();

        return $this->redirectToRoute('app_taches');
    }
}
