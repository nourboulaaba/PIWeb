<?php

namespace App\Controller;

use App\Entity\Contrat;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
    
#[Route('/contrat')]
final class ContratController extends AbstractController
{
    #[Route('/',name: 'app_contrat_index', methods: ['GET'])]
    public function index(ContratRepository $contratRepository): Response
    {
        return $this->render('contrat/list.html.twig', [
            'contrats' => $contratRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $contrat = new Contrat();
    $form = $this->createForm(ContratType::class, $contrat);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $entityManager->persist($contrat);
            $entityManager->flush();
            
            // Ajout d'un message flash pour confirmer la création
            $this->addFlash('success', 'Le contrat a été créé avec succès.');
            
            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        } else {
            // Ajout d'un message flash général en cas d'erreur
            $this->addFlash('error', 'Veuillez corriger les erreurs dans le formulaire.');
            
            // Optionnel : Log des erreurs pour le débogage
            foreach ($form->getErrors(true) as $error) {
                // Vous pouvez logger les erreurs si nécessaire
                // $this->logger->error('Erreur de validation: '.$error->getMessage());
            }
        }
    }

    return $this->render('contrat/new.html.twig', [
        'contrat' => $contrat,
        'form' => $form->createView(), // Notez le createView() ici
    ]);
}

    #[Route('/{IdContrat}', name: 'app_contrat_show', methods: ['GET'])]
public function show(int $IdContrat, ContratRepository $contratRepository): Response
{
    $contrat = $contratRepository->find($IdContrat);
    
    if (!$contrat) {
        throw $this->createNotFoundException('Contrat non trouvé');
    }

    return $this->render('contrat/show.html.twig', [
        'contrat' => $contrat,
    ]);
}

    #[Route('/{IdContrat}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        int $IdContrat, 
        ContratRepository $contratRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $contrat = $contratRepository->find($IdContrat);
        
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé');
        }
    
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_contrat_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form' => $form,
        ]);
    }

    #[Route('/{IdContrat}/delete', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        int $IdContrat,
        ContratRepository $contratRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $contrat = $contratRepository->find($IdContrat);
        
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé');
        }
    
        if ($this->isCsrfTokenValid('delete'.$IdContrat, $request->request->get('_token'))) {
            $entityManager->remove($contrat);
            $entityManager->flush();
            $this->addFlash('success', 'Contrat supprimé avec succès');
        } else {
            $this->addFlash('error', 'Échec de la suppression (token invalide)');
        }
    
        return $this->redirectToRoute('app_contrat_index');
    }
}
