<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExcelExportService
{
    /**
     * Exporte des données vers un fichier Excel
     *
     * @param array $headers Les en-têtes du tableau
     * @param array $data Les données à exporter
     * @param string $filename Le nom du fichier (sans extension)
     * @return Response
     */
    public function exportToExcel(array $headers, array $data, string $filename): Response
    {
        // Création d'un nouveau document Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Définition du style pour les en-têtes
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        
        // Définition du style pour les données
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        
        // Ajout des en-têtes
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }
        
        // Application du style aux en-têtes
        $lastColumn = chr(64 + count($headers)); // Conversion du nombre en lettre (A, B, C, etc.)
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        
        // Ajout des données
        $row = 2;
        foreach ($data as $rowData) {
            $column = 'A';
            foreach ($rowData as $cellData) {
                $sheet->setCellValue($column . $row, $cellData);
                $column++;
            }
            $row++;
        }
        
        // Application du style aux données
        $sheet->getStyle('A2:' . $lastColumn . ($row - 1))->applyFromArray($dataStyle);
        
        // Ajustement automatique de la largeur des colonnes
        foreach (range('A', $lastColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        
        // Création du fichier Excel
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($temp_file);
        
        // Création de la réponse HTTP
        $response = new Response(file_get_contents($temp_file));
        unlink($temp_file); // Suppression du fichier temporaire
        
        // Définition des en-têtes HTTP
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename . '.xlsx'
        );
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}
