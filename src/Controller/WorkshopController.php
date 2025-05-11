<?php

// App\Controller\WorkshopController
namespace App\Controller;

use App\Entity\Workshop;
use App\Form\WorkshopType;
use App\Repository\WorkshopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/workshop')]
final class WorkshopController extends AbstractController
{
    #[Route('/export', name: 'app_workshop_export', methods: ['GET'])]
    public function export(WorkshopRepository $workshopRepository): Response
    {
        $workshops = $workshopRepository->findAll();
        $html = $this->renderView('workshop/export_pdf.html.twig', [
            'workshops' => $workshops,
        ]);
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="workshops.pdf"',
            ]
        );
    }

    #[Route(name: 'app_workshop_index', methods: ['GET'])]
    public function index(Request $request, WorkshopRepository $workshopRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('q');
        $query = $workshopRepository->findBySearchQuery($search);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6 // workshops per page
        );
        return $this->render('workshop/index.html.twig', [
            'workshops' => $pagination,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_workshop_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $workshop = new Workshop();
        $form = $this->createForm(WorkshopType::class, $workshop);
        $form->handleRequest($request);
    
        $showCalendarModal = false;
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imageFile = $form->get('imageFile')->getData();
           
            
            if ($imageFile) {

                // Generate a unique filename using the original file extension
                $originalFilename = $imageFile->getClientOriginalName();
                $extension = $imageFile->getClientOriginalExtension();
                $newFilename = uniqid() . '.' . $extension;
    
                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads', // Specify the directory where you want to store the uploaded images
                        $newFilename
                    );
    
                    // Set the real path of the uploaded image in the User entity
                    $imagePath = '/uploads/' . $newFilename; // Relative path from the public directory
                   $workshop->setImage($imagePath);
                } catch (FileException $e) {
                    // Handle file upload exception if needed
                }
            }
            $workshop->setUser($this->getUser());
            $entityManager->persist($workshop);
            $entityManager->flush();
            $showCalendarModal = true;
        }
    
        return $this->render('workshop/new.html.twig', [
            'workshop' => $workshop,
            'form' => $form->createView(),
            'showCalendarModal' => $showCalendarModal,
        ]);
    }
    
    #[Route('/{id}', name: 'app_workshop_show', methods: ['GET'])]
    public function show(string $id, WorkshopRepository $workshopRepository): Response
    {
        $workshop = $workshopRepository->find($id);
        if (!$workshop) {
            throw $this->createNotFoundException('Workshop not found');
        }
        return $this->render('workshop/show.html.twig', [
            'workshop' => $workshop,
            'tasks' => $workshop->getTaskWorkshops(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_workshop_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Workshop $workshop, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WorkshopType::class, $workshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
              // Handle file upload
              $imageFile = $form->get('imageFile')->getData();
             
              if ($imageFile) {
  
                  // Generate a unique filename using the original file extension
                  $originalFilename = $imageFile->getClientOriginalName();
                  $extension = $imageFile->getClientOriginalExtension();
                  $newFilename = uniqid() . '.' . $extension;
      
                  try {
                      $imageFile->move(
                          $this->getParameter('kernel.project_dir') . '/public/uploads', // Specify the directory where you want to store the uploaded images
                          $newFilename
                      );
      
                      // Set the real path of the uploaded image in the User entity
                      $imagePath = '/uploads/' . $newFilename; // Relative path from the public directory
                     $workshop->setImage($imagePath);
                  } catch (FileException $e) {
                      // Handle file upload exception if needed
                  }
              }
            $entityManager->flush();

            return $this->redirectToRoute('app_workshop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('workshop/edit.html.twig', [
            'workshop' => $workshop,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_workshop_delete', methods: ['POST'])]
    public function delete(Request $request, Workshop $workshop, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workshop->getId(), $request->request->get('_token'))) {
            $entityManager->remove($workshop);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workshop_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/tasks-client', name: 'app_workshop_tasks_client', methods: ['GET'])]
    public function tasksClient(Workshop $workshop): Response
    {
        $tasks = $workshop->getTaskWorkshops();
        return $this->render('workshop/tasks_client.html.twig', [
            'workshop' => $workshop,
            'tasks' => $tasks,
        ]);
    }
}