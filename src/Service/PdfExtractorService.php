<?php

namespace App\Service;

use Smalot\PdfParser\Parser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PdfExtractorService
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extract text content from a PDF file path.
     *
     * @param string $filePath The full path to the PDF file
     *
     * @return string The extracted text
     */
    public function extractTextFromPdf(string $filePath): string
    {
        // Check file existence and extension
        if (!file_exists($filePath) || mime_content_type($filePath) !== 'application/pdf') {
            throw new BadRequestHttpException('The provided file must be a valid PDF.');
        }

        try {
            $pdf = $this->parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Error extracting text from PDF: ' . $e->getMessage());
        }
    }
}
