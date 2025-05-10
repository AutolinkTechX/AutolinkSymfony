<?php

namespace App\Controller;

use App\Entity\TaskWorkshop;
use App\Form\TaskWorkshopType;
use App\Repository\TaskWorkshopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Workshop;

#[Route('/task/workshop')]
final class TaskWorkshopController extends AbstractController
{
    #[Route('/', name: 'app_task_workshop_index', methods: ['GET'])]
    public function index(TaskWorkshopRepository $taskWorkshopRepository): Response
    {
        return $this->redirectToRoute('app_workshop_index');
    }

    #[Route('/new', name: 'app_task_workshop_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taskWorkshop = new TaskWorkshop();
        $form = $this->createForm(TaskWorkshopType::class, $taskWorkshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taskWorkshop);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_workshop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_workshop/new.html.twig', [
            'task_workshop' => $taskWorkshop,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_workshop_show', methods: ['GET'])]
    public function show(TaskWorkshop $taskWorkshop): Response
    {
        return $this->render('task_workshop/show.html.twig', [
            'task_workshop' => $taskWorkshop,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_workshop_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskWorkshop $taskWorkshop, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskWorkshopType::class, $taskWorkshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_workshop_show', ['id' => $taskWorkshop->getWorkshop()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_workshop/edit.html.twig', [
            'task_workshop' => $taskWorkshop,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_task_workshop_delete', methods: ['POST'])]
    public function delete(Request $request, TaskWorkshop $taskWorkshop, EntityManagerInterface $entityManager): Response
    {
        $workshopId = $taskWorkshop->getWorkshop()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$taskWorkshop->getId(), $request->request->get('_token'))) {
            $entityManager->remove($taskWorkshop);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_workshop_show', ['id' => $workshopId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new/{workshopId}', name: 'app_task_workshop_new_for_workshop', methods: ['GET', 'POST'])]
    public function newForWorkshop(
        int $workshopId,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $workshop = $entityManager->getRepository(Workshop::class)->find($workshopId);
        if (!$workshop) {
            throw $this->createNotFoundException('Workshop not found');
        }

        $taskWorkshop = new TaskWorkshop();
        $taskWorkshop->setWorkshop($workshop);

        $form = $this->createForm(TaskWorkshopType::class, $taskWorkshop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taskWorkshop);
            $entityManager->flush();

            return $this->redirectToRoute('app_workshop_show', ['id' => $workshopId]);
        }

        return $this->render('task_workshop/new.html.twig', [
            'task_workshop' => $taskWorkshop,
            'form' => $form,
        ]);
    }
}
