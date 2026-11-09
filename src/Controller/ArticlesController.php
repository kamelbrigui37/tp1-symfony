<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ArticlesController extends AbstractController
{
    // Afficher tous les articles
    #[Route('/articles', name: 'app_articles')]
    public function index(
        ArticleRepository $articleRepository
    ): Response {
        $articles = $articleRepository->findAll();

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    // Créer un nouvel article
    #[Route('/articles/nouveau', name: 'app_article_nouveau')]
    #[IsGranted('ROLE_USER')]
    public function nouveau(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $article = new Article();

        $article->setDatecreation(new \DateTime());
        $article->setPublie(false);

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuteurUser($this->getUser());
            $em->persist($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Article créé avec succès !'
            );

            return $this->redirectToRoute('app_articles');
        }

        return $this->render('articles/nouveau.html.twig', [
            'formulaire' => $form,
        ]);
    }

    // Afficher les détails d'un article
    #[Route(
        '/articles/{id}',
        name: 'app_article_detail',
        requirements: ['id' => '\d+']
    )]
    public function detail(Article $article): Response
    {
        return $this->render('articles/detail.html.twig', [
            'article' => $article,
        ]);
    }

    // Modifier un article
    #[Route(
        '/articles/{id}/modifier',
        name: 'app_article_modifier',
        requirements: ['id' => '\d+']
    )]
    #[IsGranted('ROLE_USER')]
    public function modifier(
        Article $article,
        Request $request,
        EntityManagerInterface $em
    ): Response {
if (
    $article->getAuteurUser() !== $this->getUser()
    && !$this->isGranted('ROLE_ADMIN')
) {
    throw $this->createAccessDeniedException(
        'Vous ne pouvez pas modifier cet article.'
    );
}
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Article modifié avec succès !'
            );

            return $this->redirectToRoute('app_article_detail', [
                'id' => $article->getId(),
            ]);
        }

        return $this->render('articles/modifier.html.twig', [
            'formulaire' => $form,
            'article' => $article,
        ]);
    }

    // Supprimer un article
    #[Route(
        '/articles/{id}/supprimer',
        name: 'app_article_supprimer',
        requirements: ['id' => '\d+'],
        methods: ['POST']
    )]
    #[IsGranted('ROLE_USER')]
    public function supprimer(
        Article $article,
        Request $request,
        EntityManagerInterface $em
    ): Response {
if (
    $article->getAuteurUser() !== $this->getUser()
    && !$this->isGranted('ROLE_ADMIN')
) {
    throw $this->createAccessDeniedException(
        'Vous ne pouvez pas modifier cet article.'
    );
}
        if ($this->isCsrfTokenValid(
            'supprimer_' . $article->getId(),
            $request->request->get('_token')
        )) {
            $em->remove($article);
            $em->flush();

            $this->addFlash(
                'success',
                'Article supprimé avec succès.'
            );
        } else {
            $this->addFlash(
                'danger',
                'Token CSRF invalide. Suppression annulée.'
            );
        }

        return $this->redirectToRoute('app_articles');
    }
}
