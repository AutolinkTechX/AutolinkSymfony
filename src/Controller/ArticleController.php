<?php
namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class ArticleController extends AbstractController
{
    private string $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
    }

    #[Route('/admin/listArticles', name: 'list_articles_admin')]
    public function listArticles(ArticleRepository $articleRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Pagination
        $page = $request->query->getInt('page', 1);
        $pageSize = 3;

        $query = $articleRepository->createQueryBuilder('a')->getQuery();
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $pageSize);

        $query->setFirstResult($pageSize * ($page - 1))
              ->setMaxResults($pageSize);

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $formErrors = false;

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $article = $form->getData();
                $article->setDatecreation(new \DateTimeImmutable());

                $file = $form->get('image')->getData();
                if ($file) {
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                    try {
                        $file->move($this->uploadsDirectory, $fileName);

                        // Stocker le chemin complet
                        $fullPath = 'file:/C:/Users/ghodb/OneDrive/Bureau/version%20finale%20pi/AutolinkSymfony/public/uploads/' . $fileName;
                        $article->setImage($fullPath);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                        return $this->redirectToRoute('list_articles_admin');
                    }
                } else {
                    $article->setImage('default-image.jpg');
                }

                $entityManager->persist($article);
                $entityManager->flush();

                $this->addFlash('success', 'Article ajouté avec succès!');
                return $this->redirectToRoute('list_articles_admin');
            } else {
                $formErrors = true;
                $this->addFlash('error', 'Erreur dans le formulaire');
            }
        }

        return $this->render('article/list_articles_dashboard.html.twig', [
            'articles' => $paginator,
            'form' => $form->createView(),
            'formErrors' => $formErrors,
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'currentPage' => $page,
        ]);
    }

    #[Route('/admin/editArticle/{id}', name: 'edit_article_admin')]
    public function editArticle(int $id, ArticleRepository $articleRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move($this->uploadsDirectory, $fileName);

                    // Stocker le chemin complet
                    $fullPath = 'file:/C:/Users/ghodb/OneDrive/Bureau/version%20finale%20pi/AutolinkSymfony/public/uploads/' . $fileName;
                    $article->setImage($fullPath);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                    return $this->redirectToRoute('list_articles_admin');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Article mis à jour avec succès');
            return $this->redirectToRoute('list_articles_admin');
        }

        return $this->render('article/edit_article_dashboard.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/deleteArticle/{id}', name: 'delete_article_admin')]
    public function deleteArticle(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $entityManager->remove($article);
        $entityManager->flush();
        $this->addFlash('success', 'Article supprimé avec succès');
        return $this->redirectToRoute('list_articles_admin');
    }
}
