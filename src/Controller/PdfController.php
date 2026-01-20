<?php

namespace App\Controller;

use App\Entity\WorkOrder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PdfController extends AbstractController
{
    #[Route('/pdfs', name: 'list_pdfs')]
    public function listPdfs(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pdfDirectory = '/var/www/WorkOrder/pdfot';

        // Récupération des filtres via la query string
        $selectedRequester = $request->query->get('requester');
        $selectedMachine = $request->query->get('machine');
        $selectedDate = $request->query->get('date');

        // Récupération de tous les WorkOrders
        $repo = $entityManager->getRepository(WorkOrder::class);
        $allOrders = $repo->findAll();

        // Construction des listes uniques pour les filtres
        $requesters = array_unique(array_filter(array_map(fn($o) => $o->getInterventionRequester(), $allOrders)));
        $machines = array_unique(array_filter(array_map(fn($o) => $o->getMachineName(), $allOrders)));
        $dates = array_unique(array_filter(array_map(function ($o) {
            return $o->getInterventionRequestDate()?->format('d-m-Y');
        }, $allOrders)));

        sort($requesters);
        sort($machines);
        sort($dates);

        // Application des filtres
        $filteredOrders = array_filter($allOrders, function (WorkOrder $order) use ($selectedRequester, $selectedMachine, $selectedDate) {
            $match = true;

            if ($selectedRequester && $order->getInterventionRequester() !== $selectedRequester) {
                $match = false;
            }

            if ($selectedMachine && $order->getMachineName() !== $selectedMachine) {
                $match = false;
            }

            if ($selectedDate && $order->getInterventionRequestDate()?->format('d-m-Y') !== $selectedDate) {
                $match = false;
            }

            return $match;
        });

        // Génération des noms de fichiers attendus (FORMAT UNIFIÉ avec secteur)
        $getFileName = function (WorkOrder $order) use ($pdfDirectory) {
            $id = $order->getId();
            $sector = $order->getSector();
            $machine = $order->getMachineName();
            $date = $order->getInterventionRequestDate()?->format('d-m-Y');

            // Nettoie les valeurs
            $sectorClean = str_replace(' ', '_', $sector ?? 'NO_SECTOR');
            $machineClean = str_replace(' ', '_', $machine ?? 'NO_MACHINE');

            // Format unique : {id}-{sector}-{machine}-{date}-ot.pdf
            $filename = "{$id}-{$sectorClean}-{$machineClean}-{$date}-ot.pdf";

            return file_exists("$pdfDirectory/$filename") ? $filename : null;
        };

        // Tri en deux groupes : en cours / fermés
        $activePdfs = array_filter(array_map($getFileName, array_filter($filteredOrders, fn($o) => $o->isStatus())));
        $closedPdfs = array_filter(array_map($getFileName, array_filter($filteredOrders, fn($o) => !$o->isStatus())));

        return $this->render('pdf.html.twig', [
            'activePdfs' => $activePdfs,
            'closedPdfs' => $closedPdfs,
            'pdfDirectory' => $pdfDirectory,
            'requesters' => $requesters,
            'machines' => $machines,
            'dates' => $dates,
            'selectedRequester' => $selectedRequester,
            'selectedMachine' => $selectedMachine,
            'selectedDate' => $selectedDate,
        ]);
    }

    #[Route('/view-pdf/{filename}', name: 'view_pdf')]
    public function viewPdf(string $filename): Response
    {
        $pdfDirectory = '/var/www/WorkOrder/pdfot';
        $pdfPath = "$pdfDirectory/$filename";

        if (!file_exists($pdfPath)) {
            throw $this->createNotFoundException("Le fichier PDF n'existe pas.");
        }

        return new Response(file_get_contents($pdfPath), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

}
