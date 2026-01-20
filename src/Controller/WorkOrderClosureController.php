<?php

namespace App\Controller;

use App\Entity\WorkOrder;
use App\Form\WorkOrderClosureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;

class WorkOrderClosureController extends AbstractController
{
    #[Route('/edit', name: 'work_order_edit_list')]
    public function listOpenOrders(EntityManagerInterface $entityManager): Response
    {
        $pdfDirectory = '/var/www/WorkOrder/pdfot';

        // Récupération des OT actifs
        $orders = $entityManager->getRepository(WorkOrder::class)->findBy(['status' => true]);

        // Génération des noms de fichiers PDF attendus
        $getFileName = function (WorkOrder $order) use ($pdfDirectory) {
            $id = $order->getId();
            $sector = $order->getSector();
            $machine = $order->getMachineName();
            $date = $order->getInterventionRequestDate()?->format('d-m-Y');

            // Nettoie les valeurs
            $sectorClean = str_replace(' ', '_', $sector ?? 'NO_SECTOR');
            $machineClean = str_replace(' ', '_', $machine ?? 'NO_MACHINE');

            // Format unifié avec secteur
            $filename = "{$id}-{$sectorClean}-{$machineClean}-{$date}-ot.pdf";

            return file_exists("$pdfDirectory/$filename") ? $filename : null;
        };

        // Liste des fichiers PDF actifs
        $activePdfs = array_filter(array_map($getFileName, $orders));

        return $this->render('work_order_edit.html.twig', [
            'activePdfs' => $activePdfs,
            'WorkOrderClosureForm' => null,
            'workOrder' => null,
        ]);
    }

    #[Route('/edit/{id}', name: 'work_order_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $workOrder = $entityManager->getRepository(WorkOrder::class)->find($id);

        if (!$workOrder) {
            throw $this->createNotFoundException('Ordre de travail introuvable.');
        }

        $form = $this->createForm(WorkOrderClosureType::class, $workOrder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // === UPLOAD PHOTO DESCRIPTIVE ===
            $photoFile = $form->get('descriptionPhoto')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/photos/',
                    $newFilename
                );

                // Sauvegarde le chemin
                $workOrder->setDescriptionPhoto('/uploads/photos/'.$newFilename);
            }

            // Marque comme clôturé
            $workOrder->setStatus(false);

            // Downtime en minutes
            $start = $workOrder->getDowntimeStartTime();
            $end = $workOrder->getDowntimeEndTime();

            if ($start && $end) {
                $interval = $start->diff($end);
                $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                $workOrder->setDowntimeTime($minutes);
            }

            // Intervention en minutes
            $intStart = $workOrder->getInterventionStartTime();
            $intEnd = $workOrder->getInterventionEndTime();

            if ($intStart && $intEnd) {
                $interval = $intStart->diff($intEnd);
                $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                $workOrder->setInterventionTime($minutes);
            }

            $entityManager->flush();

            // Génère le PDF de clôture
            $this->generateClosurePDF($workOrder);

            $this->addFlash('success', "L'ordre de travail #{$id} a été modifié et clôturé.");
            return $this->redirectToRoute('work_order_edit_list');
        }

        return $this->render('work_order_edit.html.twig', [
            'WorkOrderClosureForm' => $form->createView(),
            'workOrder' => $workOrder,
            'activePdfs' => null, // Pas de liste quand on édite un OT spécifique
        ]);
    }

    private function minutesToHhMm(?int $minutes): string
    {
        if ($minutes === null) {
            return 'N/A';
        }
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%02d:%02d', $h, $m);
    }




    private function drawSection(TCPDF $pdf, string $title, callable $contentCallback): void
    {
        if ($pdf->getPage() > 0) {
            $pdf->Ln(8);
        }

        $pdf->SetFillColor(200, 220, 255);
        $pdf->SetTextColor(14, 52, 113);
        $pdf->SetFont('helvetica', 'B', 14);

        $pdf->SetX(15);
        $pdf->Cell(180, 10, $title, 0, 1, 'C', 1);

        $startY = $pdf->GetY();

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', '', 12);

        $contentCallback();

        $endY = $pdf->GetY();
        $boxHeight = $endY - $startY;

        $pdf->Rect(15, $startY, 180, $boxHeight);
    }


    private function generateClosurePDF(WorkOrder $workOrder): void
    {
        $machineName = $workOrder->getMachineName();
        $sector = $workOrder->getSector();
        $interventionDescription = $workOrder->getInterventionDescription();
        $interventionRequestDate = $workOrder->getInterventionRequestDate()?->format('d/m/Y');
        $interventionRequestDateFile = $workOrder->getInterventionRequestDate()?->format('d-m-Y');
        $id_ot = $workOrder->getId();

        // Nettoie les valeurs pour le filename
        $sectorClean = str_replace(' ', '_', $sector ?? 'NO_SECTOR');
        $machineNameClean = str_replace(' ', '_', $machineName ?? 'NO_MACHINE');

        $filePath = "/var/www/WorkOrder/pdfot/{$id_ot}-{$sectorClean}-{$machineNameClean}-{$interventionRequestDateFile}-ot.pdf";

        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);

        // === EN-TÊTE ===
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/images/OPMobility.jpg', 10, 10, 40, 15, 'JPG');

        // Titre principal
        $pdf->SetX(50);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(0, 0, 128); // Bleu foncé
        $pdf->Cell(0, 15, 'ORDRE DE MAINTENANCE TRACKING', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, strtoupper($this->getMonthName($workOrder->getInterventionRequestDate())), 0, 1, 'C');

        $pdf->Ln(5);

        // === TABLEAU 2 COLONNES (GAUCHE/DROITE) ===
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
        $rightColX = 115;
        $leftColWidth = 95;
        $rightColWidth = 90;
        $rowHeight = 10;

        $pdf->SetFont('helvetica', '', 9);  // Réduit de 10 à 9
        $pdf->SetTextColor(0, 0, 0);

        $startY = $pdf->GetY();

        // === COLONNE GAUCHE ===
        $pdf->SetY($startY);
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'N° ORDRE TRAVAIL', (string)$workOrder->getId());

        $pdf->SetY($startY + $rowHeight);
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'STATUT OT', $workOrder->getStatus() ? 'OUVERT' : 'CLOTURE');

        $pdf->SetY($startY + ($rowHeight * 2));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'NOM(S) INTERVENANTS', $workOrder->getTechnicianName() ?? 'N/A');

        $pdf->SetY($startY + ($rowHeight * 3));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'DATE INTERVENTION', $interventionRequestDate);

        $pdf->SetY($startY + ($rowHeight * 4));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'DEMANDEUR INTERVENTION', $workOrder->getInterventionRequester() ?? 'N/A');

        $pdf->SetY($startY + ($rowHeight * 5));
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'TYPE MAINTENANCE', $workOrder->getMaintenanceType() ?? 'N/A');

        $pdf->SetY($startY + ($rowHeight * 6));
        $closureDate = $workOrder->getDowntimeEndTime()?->format('d/m/Y') ?? 'N/A';
        $this->drawTableRow($pdf, $leftColX, $leftColWidth, $rowHeight, 'DATE CLOTURE', $closureDate);

        // === COLONNE DROITE ===
        $pdf->SetY($startY);
        $this->drawTableRowHighlight($pdf, $rightColX, $rightColWidth, $rowHeight, 'SECTEUR', $workOrder->getSector() ?? 'N/A');

        $pdf->SetY($startY + $rowHeight);
        $this->drawTableRowHighlight($pdf, $rightColX, $rightColWidth, $rowHeight, 'NOM MACHINE', $workOrder->getMachineName() ?? 'N/A');

        $pdf->SetY($startY + ($rowHeight * 2));
        $this->drawTableRowHighlight($pdf, $rightColX, $rightColWidth, $rowHeight, 'DOMAINE INTERVENTION', $workOrder->getFieldIntervention() ?? 'N/A');

        // Repositionne après les 2 colonnes
        $pdf->SetY($startY + ($rowHeight * 7));
        $pdf->Ln(5);
    }

    private function drawTableRow(TCPDF $pdf, float $x, float $width, float $height, string $label, string $value): void
    {
        $pdf->SetX($x);
        $pdf->SetFont('helvetica', 'B', 8);  // Label en gras, taille 8
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell($width * 0.45, $height, $label, 1, 0, 'L', true);

        $pdf->SetFont('helvetica', '', 9);  // Valeur normale, taille 9
        $pdf->Cell($width * 0.55, $height, $value, 1, 0, 'L');
    }

    private function drawTableRowHighlight(TCPDF $pdf, float $x, float $width, float $height, string $label, string $value): void
    {
        $pdf->SetX($x);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(200, 230, 255); // Bleu clair
        $pdf->Cell($width * 0.45, $height, $label, 1, 0, 'L', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(240, 248, 255);
        $pdf->Cell($width * 0.55, $height, $value, 1, 0, 'L', true);
    }

    private function drawDowntimeSection(TCPDF $pdf, WorkOrder $workOrder): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 32, 96); // Bleu foncé
        $pdf->Cell(0, 8, 'DUREE PANNE/INTERVENTION', 0, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Ln(3);

        // Largeur totale disponible (marges prises en compte)
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $availableWidth = $pageWidth - $margins['left'] - $margins['right'];

        $colWidth = $availableWidth / 4;
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

        // Données (heure seule ici, tu peux remettre ton format jour+date si tu veux)
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getDowntimeStartTime()?->format('d/m/Y H:i') ?? '', 1, 0, 'C');
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getDowntimeEndTime()?->format('d/m/Y H:i') ?? '', 1, 0, 'C');
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getInterventionStartTime()?->format('d/m/Y H:i') ?? '', 1, 0, 'C');
        $pdf->Cell($colWidth, $rowHeight, $workOrder->getInterventionEndTime()?->format('d/m/Y H:i') ?? '', 1, 1, 'C');

        $pdf->Ln(2);

        // Durées
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell($availableWidth / 2, $rowHeight, 'DUREE PANNE', 1, 0, 'C', true);
        $pdf->Cell($availableWidth / 2, $rowHeight, 'DUREE INTERVENTION', 1, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetFillColor(240, 240, 240);
        $dowMinutes = $workOrder->getDowntimeTime() ?? 0;
        $intMinutes = $workOrder->getInterventionTime() ?? 0;

        $pdf->Cell($availableWidth / 2, $rowHeight, $this->minutesToHhMm($dowMinutes), 1, 0, 'C', true);
        $pdf->Cell($availableWidth / 2, $rowHeight, $this->minutesToHhMm($intMinutes), 1, 1, 'C', true);

        $pdf->Ln(5);
    }


    /**
     * Formate une DateTime pour l'affichage dans le PDF
     * Format: "Lun. 19/01/2026\n11:26"
     */
    private function formatDateTimeForPdf(?\DateTime $dateTime): string
    {
        if (!$dateTime) {
            return '';
        }

        // Créer une locale française
        $locale = 'fr_FR';

        // Jour court (Lun., Mar., etc.)
        $dayName = match((int)$dateTime->format('N')) {
            1 => 'Lun.',
            2 => 'Mar.',
            3 => 'Mer.',
            4 => 'Jeu.',
            5 => 'Ven.',
            6 => 'Sam.',
            7 => 'Dim.',
            default => '',
        };

        // Date au format d/m/Y et heure
        $date = $dateTime->format('d/m/Y');
        $time = $dateTime->format('H:i');

        return $dayName . ' ' . $date . "\n" . $time;
    }



    private function drawTechnicalDescriptionSection(TCPDF $pdf, WorkOrder $workOrder): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 32, 96); // Bleu foncé
        $pdf->Cell(0, 8, 'DESCRIPTIF INTERVENTION TECHNIQUE', 0, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // Largeur dispo
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $availableWidth = $pageWidth - $margins['left'] - $margins['right'];

        $descWidth = $availableWidth * 0.55;
        $photoWidth = $availableWidth * 0.45;

        // En-tête colonnes
        $pdf->SetFillColor(100, 100, 100);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($descWidth, 8, 'DESCRIPTION INTERVENTION', 1, 0, 'C', true);
        $pdf->Cell($photoWidth, 8, 'PHOTO(S)', 1, 1, 'C', true);

        $startY = $pdf->GetY();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);

        // DESCRIPTION
        $pdf->MultiCell($descWidth, 5, $workOrder->getInterventionDescription() ?? 'Aucune description', 1, 'L');

        $endY = $pdf->GetY();
        $cellHeight = max(40, $endY - $startY); // hauteur mini

        // PHOTO
        $photoPath = $workOrder->getDescriptionPhoto();
        $photoX = $margins['left'] + $descWidth;

        if ($photoPath && file_exists($this->getParameter('kernel.project_dir') . '/public' . $photoPath)) {
            try {
                $maxPhotoWidth = $photoWidth - 10;
                $maxPhotoHeight = $cellHeight - 10;

                $photoXPos = $photoX + ($photoWidth - $maxPhotoWidth) / 2;
                $photoYPos = $startY + ($cellHeight - $maxPhotoHeight) / 2;

                $pdf->Image(
                    $this->getParameter('kernel.project_dir') . '/public' . $photoPath,
                    $photoXPos,
                    $photoYPos,
                    $maxPhotoWidth,
                    $maxPhotoHeight,
                    '',
                    '',
                    '',
                    false,
                    300
                );

                // cadre
                $pdf->Rect($photoX, $startY, $photoWidth, $cellHeight);
            } catch (\Exception $e) {
                $pdf->SetXY($photoX, $startY);
                $pdf->SetFillColor(240, 240, 240);
                $pdf->MultiCell($photoWidth, $cellHeight, "Erreur\nchargement", 1, 'C', true);
            }
        } else {
            $pdf->SetXY($photoX, $startY);
            $pdf->SetFillColor(240, 240, 240);
            $pdf->MultiCell($photoWidth, $cellHeight, "Aucune photo", 1, 'C', true);
        }

        $pdf->SetY($startY + $cellHeight + 3);
        $pdf->Ln(3);
    }


    private function drawSparePartSection(TCPDF $pdf, WorkOrder $workOrder): void
    {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 32, 96); // Bleu foncé
        $pdf->Cell(0, 8, 'GESTION MAGASIN PIECES DE RECHANGE', 0, 1, 'C', true);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // Largeur dispo
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $availableWidth = $pageWidth - $margins['left'] - $margins['right'];

        $col1Width = $availableWidth * 0.55;
        $col2Width = $availableWidth * 0.225;
        $col3Width = $availableWidth * 0.225;

        // En-tête
        $pdf->SetFillColor(100, 100, 100);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 8);

        $pdf->Cell($col1Width, 8, 'SORTIE PIECES', 1, 0, 'C', true);
        $pdf->Cell($col2Width, 8, 'NON', 1, 0, 'C', true);
        $pdf->Cell($col3Width, 8, 'SI NON', 1, 1, 'C', true);

        // Contenu
        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 9);

        $pdf->Cell($col1Width, 8, 'BESOIN A REPORTER DANS FICHIER SPARE PART LIST :', 1, 0, 'L', true);
        $pdf->Cell($col2Width, 8, $workOrder->isPieceIssued() ? 'OUI' : 'NON', 1, 0, 'C', true);
        $pdf->Cell($col3Width, 8, $workOrder->getIfPieceNotIssued(), 1, 1, 'C', true);

        // Remarque
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
}
