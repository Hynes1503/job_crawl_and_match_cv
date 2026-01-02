<?php

namespace App\Helpers;

use PhpOffice\PhpWord\IOFactory as PhpWordIOFactory;
use Smalot\PdfParser\Parser;

class CvTextExtractor
{
    public static function extract(string $content, string $extension): string
    {
        $extension = strtolower($extension);

        if ($extension === 'pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseContent($content);
                return trim($pdf->getText());
            } catch (\Exception $e) {
                \Log::warning('PDF extract error: ' . $e->getMessage());
                return '';
            }
        } elseif (in_array($extension, ['docx', 'doc'])) {
            try {
                $tempPath = tempnam(sys_get_temp_dir(), 'cv_doc_');
                file_put_contents($tempPath, $content);
                $phpWord = PhpWordIOFactory::load($tempPath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        }
                    }
                }
                unlink($tempPath);
                return trim($text);
            } catch (\Exception $e) {
                \Log::warning('DOCX extract error: ' . $e->getMessage());
                return '';
            }
        } elseif ($extension === 'txt') {
            return trim($content);
        }

        return '';
    }
}