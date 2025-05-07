<?php

namespace App\Controller\Api;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/formations')]
class FormationApiController extends AbstractController
{
    private FormationRepository $formationRepository;
    private NoteRepository $noteRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        FormationRepository $formationRepository,
        NoteRepository $noteRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->formationRepository = $formationRepository;
        $this->noteRepository = $noteRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'api_formations_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Récupérer les paramètres de pagination
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        // Construire la requête
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('f')
           ->from(Formation::class, 'f')
           ->orderBy('f.name', 'ASC');

        // Pagination
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        // Exécuter la requête
        $formations = $qb->getQuery()->getResult();

        // Compter le nombre total de résultats pour la pagination
        $countQb = clone $qb;
        $countQb->select('COUNT(f.id)')
                ->setFirstResult(null)
                ->setMaxResults(null);
        $total = $countQb->getQuery()->getSingleScalarResult();

        // Formater les résultats
        $formattedFormations = [];
        foreach ($formations as $formation) {
            $moyenne = $this->noteRepository->calculateAverageRating($formation);
            $nombreAvis = $this->noteRepository->countRatings($formation);

            $formattedFormations[] = [
                'id' => $formation->getId(),
                'name' => $formation->getName(),
                'description' => $formation->getDescription(),
                'prix' => $formation->getPrix(),
                'date' => $formation->getDate() ? $formation->getDate()->format('Y-m-d') : null,
                'moyenne' => $moyenne,
                'nombreAvis' => $nombreAvis
            ];
        }

        return new JsonResponse([
            'formations' => $formattedFormations,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($total / $limit)
        ]);
    }

    #[Route('/test', name: 'api_formations_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'API de formations fonctionne correctement',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }


}
