<?php

namespace App\Controller;

use TCPDF;
use App\Entity\WorkOrder;
use App\Form\WorkOrderType;
use Doctrine\Inflector\Rules\Word;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WorkOrderController extends AbstractController
{
    #[Route('/generateur', name: 'work_order_generator')]
    public function generator(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création des objets
        $workOrder = new WorkOrder();

        // Création des formulaires
        $workOrderForm = $this->createForm(WorkOrderType::class, $workOrder);
        
        //Gestion des requêtes
        $workOrderForm->handleRequest($request);


        if ($workOrderForm->isSubmitted() && $workOrderForm->isValid()) {
            // Enregistrer le participant dans la base de données
            $entityManager->persist($workOrder);

            $entityManager->flush();

            $this->generatePDF($workOrder);

            $this->addFlash('success', 'Ordre de Travail enregistré avec succés !');
            return $this->redirectToRoute('work_order_generator');
        }

        return $this->render('work_order.html.twig', [
            'WorkOrderForm' => $workOrderForm->createView(),          
        ]);
    }

    private function drawSection(TCPDF $pdf, string $title, callable $contentCallback): void
    {
        $pdf->Ln(8);
        $pdf->SetFillColor(200, 220, 255); // bleu clair
        $pdf->SetTextColor(14, 52, 113);   // texte bleu foncé
        $pdf->SetFont('helvetica', 'B', 14);
        
        $pdf->SetX(15); // Alignement à gauche du fond
        $pdf->Cell(180, 10, $title, 0, 1, 'C', 1);

        $startY = $pdf->GetY();

        $pdf->Ln(3);
        $pdf->SetFont('helvetica', '', 12);

        $contentCallback();

        $endY = $pdf->GetY();
        $boxHeight = $endY - $startY;

        $pdf->Rect(15, $startY, 180, $boxHeight);
    }


    private function generatePDF(WorkOrder $workOrder)
    {
        $machineName = $workOrder->getMachineName();
        $interventionDate = $workOrder->getInterventionDate()->format('d_m_Y');
        $interventionDatePDF = $workOrder->getInterventionDate()->format('d/m/Y');
        $interventionRequestDate = $workOrder->getInterventionRequestDate()->format('d/m/Y');
        $id_ot = $workOrder->getId();

        $downtime_start = $workOrder->getDowntimeStartTime();
        $downtime_end = $workOrder->getDowntimeEndTime();
        $intervention_start = $workOrder->getInterventionStartTime();
        $intervention_end = $workOrder->getInterventionEndTime();

        $downtime_str = $downtime_start->diff($downtime_end)->format('%h h %i min');
        $intervention_str = $intervention_start->diff($intervention_end)->format('%h h %i min');

        $downtime_start_time = $downtime_start->format('H:i');
        $downtime_end_time = $downtime_end->format('H:i');
        $intervention_start_time = $intervention_start->format('H:i');
        $intervention_end_time = $intervention_end->format('H:i');

        $filePath = "/var/www/WorkOrder/pdfot/$id_ot-ot-$interventionDate-$machineName.pdf";

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetTextColor(14, 52, 113);
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/images/OPMobility.jpg', 0, 14.55, 37.5, 15, 'JPG');
        $pdf->Ln(9);

        // ===== HEADER PRINCIPAL =====
        $this->drawSection($pdf, "Ordre de Maintenance tracking 2025", function() use ($pdf, $workOrder, $interventionDate) {
            $pdf->SetFont('helvetica', '', 13);
            $pdf->Cell(95, 10, 'Nom(s) Intervenants:', 0, 0, 'C');
            $pdf->Cell(95, 10, 'Nom Machine/Secteur:', 0, 1, 'C');
            $pdf->Cell(95, 10, $workOrder->getTechnicianName(), 0, 0, 'C');
            $pdf->Cell(95, 10, $workOrder->getMachineName(), 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->Cell(95, 10, 'Type Maintenance:', 0, 0, 'C');
            $pdf->Cell(95, 10, 'Poste Technique:', 0, 1, 'C');
            $pdf->Cell(95, 10, $workOrder->getMaintenanceType(), 0, 0, 'C');
            $pdf->Cell(95, 10, $workOrder->getTechnicalPosition(), 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->Cell(0, 10, "Date d'Intervention: $interventionDate", 0, 1, 'C');
        });

        // ===== Durée Panne/Intervention =====
        $this->drawSection($pdf, "Durée Panne/Intervention", function() use ($pdf, $downtime_start_time, $downtime_end_time, $downtime_str, $intervention_start_time, $intervention_end_time, $intervention_str, $workOrder) {
            $pdf->Cell(90, 10, 'Heure Début Panne: ' . $downtime_start_time, 0, 0, 'C');
            $pdf->Cell(90, 10, 'Heure Début Intervention: ' . $intervention_start_time, 0, 1, 'C');
            $pdf->Cell(90, 10, 'Heure Fin Panne: ' . $downtime_end_time, 0, 0, 'C');
            $pdf->Cell(90, 10, 'Heure Fin Intervention: ' . $intervention_end_time, 0, 1, 'C');
            $pdf->Cell(90, 10, 'Durée Panne: ' . $downtime_str, 0, 0, 'C');
            $pdf->Cell(90, 10, 'Durée Intervention: ' . $intervention_str, 0, 1, 'C');
            $pdf->Cell(0, 10, "Domaine d'Intervention: " . $workOrder->getFieldIntervention(), 0, 1, 'C');
        });

        // ===== Demande Intervention =====
        $this->drawSection($pdf, "Demande Intervention", function() use ($pdf, $workOrder, $interventionRequestDate) {
            $pdf->Cell(95, 10, "Demandeur Intervention: ". $workOrder->getInterventionRequester(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Date Demande Intervention: " . $interventionRequestDate, 0, 1, 'C');
        });

        // ===== Description Technique Identifiée =====
        $this->drawSection($pdf, "Description Technique Identifiée", function() use ($pdf, $workOrder) {
            $pdf->MultiCell(0, 10, $workOrder->getTechnicalDetails(), 0, 'C');
        });

        // ===== Gestion Magasin =====
        $this->drawSection($pdf, "Gestion Magasin", function() use ($pdf, $workOrder) {
            $pdf->Cell(95, 10, "Sortie Pièces: " . $workOrder->isPieceIssued(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Si NON: Pièces non crées", 0, 1, 'C');
            $pdf->Cell(95, 10, "Type de Pièces: " . $workOrder->getPieceType(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Marque Fabricant: " . $workOrder->getPieceBrand(), 0, 1, 'C');
            $pdf->Cell(95, 10, "Référence SAP: " . $workOrder->getSapReference(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Quantité: " . $workOrder->getQuantity(), 0, 1, 'C');
            $pdf->Ln(10);
        });

        // ===== Détails pièces à créer =====
        $this->drawSection($pdf, "Détails pièces à créer", function() use ($pdf, $workOrder) {
            $pdf->Cell(95, 10, "Marque : " . $workOrder->getBrand(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Type : " . $workOrder->getType(), 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->Cell(95, 10, "Ref Fabricant : " . $workOrder->getManufacturerReference(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Quantité : " . $workOrder->getCreatedPieceQuantity(), 0, 1, 'C');
            $pdf->Ln(5);
            $pdf->Cell(0, 10, "Dimension : " . $workOrder->getSize(), 0, 1, 'C');
            $pdf->Cell(0, 10, "Descriptif supplémentaire :", 0, 1, 'C');
            $pdf->MultiCell(0, 10, $workOrder->getAdditionalDetails(), 0, 'C');
        });

        // Enregistrement du fichier
        $pdf->Output($filePath, 'F');
    }

}
