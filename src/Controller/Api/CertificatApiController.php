<?php

namespace App\Controller\Api;

use App\Entity\Certificat;
use App\Repository\CertificatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/certificats')]
class CertificatApiController extends AbstractController
{
    private CertificatRepository $certificatRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CertificatRepository $certificatRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->certificatRepository = $certificatRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'api_certificats_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Récupérer les paramètres de pagination
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        // Construire la requête
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')
           ->from(Certificat::class, 'c')
           ->leftJoin('c.formation', 'f')
           ->orderBy('c.dateExamen', 'DESC');

        // Pagination
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        // Exécuter la requête
        $certificats = $qb->getQuery()->getResult();

        // Compter le nombre total de résultats pour la pagination
        $countQb = clone $qb;
        $countQb->select('COUNT(c.idCertif)')
                ->setFirstResult(null)
                ->setMaxResults(null);
        $total = $countQb->getQuery()->getSingleScalarResult();

        // Formater les résultats
        $formattedCertificats = [];
        foreach ($certificats as $certificat) {
            $formation = $certificat->getFormation();
            $formattedCertificats[] = [
                'id' => $certificat->getIdCertif(),
                'formation' => $formation ? [
                    'id' => $formation->getId(),
                    'name' => $formation->getName()
                ] : null,
                'dateExamen' => $certificat->getDateExamen()->format('Y-m-d'),
                'heure' => $certificat->getHeure()->format('H:i'),
                'duree' => $certificat->getDuree(),
                'prixExam' => $certificat->getPrixExam(),
                'niveau' => $certificat->getNiveau(),
                'resultatExamen' => $certificat->getResultatExamen(),
                'dateReprogrammation' => $certificat->getDateReprogrammation() ?
                    $certificat->getDateReprogrammation()->format('Y-m-d') : null
            ];
        }

        return new JsonResponse([
            'certificats' => $formattedCertificats,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($total / $limit)
        ]);
    }

    #[Route('/test', name: 'api_certificats_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'API de certificats fonctionne correctement',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }


}
