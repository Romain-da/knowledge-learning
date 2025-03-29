<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfGenerator
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function generate(string $template, array $data = []): string
    {
        // Configure Dompdf selon vos besoins
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);

        // Rendre le template avec les données fournies
        $html = $this->twig->render($template, $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Retourne le contenu du PDF en tant que chaîne
        return $dompdf->output();
    }
}