<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiInscriptionController extends AbstractController
{
    #[Route('/api/inscription', name: 'api_inscription', methods: ['POST'])]
    public function inscription(Request $request): JsonResponse
    {
        // Récupérer les données du formulaire
        $nom = $request->request->get('first_name');
        $prenom = $request->request->get('last_name');
        $email = $request->request->get('email');
        $phone = $request->request->get('phone');
        $workshopId = $request->request->get('workshop_id');

        // Ici tu peux ajouter la logique d'enregistrement en base de données si besoin
        // ...

        // Retourner un succès
        return new JsonResponse([
            'message' => 'Inscription validée avec succès'
        ], 200);
    }
} 