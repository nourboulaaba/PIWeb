<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ajax')]
class AjaxController extends AbstractController
{
    #[Route('/users/search', name: 'app_ajax_users_search', methods: ['GET'])]
    public function searchUsers(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): JsonResponse {
        try {
            // Récupérer et nettoyer les critères de recherche
            $criteria = $this->getSanitizedCriteria($request);

            // Obtenir le QueryBuilder avec les critères appliqués
            $queryBuilder = $userRepository->findBySearchCriteria($criteria);

            // Récupérer tous les paramètres de la requête pour les transmettre à la pagination
            $routeParams = $request->query->all();

            // Paginer les résultats
            $users = $paginator->paginate(
                $queryBuilder,
                $request->query->getInt('page', 1),
                $request->query->getInt('limit', 5),
                [
                    'pageParameterName' => 'page',
                    'sortDirectionParameterName' => 'sort_dir',
                    'sortFieldParameterName' => 'sort_by',
                    'filterFieldParameterName' => 'filter',
                    'distinct' => true
                ]
            );

            // Rendre le fragment HTML pour le tableau des utilisateurs
            $html = $this->renderView('user/_users_table.html.twig', [
                'users' => $users,
                'criteria' => $criteria,
                'routeParams' => $routeParams
            ]);

            // Retourner la réponse JSON avec le HTML et les informations de pagination
            return new JsonResponse([
                'html' => $html,
                'pagination' => [
                    'current' => $users->getCurrentPageNumber(),
                    'total' => ceil($users->getTotalItemCount() / $users->getItemNumberPerPage()),
                    'count' => $users->getTotalItemCount()
                ],
                'success' => true
            ]);
        } catch (\Exception $e) {
            // En production, vous pouvez activer cette ligne pour le logging
            // error_log('Erreur AJAX: ' . $e->getMessage());

            // Retourner une réponse d'erreur simplifiée
            return new JsonResponse([
                'error' => true,
                'message' => 'Une erreur est survenue lors du chargement des utilisateurs. Veuillez réinitialiser les filtres ou rafraîchir la page.'
            ], 500);
        }
    }

    /**
     * Récupère et nettoie les critères de recherche
     *
     * @param Request $request
     * @return array
     */
    private function getSanitizedCriteria(Request $request): array
    {
        // Récupérer les critères de base
        $criteria = [
            'search' => trim($request->query->get('search', '')),
            'role' => trim($request->query->get('role', '')),
            'is_verified' => $request->query->get('is_verified', ''),
            'sort_by' => strtolower(trim($request->query->get('sort_by', ''))),
            'sort_dir' => strtolower(trim($request->query->get('sort_dir', '')))
        ];

        // Valider les critères de tri
        $validSortFields = ['name', 'salary'];
        if (!in_array($criteria['sort_by'], $validSortFields)) {
            $criteria['sort_by'] = '';
        }

        $validSortDirections = ['asc', 'desc'];
        if (!in_array($criteria['sort_dir'], $validSortDirections)) {
            $criteria['sort_dir'] = 'asc';
        }

        return $criteria;
    }

    #[Route('/users/stats', name: 'app_ajax_users_stats', methods: ['GET'])]
    public function getUserStats(UserRepository $userRepository): JsonResponse
    {
        // Récupérer les statistiques
        $roleStats = $userRepository->getUserStatsByRole();
        $salaryStat = $userRepository->getAverageSalaryByRole();

        return new JsonResponse([
            'roles' => $roleStats,
            'salaries' => $salaryStat
        ]);
    }
}
