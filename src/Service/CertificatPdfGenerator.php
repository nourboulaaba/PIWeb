<?php

namespace App\Service;

use App\Entity\Certificat;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class CertificatPdfGenerator
{
    private $twig;
    private $projectDir;

    public function __construct(Environment $twig, string $projectDir)
    {
        $this->twig = $twig;
        $this->projectDir = $projectDir;
    }

    public function generatePdf(Certificat $certificat): Response
    {
        // Vérifier si le certificat a été validé
        if ($certificat->getResultatExamen() !== 'Réussi') {
            throw new \Exception('Impossible de générer un certificat pour un examen non réussi.');
        }

        // Configurer les options de DOMPDF
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        // Créer une instance de DOMPDF
        $dompdf = new Dompdf($options);

        // Générer le HTML du certificat
        $html = $this->twig->render('certificat/pdf/template.html.twig', [
            'certificat' => $certificat,
            'date' => new \DateTime(),
            'logo_path' => $this->projectDir . '/public/images/logo.png'
        ]);

        // Charger le HTML dans DOMPDF
        $dompdf->loadHtml($html);

        // Définir le format du papier (A4 paysage)
        $dompdf->setPaper('A4', 'landscape');

        // Rendre le PDF
        $dompdf->render();

        // Générer le nom du fichier
        $filename = sprintf(
            'certificat_%s_%s.pdf',
            $certificat->getFormation()->getName(),
            $certificat->getIdCertif()
        );

        // Créer une réponse HTTP avec le PDF
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}
