<?php

namespace App\Controller;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/mission')]
final class MissionController extends AbstractController
{
    #[Route('/', name: 'app_mission_index', methods: ['GET'])]
    public function index(
        Request $request,
        MissionRepository $missionRepository,
        PaginatorInterface $paginator,
    ): Response {
        $criteria = [
            'search' => $request->query->get('search'),
            'sort_by' => $request->query->get('sort_by'),
            'sort_dir' => $request->query->get('sort_dir')
        ];
    
        $qb = $missionRepository->findBySearchCriteria($criteria);
        $pagination = $paginator->paginate($qb, $request->query->getInt('page', 1), 10);
    
        return $this->render('mission/list.html.twig', [
            'pagination' => $pagination,
            'criteria' => $criteria
        ]);
    }
    
    #[Route('/new', name: 'app_mission_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        \App\Service\TwilioSmsApiService $smsService
    ): Response {
        $mission = new Mission();
        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mission);
            $entityManager->flush();

            $message = sprintf(
                'Votre mission "%s" a été ajoutée avec succès.',
                $mission->getTitre()
            );
            $toPhoneNumber = '+21627303018';
            $smsService->sendSms($toPhoneNumber, $message);
    
            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/new.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }


    #[Route('/search', name: 'app_mission_search', methods: ['GET'])]
public function search(Request $request, MissionRepository $missionRepository): Response
{
    $title = $request->query->get('title', '');
    
    if (empty($title)) {
        return $this->redirectToRoute('app_mission_index');
    }

    $missions = $missionRepository->findByTitle($title);

    if ($request->isXmlHttpRequest()) {
        return $this->render('mission/_mission_table.html.twig', [
            'missions' => $missions,
            'criteria' => ['search' => $title] // Ajouté pour la cohérence
        ]);
    }

    return $this->render('mission/search_results.html.twig', [
        'missions' => $missions,
        'search_term' => $title,
        'criteria' => ['search' => $title] // Ajouté pour la cohérence
    ]);
}

    #[Route('/{idMission}', name: 'app_mission_show', methods: ['GET'])]
    public function show($idMission, MissionRepository $missionRepository): Response
    {
        $mission = $missionRepository->find($idMission);
        
        if (!$mission) {
            throw $this->createNotFoundException('La mission demandée n\'existe pas');
        }

        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

    #[Route('/{idMission}/edit', name: 'app_mission_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        $idMission, 
        MissionRepository $missionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $mission = $missionRepository->find($idMission);
        
        if (!$mission) {
            throw $this->createNotFoundException('La mission demandée n\'existe pas');
        }

        $form = $this->createForm(MissionType::class, $mission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('mission/edit.html.twig', [
            'mission' => $mission,
            'form' => $form,
        ]);
    }

    #[Route('/{idMission}', name: 'app_mission_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        $idMission, 
        MissionRepository $missionRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $mission = $missionRepository->find($idMission);
        
        if (!$mission) {
            throw $this->createNotFoundException('La mission demandée n\'existe pas');
        }

        if ($this->isCsrfTokenValid('delete'.$mission->getIdMission(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_mission_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/ajax/list', name: 'app_mission_ajax_list', methods: ['GET'])]
    public function ajaxList(
        Request $request, 
        MissionRepository $missionRepository, 
        SerializerInterface $serializer
    ): Response {
        $filters = [
            'search' => $request->query->get('search', ''),
            'date_from' => $request->query->get('date_from', ''),
            'date_to' => $request->query->get('date_to', '')
        ];
    
        $sort = $request->query->all('sort');
        $sorting = ['date' => 'DESC']; // Tri par défaut
    
        if (is_array($sort)) {
            $sorting = [];
            foreach ($sort as $field => $direction) {
                if (in_array($field, ['titre', 'date', 'destination'])) {
                    $sorting[$field] = strtolower($direction) === 'asc' ? 'ASC' : 'DESC';
                }
            }
        }
    
        $qb = $missionRepository->searchAndSort($filters, $sorting);
        $missions = $qb->getQuery()->getResult();
        
        return $this->render('mission/_list_partial.html.twig', [
            'missions' => $missions,
        ]);
    }

    
}