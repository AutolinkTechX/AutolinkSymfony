<?php

// App\Controller\WorkshopInscritController
namespace App\Controller;

use App\Entity\Workshop;
use App\Entity\InscriptionWorkshop;
use App\Form\WorkshopType;
use App\Repository\WorkshopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WorkshopInscritController extends AbstractController
{

    #[Route('/workshopinscrit', name: 'app_workshop_inscrit', methods: ['GET'])]
    public function home(WorkshopRepository $workshopRepository): Response
    {
        return $this->render('workshop_inscrit.html.twig', [
            'workshops' => $workshopRepository->findAll(),
        ]);
    }

}
