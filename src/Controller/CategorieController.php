<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategorieController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(
        CategorieRepository $categorieRepository
    ): Response {
        $categories = $categorieRepository->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/nouvelle', name: 'app_categorie_nouvelle')]
    public function nouvelle(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $categorie = new Categorie();

        $form = $this->createFormBuilder($categorie)
            ->add('nom', TextType::class, [
                'label' => 'Nom de la catégorie',
                'attr' => [
                    'placeholder' => 'Ex : Technologie, Sport...',
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'class' => 'form-control',
                ],
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Créer la catégorie',
                'attr' => [
                    'class' => 'btn btn-primary w-100 mt-3',
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash(
                'success',
                'Catégorie "' . $categorie->getNom() . '" créée !'
            );

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categorie/nouvelle.html.twig', [
            'formulaire' => $form,
        ]);
    }
#[Route(
    '/categories/{id}',
    name: 'app_categorie_detail',
    requirements: ['id' => '\d+'],
    methods: ['GET']
)]
public function detail(Categorie $categorie): Response
{
    return $this->render('categorie/detail.html.twig', [
        'categorie' => $categorie,
    ]);
}

#[Route(
    '/categories/{id}/modifier',
    name: 'app_categorie_modifier',
    requirements: ['id' => '\d+']
)]
public function modifier(
    Categorie $categorie,
    Request $request,
    EntityManagerInterface $em
): Response {
    $form = $this->createForm(CategorieType::class, $categorie);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();

        $this->addFlash(
            'success',
            'Catégorie modifiée avec succès !'
        );

        return $this->redirectToRoute(
            'app_categorie_detail',
            ['id' => $categorie->getId()]
        );
    }

    return $this->render('categorie/modifier.html.twig', [
        'formulaire' => $form,
        'categorie' => $categorie,
    ]);
}

#[Route(
    '/categories/{id}/supprimer',
    name: 'app_categorie_supprimer',
    requirements: ['id' => '\d+'],
    methods: ['POST']
)]
public function supprimer(
    Categorie $categorie,
    Request $request,
    EntityManagerInterface $em
): Response {
    if (!$this->isCsrfTokenValid(
        'supprimer_categorie_' . $categorie->getId(),
        $request->request->get('_token')
    )) {
        $this->addFlash(
            'danger',
            'Token CSRF invalide. Suppression annulée.'
        );

        return $this->redirectToRoute(
            'app_categorie_detail',
            ['id' => $categorie->getId()]
        );
    }

    if (!$categorie->getArticles()->isEmpty()) {
        $this->addFlash(
            'danger',
            'Impossible de supprimer cette catégorie : elle contient encore des articles.'
        );

        return $this->redirectToRoute(
            'app_categorie_detail',
            ['id' => $categorie->getId()]
        );
    }

    $em->remove($categorie);
    $em->flush();

    $this->addFlash(
        'success',
        'Catégorie supprimée avec succès !'
    );

    return $this->redirectToRoute('app_categories');
}
}
