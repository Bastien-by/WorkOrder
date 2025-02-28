<?php
// src/Controller/PdfController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;

class PdfController extends AbstractController
{
    #[Route('/pdfs', name: 'list_pdfs')]
    public function listPdfs(): Response
    {
        // Définir le répertoire où les PDF sont stockés
        $pdfDirectory = '/var/www/WorkOrder/pdfot'; // Remplace par le chemin du répertoire où tu stockes les PDF

        // Utiliser Symfony Finder pour lister les fichiers PDF dans ce répertoire
        $finder = new Finder();
        $finder->files()->in($pdfDirectory)->name('*.pdf'); // Filtrer uniquement les fichiers PDF

        // Créer un tableau pour stocker les fichiers PDF
        $pdfFiles = [];
        foreach ($finder as $file) {
            $pdfFiles[] = $file->getFilename(); // Ajouter le nom de chaque fichier PDF à la liste
        }

        // Passer la liste des fichiers PDF à la vue
        return $this->render('pdf.html.twig', [
            'pdfFiles' => $pdfFiles,
            'pdfDirectory' => $pdfDirectory
        ]);
    }

    // Une autre méthode pour afficher le PDF en cliquant
    #[Route('/view-pdf/{filename}', name: 'view_pdf')]
    public function viewPdf(string $filename): Response
    {
        // Le répertoire où les fichiers sont stockés
        $pdfDirectory = '/var/www/WorkOrder/pdfot'; // Remplace par ton répertoire

        // Vérifier si le fichier PDF existe
        $pdfPath = $pdfDirectory . '/' . $filename;
        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException('Le fichier PDF n\'existe pas.');
        }

        // Retourner le fichier PDF pour qu'il s'affiche dans le navigateur
        return $this->file($pdfPath);
    }
}
