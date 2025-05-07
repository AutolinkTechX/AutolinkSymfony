<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Form\Bloguser;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog')]
class BlogController extends AbstractController
{
    #[Route('/', name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository, Request $request): Response
    {
        $session = $request->getSession();
        $blogs = $blogRepository->findAll();

        foreach ($blogs as $blog) {
            $blog->userReaction = $session->get('blog_reaction_' . $blog->getId(), null);
        }

        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $form = $this->createForm(Bloguser::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            echo $imageFile;

            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $blog->setImage($newFilename); // Enregistrement du nom de fichier en BDD
                } catch (FileException $e) {
                    return new Response("Erreur lors de l'upload : " . $e->getMessage());
                }
            }

            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_blog_index');
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_index');
    }

    #[Route('/{id}/like', name: 'app_blog_like', methods: ['POST'])]
    public function like(Blog $blog, EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $request->getSession();
        $userReaction = $session->get('blog_reaction_' . $blog->getId(), null);

        if ($userReaction === 'like') {
            $blog->setLikes(max(0, $blog->getLikes() - 1)); // Évite d'avoir un nombre négatif
            $session->remove('blog_reaction_' . $blog->getId());
            $reaction = null;
        } else {
            if ($userReaction === 'dislike') {
                $blog->setDislikes(max(0, $blog->getDislikes() - 1));
            }
            $blog->setLikes($blog->getLikes() + 1);
            $session->set('blog_reaction_' . $blog->getId(), 'like');
            $reaction = 'like';
        }

        $entityManager->flush();

        return $this->json([
            'likes' => $blog->getLikes(),
            'dislikes' => $blog->getDislikes(),
            'reaction' => $reaction
        ]);
    }

    #[Route('/{id}/dislike', name: 'app_blog_dislike', methods: ['POST'])]
    public function dislike(Blog $blog, EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $request->getSession();
        $userReaction = $session->get('blog_reaction_' . $blog->getId(), null);

        if ($userReaction === 'dislike') {
            $blog->setDislikes(max(0, $blog->getDislikes() - 1));
            $session->remove('blog_reaction_' . $blog->getId());
            $reaction = null;
        } else {
            if ($userReaction === 'like') {
                $blog->setLikes(max(0, $blog->getLikes() - 1));
            }
            $blog->setDislikes($blog->getDislikes() + 1);
            $session->set('blog_reaction_' . $blog->getId(), 'dislike');
            $reaction = 'dislike';
        }

        $entityManager->flush();

        return $this->json([
            'likes' => $blog->getLikes(),
            'dislikes' => $blog->getDislikes(),
            'reaction' => $reaction
        ]);
    }
}
