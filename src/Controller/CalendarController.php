<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AccordRepository;
use Symfony\Component\HttpFoundation\JsonResponse;


final class CalendarController extends AbstractController{
    #[Route('/calendar', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('calendar/index1.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }



    #[Route('/calendar/events', name: 'app_calendar_events')]
    public function getEvents(AccordRepository $accordRepository): JsonResponse
    {
        $accords = $accordRepository->findAll();
        $events = [];
    
        foreach ($accords as $accord) {
            $materiel = $accord->getMaterielRecyclable();
    
          /*  if (!$materiel) {
                continue; // Ignore cet accord s'il n'a pas de matériel
            }*/
    
            $nomProduit = $materiel->getName();
            $statutEnum = $materiel->getStatut(); // Doit être un objet StatutEnum
            $statut = $statutEnum ? $statutEnum->value : null;
    
            // Définition de la couleur en fonction du statut
            $color = match ($statut) {
                'en_attente' => 'yellow',
                'valide' => 'green',
                'refuse' => 'red',
              
            };
    
            $events[] = [
                'title' => $nomProduit,
                'start' => $accord->getDateCreation()->format('Y-m-d'),
                'end' => $accord->getDateReception()?->format('Y-m-d'),
                'color' => $color,
                'date_creation' => $accord->getDateCreation()->format('Y-m-d'),
                'date_acceptation' => $accord->getDateReception()?->format('Y-m-d'),
            ];
        }
    
        return new JsonResponse($events);
    }




    #[Route('/calendar/update-status', name: 'update_status', methods: ['POST'])]
    public function updateStatus(Request $request, EntityManagerInterface $entityManager, AccordRepository $accordRepository): JsonResponse
    {
        $id = $request->request->get('id');
        $newStatus = $request->request->get('status');

        $accord = $accordRepository->find($id);
        if (!$accord) {
            return new JsonResponse(['error' => 'Accord non trouvé'], 404);
        }

        $accord->getMaterielRecyclable()->setStatut($newStatus);
        $entityManager->persist($accord);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Statut mis à jour avec succès']);
    }
    
}


