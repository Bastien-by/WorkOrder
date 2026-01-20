<?php

namespace App\Controller;

use TCPDF;
use App\Entity\WorkOrder;
use App\Form\WorkOrderRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkOrderController extends AbstractController
{
    #[Route('/create', name: 'work_order_generator')]
    public function generator(Request $request, EntityManagerInterface $entityManager): Response
    {
        $request->getSession()->start();

        $workOrder = new WorkOrder();

        $requesters = file_exists($this->getParameter('kernel.project_dir').'/config/workers.php')
            ? require $this->getParameter('kernel.project_dir').'/config/workers.php'
            : ['default_user'];

        $WorkOrderRequestForm = $this->createForm(WorkOrderRequestType::class, $workOrder, [
            'requesters' => $requesters['intervention_requesters'] ?? [],
        ]);
        $WorkOrderRequestForm->handleRequest($request);

        if ($WorkOrderRequestForm->isSubmitted() && $WorkOrderRequestForm->isValid()) {
            $workOrder->setStatus(true);

            $entityManager->persist($workOrder);
            $entityManager->flush();

            $this->generatePDF($workOrder);

            $this->addFlash('success', 'Ordre de Travail enregistré avec succès !');
            return $this->redirectToRoute('work_order_generator');
        }

        return $this->render('work_order.html.twig', [
            'WorkOrderRequestForm' => $WorkOrderRequestForm->createView(),
        ]);
    }

    private function generatePDF(WorkOrder $workOrder): void
    {
        $sector = $workOrder->getSector();
        $machineName = $workOrder->getMachineName();
        $interventionRequestDate = $workOrder->getInterventionRequestDate()?->format('d/m/Y');
        $interventionRequestDateFile = $workOrder->getInterventionRequestDate()?->format('d-m-Y');
        $id_ot = $workOrder->getId();

        // Nettoie les valeurs pour le nom de fichier
        $sectorClean = str_replace(' ', '_', $sector ?? 'NO_SECTOR');
        $machineClean = str_replace(' ', '_', $machineName ?? 'NO_MACHINE');

        $filePath = "/var/www/WorkOrder/pdfot/{$id_ot}-{$sectorClean}-{$machineClean}-{$interventionRequestDateFile}-ot.pdf";

        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        // === EN-TÊTE ===
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/images/OPMobility.jpg', 10, 10, 40, 15, 'JPG');

        // Titre principal
        $pdf->SetX(50);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->Cell(0, 15, 'ORDRE DE MAINTENANCE TRACKING', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, strtoupper($this->getMonthName($workOrder->getInterventionRequestDate())), 0, 1, 'C');

        $pdf->Ln(5);

        // === TABLEAU 2 COLONNES ===
        $this->drawTwoColumnLayout($pdf, $workOrder, $interventionRequestDate);

        // === DURÉE PANNE/INTERVENTION ===
        $this->drawDowntimeSection($pdf, $workOrder);

        // === DESCRIPTION TECHNIQUE ===
        $this->drawTechnicalDescriptionSection($pdf, $workOrder);

        // === GESTION MAGASIN PIÈCES ===
        $this->drawSparePartSection($pdf, $workOrder);

        $pdf->Output($filePath, 'F');
    }

    private function drawTwoColumnLayout(TCPDF $pdf, WorkOrder $workOrder, string $interventionRequestDate): void
    {
        $leftColX = 10;
        $rightColX = 110;
        $leftColWidth = 90;
        $rightColWidth = 90;
        $rowHeight = 11;

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        $startY = $pdf->GetY();

        // === COLONNE GAUCHE ===
        $pdf->SetY($startY);
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'N° ORDRE TRAVAIL', (string)$workOrder->getId());

        $pdf->SetY($startY + $rowHeight);
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'STATUT OT', $workOrder->getStatus() ? 'OUVERT' : 'CLOTURE');

        $pdf->SetY($startY + ($rowHeight * 2));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'NOM(S) INTERVENANTS', $workOrder->getTechnicianName() ?? '');

        $pdf->SetY($startY + ($rowHeight * 3));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'DATE INTERVENTION', $interventionRequestDate);

        $pdf->SetY($startY + ($rowHeight * 4));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'DEMANDEUR', $workOrder->getInterventionRequester() ?? '');

        $pdf->SetY($startY + ($rowHeight * 5));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'TYPE MAINTENANCE', $workOrder->getMaintenanceType() ?? '');

        $pdf->SetY($startY + ($rowHeight * 6));
        $closureDate = $workOrder->getDowntimeEndTime()?->format('d/m/Y') ?? '';
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'DATE CLOTURE', $closureDate);

        // === COLONNE DROITE ===
        $pdf->SetY($startY);
        $this->drawTableRowHighlight($pdf, $rightColX, $rightColWidth, $rowHeight, 'SECTEUR', $workOrder->getSector() ?? '');

        $pdf->SetY($startY + $rowHeight);
        $this->drawTableRowHighlight($pdf, $rightColX, $rightColWidth, $rowHeight, 'NOM MACHINE', $workOrder->getMachineName() ?? '');

        $pdf->SetY($startY + ($rowHeight * 2));
        $this->drawTableRowHighlight($pdf, $rightColX, $rightColWidth, $rowHeight, 'DOMAINE', $workOrder->getFieldIntervention() ?? '');

        $pdf->SetY($startY + ($rowHeight * 7));
        $pdf->Ln(5);
    }

    private function drawTableRow(TCPDF $pdf, float $x, float $width, float $height, string $label, string $value): void
    {
        $pdf->SetX($x);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell($width * 0.40, $height, $label, 1, 0, 'L', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell($width * 0.60, $height, $value, 1, 0, 'L');
    }

    private function drawTableRowHighlight(TCPDF $pdf, float $x, float $width, float $height, string $label, string $value): void
    {
        $pdf->SetX($x);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(200, 230, 255);
        $pdf->Cell($width * 0.40, $height, $label, 1, 0, 'L', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(240, 248, 255);
        $pdf->Cell($width * 0.60, $height, $value, 1, 0, 'L', true);
    }

    private function drawDowntimeSection(TCPDF $pdf, WorkOrder $workOrder): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 32, 96);
        $pdf->Cell(0, 8, 'DUREE PANNE/INTERVENTION', 0, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln(3);

        $colWidth = 47.5;
        $rowHeight = 10;

        // En-tête
        $pdf->SetFillColor(100, 100, 100);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);


        $pdf->Cell($colWidth, $rowHeight, 'HEURE DEBUT PANNE', 1, 0, 'C', true);
        $pdf->Cell($colWidth, $rowHeight, 'HEURE FIN PANNE', 1, 0, 'C', true);
        $pdf->Cell($colWidth, $rowHeight, 'HEURE DEBUT INTERVENTION', 1, 0, 'C', true);
        $pdf->Cell($colWidth, $rowHeight, 'HEURE FIN INTERVENTION', 1, 1, 'C', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(220, 220, 220);

        // Données (vides pour ordre en cours)
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getDowntimeStartTime()?->format('H:i') ?? '', 1, 0, 'C');
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getDowntimeEndTime()?->format('H:i') ?? '', 1, 0, 'C');
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getInterventionStartTime()?->format('H:i') ?? '', 1, 0, 'C');
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getInterventionEndTime()?->format('H:i') ?? '', 1, 1, 'C');

        $pdf->Ln(2);

        // Durées
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(95, $rowHeight, 'DUREE PANNE', 1, 0, 'C', true);
        $pdf->Cell(95, $rowHeight, 'DUREE INTERVENTION', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(240, 240, 240);
        $dowMinutes = $workOrder->getDowntimeTime() ?? 0;
        $intMinutes = $workOrder->getInterventionTime() ?? 0;

        $pdf->Cell(95, $rowHeight, $this->minutesToHhMm($dowMinutes), 1, 0, 'C', true);
        $pdf->Cell(95, $rowHeight, $this->minutesToHhMm($intMinutes), 1, 1, 'C', true);

        $pdf->Ln(5);
    }

    private function drawTechnicalDescriptionSection(TCPDF $pdf, WorkOrder $workOrder): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 32, 96);
        $pdf->Cell(0, 8, 'DESCRIPTIF INTERVENTION TECHNIQUE', 0, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // Calcul automatique de la largeur disponible
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $availableWidth = $pageWidth - $margins['left'] - $margins['right'];

        $descWidth = $availableWidth * 0.55;
        $photoWidth = $availableWidth * 0.45;

        // Pas de SetX() - laisse la position courante
        $pdf->SetFillColor(100, 100, 100);
        $pdf->Cell($descWidth, 8, 'DESCRIPTION INTERVENTION', 1, 0, 'C', true);
        $pdf->Cell($photoWidth, 8, 'PHOTO(S)', 1, 1, 'C', true);

        $pdf->SetTextColor(0, 0, 0);
        $startY = $pdf->GetY();

        // Description
        $pdf->MultiCell($descWidth, 5, $workOrder->getTechnicalDetails() ?? '', 1, 'L');

        $endY = $pdf->GetY();
        $cellHeight = $endY - $startY;

        // Photo - utilise GetX() pour la position actuelle
        $currentX = $pdf->GetX();
        $photoX = $pdf->getPageWidth() - $margins['right'] - $photoWidth;

        $pdf->SetY($startY);
        $pdf->SetX($photoX);
        $pdf->Rect($photoX, $startY, $photoWidth, $cellHeight);

        $pdf->SetY($endY);
        $pdf->Ln(5);
    }

    private function drawSparePartSection(TCPDF $pdf, WorkOrder $workOrder): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 32, 96);
        $pdf->Cell(0, 8, 'GESTION MAGASIN PIECES DE RECHANGE', 0, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->Ln(3);

        // Calcul automatique de la largeur disponible
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $availableWidth = $pageWidth - $margins['left'] - $margins['right'];

        $col1Width = $availableWidth * 0.55;
        $col2Width = $availableWidth * 0.225;
        $col3Width = $availableWidth * 0.225;

        // === LIGNE D'EN-TÊTE ===
        $pdf->SetFillColor(100, 100, 100);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);

        $pdf->Cell($col1Width, 8, 'SORTIE PIECES', 1, 0, 'C', true);
        $pdf->Cell($col2Width, 8, 'NON', 1, 0, 'C', true);
        $pdf->Cell($col3Width, 8, 'SI NON', 1, 1, 'C', true);

        // === LIGNE DE CONTENU ===
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);

        $pdf->Cell($col1Width, 8, 'BESOIN A REPORTER DANS FICHIER SPARE PART LIST :', 1, 0, 'L', true);
        $pdf->Cell($col2Width, 8, '', 1, 0, 'C', true);
        $pdf->Cell($col3Width, 8, '', 1, 1, 'C', true);

        // === LIGNE REMARQUE ===
        $pdf->Cell($col1Width, 8, 'Remarque :', 1, 0, 'L', true);

        $pdf->Ln(5);
    }




    private function getMonthName(\DateTimeInterface $date = null): string
    {
        if (!$date) return 'N/A';

        $months = ['', 'JANVIER', 'FEVRIER', 'MARS', 'AVRIL', 'MAI', 'JUIN',
            'JUILLET', 'AOUT', 'SEPTEMBRE', 'OCTOBRE', 'NOVEMBRE', 'DECEMBRE'];
        return $months[(int)$date->format('n')];
    }

    private function minutesToHhMm(?int $minutes): string
    {
        if ($minutes === null || $minutes === 0) return '';
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }
}
