<?php
namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MaterielRecyclable;
use App\Form\MaterielRecyclableType;
use App\Form\MaterielRecyclableEditType;
use App\Repository\MaterielRecyclableRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Enum\StatutEnum;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Accord;
use App\Repository\EntrepriseRepository;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;






class MaterielRecyclableController extends AbstractController
{
    private string $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
    }

    #[Route('/materiel/recyclable', name: 'app_materiel_recyclable')]
    public function index(): Response
    {
        return $this->render('materiel_recyclable/index.html.twig', [
            'controller_name' => 'MaterielRecyclableController',
        ]);
    }

  



   /* #[Route('/materiaux', name: 'app_materiaux_liste')]
public function liste(Request $request, MaterielRecyclableRepository $materielRepository): Response
{
    // Vérifier si l'utilisateur est connecté
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos matériaux.');
    }

    // Récupérer les matériaux appartenant à l'utilisateur connecté
    $materiaux = $materielRepository->findBy(['user' => $user]);

    return $this->render('materiel_recyclable/list.html.twig', [
        'materiaux' => $materiaux,
    ]);
}
*/


#[Route('/materiaux', name: 'app_materiaux_liste')]
public function liste(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
{
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos matériaux.');
    }

    // Create a query for the current user's materials
    $query = $entityManager->getRepository(MaterielRecyclable::class)
        ->createQueryBuilder('m')
        ->where('m.user = :user')
        ->setParameter('user', $user)
        ->getQuery();

    
    $materiaux = $paginator->paginate(
        $query, // Use the query instead of undefined $data
        $request->query->getInt('page', 1),
        4
    );

    return $this->render('materiel_recyclable/list.html.twig', [
        'materiaux' => $materiaux,
        'order' => null,
        'statut' => null,
    ]);
}






#[Route('/materiaux/statut/{statut}', name: 'list_materiaux_by_statut')]
public function filterByStatut(
    string $statut,
    MaterielRecyclableRepository $materielRepository,
    PaginatorInterface $paginator,
    Request $request
): Response {


    $user = $this->getUser();
if (!$user) {
    throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos matériaux.');
}

    
    if (!in_array($statut, ['en_attente', 'valide','refuse'])) {
        throw $this->createNotFoundException('Statut invalide.');
    }

    $query = $materielRepository->createQueryBuilder('m')
        ->where('m.statut = :statut')
        ->andWhere('m.user = :user') 
        ->setParameter('statut', $statut)
        ->setParameter('user', $user)
        ->getQuery();
       

    // Paginer les résultats
    $materiaux = $paginator->paginate(
        $query, // On passe la requête DQL
        $request->query->getInt('page', 1), // Page actuelle
        4 ,
    );

    return $this->render('materiel_recyclable/list.html.twig', [
        'materiaux' => $materiaux,
        'statut' => $statut,
        'order' => null,
    ]);
}

/*#[Route('/materiaux/statut/{statut}', name: 'list_materiaux_by_statut')]
public function filterByStatut(string $statut, MaterielRecyclableRepository $materielRepository): Response
{
    if (!in_array($statut, ['en_attente', 'valide'])) {
        throw $this->createNotFoundException('Statut invalide.');
    }

    $materiaux = $materielRepository->findByStatut($statut);

    return $this->render('materiel_recyclable/list.html.twig', [
        'materiaux' => $materiaux,
        'statut' => $statut,
        'order'=>null,
    ]);
}*/




#[Route('/materiaux/tri/{order}', name: 'list_materiaux_sorted')]
public function sortByDate(string $order, MaterielRecyclableRepository $materielRepository, PaginatorInterface $paginator, Request $request): Response
{

    
    $user = $this->getUser();
if (!$user) {
    throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos matériaux.');
}


    if (!in_array($order, ['asc', 'desc'])) {
        throw $this->createNotFoundException('Ordre de tri invalide.');
    }

    //$query = $materielRepository->findAllSortedByDate($order);
    $query = $materielRepository->findAllSortedByUserAndDate($user, $order);

    $materiaux = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // page number
        4 // limit per page
    );

    return $this->render('materiel_recyclable/list.html.twig', [
        'materiaux' => $materiaux,
        'order' => $order,
        'statut' => null,
    ]);
}
    #[Route('/add', name: 'ajouter_materiel_recyclable')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $materiel = new MaterielRecyclable();
        $materiel->setStatut(StatutEnum::EN_ATTENTE);
        $form = $this->createForm(MaterielRecyclableType::class, $materiel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $materiel->setDatecreation(new \DateTimeImmutable());
            if ($user) {
                $materiel->setUser($user);
            }
            else{
                return $this->redirectToRoute('login');
            }
            $imageFile = $form->get('image')->getData();
            
            if ($imageFile) {
                $fileName = $imageFile->getClientOriginalName();
                $fullPath = $this->uploadsDirectory . '/' . $fileName;
                $imageFile->move($this->uploadsDirectory, $fileName);
                $materiel->setImage($fullPath);
            } else {
                $materiel->setImage('default-image.jpg');
            }
            $entityManager->persist($materiel);

            // Création de l'accord pour ce matériel
            $accord = new Accord();
            $accord->setMaterielRecyclable($materiel);
           

            $entreprise = $materiel->getEntreprise(); // Assure-toi que cette relation existe

           if ($entreprise !== null) {
             $accord->setEntreprise($entreprise);
            } else {
          throw new \Exception("L'entreprise associée au matériel est introuvable !");
                }
           
            $accord->setDateCreation(new \DateTimeImmutable());
            $accord->setquantity('0');
            $accord->setoutput('output');

          // Sauvegarde de l'accord dans la base de données
          $entityManager->persist($accord);
          $entityManager->flush();


            
            $this->addFlash('success', 'Matériel ajouté avec succès !');
            return $this->redirectToRoute('app_materiaux_liste');
        }
        return $this->render('materiel_recyclable/ajouter.html.twig', [
            'form' => $form->createView(),
            'materielRecyclable' => $materiel
        ]);
    }




 #[Route('/materiel/edit/{id}', name: 'materiel_edit')]
public function edit(
    int $id, 
    MaterielRecyclableRepository $materielRepository, 
    Request $request, 
    EntityManagerInterface $entityManager,
    ParameterBagInterface $params
): Response {
    $materiel = $materielRepository->find($id);
    
    if (!$materiel) {
        throw $this->createNotFoundException('Material not found');
    }

    // Récupérer le chemin complet actuel depuis la BDD
    $currentImagePath = $materiel->getImage();
    
    $form = $this->createForm(MaterielRecyclableEditType::class, $materiel);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile|null $file */
        $file = $form->get('image')->getData();
        $uploadsDir = $params->get('uploads_directory');

        if ($file) {
            $fileName = $file->getClientOriginalName(); // Garder le nom original
            $fullPath = $uploadsDir.'/'.$fileName;

            try {
                // Supprimer l'ancienne image si elle existe
                if ($currentImagePath && file_exists($currentImagePath)) {
                    unlink($currentImagePath);
                }
                
                $file->move($uploadsDir, $fileName);
                $materiel->setImage($fullPath); // Stocker le chemin complet
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur upload: '.$e->getMessage());
                return $this->redirectToRoute('materiel_edit', ['id' => $id]);
            }
        }

        $entityManager->flush();
        $this->addFlash('success', 'Modification réussie');
        return $this->redirectToRoute('app_materiaux_liste');
    }

    return $this->render('materiel_recyclable/edit.html.twig', [
        'form' => $form->createView(),
        'materiel' => $materiel,
        'current_image_path' => $currentImagePath // Passer le chemin complet au template
    ]);
}

    #[Route('/admin/listMaterials', name: 'list_materials_admin')]
    public function listMaterial(MaterielRecyclableRepository $materielRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $materiels = $materielRepository->findAll();
        $materiel = new MaterielRecyclable();
        $form = $this->createForm(MaterielRecyclableType::class, $materiel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $materiel = $form->getData();
            $materiel->setDatecreation(new \DateTimeImmutable());
            
            // Fetch the current user from the session
            $user = $this->getUser();
            if ($user) {
                $materiel->setUser($user);
            }

            $file = $form->get('image')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move($this->uploadsDirectory, $fileName);
                    $materiel->setImage($fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'There was an error uploading your Material image: ' . $e->getMessage());
                    return $this->redirectToRoute('list_materials_admin');
                }
            } else {
                $this->addFlash('error', $materiel->getName() . ' was added without an Image!');
                $materiel->setImage('default-image.jpg');
            }
            $materiel->setStatut(StatutEnum::EN_ATTENTE);
            $this->addFlash('success', $materiel->getName() . ' was added successfully!');
            $entityManager->persist($materiel);
            $entityManager->flush();
            return $this->redirectToRoute('list_materials_admin');
        }
        return $this->render('materiel_recyclable/list_materiel_recyclable_dashboard.html.twig', [
            'materiels' => $materiels,
            'form' => $form->createView(),
        ]);
    }



   /* #[Route('/admin/editMaterial/{id}', name: 'edit_material_admin')]
    public function editMaterial(int $id, MaterielRecyclableRepository $materielRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $materiel = $materielRepository->find($id);
        if (!$materiel) {
            throw $this->createNotFoundException('The material does not exist.');
        }
        $form = $this->createForm(MaterielRecyclableEditType::class, $materiel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $materiel = $form->getData();
            $this->addFlash('success', 'Updating Material: ' . $materiel->getName());
            $entityManager->persist($materiel);
            $entityManager->flush();
            return $this->redirectToRoute('list_materials_admin');
        }
        return $this->render('materiel_recyclable/edit_materiel_recyclable_dashboard.html.twig', [
            'materiel' => $materiel,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/deleteMaterial/{id}', name: 'delete_material_admin')]
    public function deleteMaterial(EntityManagerInterface $entityManager, int $id): Response
    {
        $materiel = $entityManager->getRepository(MaterielRecyclable::class)->find($id);
        if (!$materiel) {
            throw $this->createNotFoundException('Matériel recyclable non trouvé.');
        }
        if ($materiel->getImage()) {
            $imagePath = $this->uploadsDirectory . '/' . $materiel->getImage();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $entityManager->remove($materiel);
        $entityManager->flush();
        $this->addFlash('success', $materiel->getName() . ' a été supprimé avec succès !');
        return $this->redirectToRoute('list_materials_admin');
    }


   /* #[Route('/list/materiel/statut/{statut}', name: 'list_materiel_by_statut', defaults: ['statut' => 'tous'])]
    public function filterByStatut(?string $statut, MaterielRecyclableRepository $materielRepository): Response
    {
        $materiaux = $materielRepository->findByStatut($statut);

        return $this->render('materiel_recyclable/list.html.twig', [
            'materiaux' => $materiaux,
            'statut' => $statut, // On envoie le statut au template
        ]);
    }*/
   


    #[Route('/materiel/delete/{id}', name: 'materiel_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $materiel = $entityManager->getRepository(MaterielRecyclable::class)->find($id);
        if ($materiel) {
            if ($materiel->getImage()) {
                $imagePath = $this->uploadsDirectory . '/' . $materiel->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $entityManager->remove($materiel);
            $entityManager->flush();
            $this->addFlash('success', 'Matériel recyclable supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Matériel recyclable non trouvé.');
        }
        return $this->redirectToRoute('app_materiaux_liste');
    }

}