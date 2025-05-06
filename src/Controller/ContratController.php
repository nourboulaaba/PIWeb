<?php
namespace App\Controller;

use App\Entity\Contrat;
use App\Form\ContratType;
use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contrat')]
class ContratController extends AbstractController
{
    #[Route('/', name: 'app_contrat_index', methods: ['GET'])]
    public function index(
        Request $request,
        ContratRepository $contratRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $qb = $contratRepository
            ->createQueryBuilder('c')
            ->orderBy('c.idContrat', 'DESC');

        $page  = $request->query->getInt('page', 1);
        $limit = 10;

        $pagination = $paginator->paginate($qb, $page, $limit);

        return $this->render('contrat/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_contrat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $contrat = new Contrat();
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contrat);
            $em->flush();
            $this->addFlash('success', 'Contrat créé avec succès.');
            return $this->redirectToRoute('app_contrat_index');
        }

        return $this->render('contrat/new.html.twig', [
            'contrat' => $contrat,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{IdContrat}', name: 'app_contrat_show', methods: ['GET'])]
    public function show(int $IdContrat, ContratRepository $repo): Response
    {
        $contrat = $repo->find($IdContrat);
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé.');
        }
        return $this->render('contrat/show.html.twig', [
            'contrat' => $contrat,
        ]);
    }

    #[Route('/{IdContrat}/edit', name: 'app_contrat_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        int $IdContrat,
        ContratRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $contrat = $repo->find($IdContrat);
        if (!$contrat) {
            throw $this->createNotFoundException('Contrat non trouvé.');
        }
        $form = $this->createForm(ContratType::class, $contrat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_contrat_index');
        }

        return $this->render('contrat/edit.html.twig', [
            'contrat' => $contrat,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{IdContrat}/delete', name: 'app_contrat_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        int $IdContrat,
        ContratRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $contrat = $repo->find($IdContrat);
        if ($this->isCsrfTokenValid('delete'.$IdContrat, $request->request->get('_token'))) {
            $em->remove($contrat);
            $em->flush();
            $this->addFlash('success', 'Contrat supprimé.');
        }
        return $this->redirectToRoute('app_contrat_index');
    }

    #[Route('/{id}/pdf', name: 'contrat_pdf', methods: ['GET'])]
    public function pdf(Contrat $contrat, Pdf $knpSnappyPdf): Response
    {
        $html = $this->renderView('contrat/pdf.html.twig', [
            'contrat' => $contrat,
        ]);

        $filename = sprintf('contrat_%d.pdf', $contrat->getIdContrat());
        return new Response(
            $knpSnappyPdf->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]
        );
    }
}
