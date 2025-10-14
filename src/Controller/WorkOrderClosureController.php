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

        // Génération des noms de fichiers PDF attendus (même logique que dans PdfController)
        $getFileName = function (WorkOrder $order) use ($pdfDirectory) {
            $id = $order->getId();
            $machine = $order->getMachineName();
            $date = $order->getInterventionRequestDate()?->format('d-m-Y');
            $filename = "{$id}-{$machine}-{$date}-ot.pdf";

            return file_exists("$pdfDirectory/$filename") ? $filename : null;
        };

        // Liste des fichiers PDF actifs
        $activePdfs = array_filter(array_map($getFileName, $orders));

        return $this->render('work_order_edit.html.twig', [
            'activePdfs' => $activePdfs,
            'WorkOrderClosureForm' => null,
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
            // 🔽 Gestion de l'upload d'image du plan électrique
            $uploadedFile = $form->get('elecPlanPicture')->getData();

            // Vérifie si les dates sont bien récupérées depuis le formulaire
            dump($workOrder->getDowntimeStartTime());
            dump($workOrder->getDowntimeEndTime());



            if ($uploadedFile) {
                $filename = uniqid().'.'.$uploadedFile->guessExtension();
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads/elec_plans';
                $uploadedFile->move($destination, $filename);

                // 🔹 Stocke le chemin dans la bonne propriété (elecPlanPicture)
                $workOrder->setElecPlanPicture('/uploads/elec_plans/'.$filename);
            }

            // 🔹 Marque comme clôturé
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

            // 🔹 Génère ton PDF mais sans l'image (puisque tu ne veux pas l'afficher)
            $this->generateClosurePDF($workOrder);

            $this->addFlash('success', "L'ordre de travail #{$id} a été modifié et clôturé.");
            return $this->redirectToRoute('list_pdfs');
        }

        return $this->render('work_order_edit.html.twig', [
            'WorkOrderClosureForm' => $form->createView(),
            'workOrder' => $workOrder,
            'orders' => null,
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
        $interventionRequestDate = $workOrder->getInterventionRequestDate()?->format('d/m/Y');
        $interventionRequestDateFile = $workOrder->getInterventionRequestDate()?->format('d-m-Y');
        $id_ot = $workOrder->getId();

        $filePath = "/var/www/WorkOrder/pdfot/$id_ot-$machineName-$interventionRequestDateFile-ot.pdf";

        $pdf = new TCPDF();
        $pdf->AddPage();
        $pdf->SetTextColor(14, 52, 113);
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/images/OPMobility.jpg', 0, 14.55, 37.5, 15, 'JPG');
        $pdf->Ln(9);

        // SECTION 1 : Demande Intervention
        $this->drawSection($pdf, "Demande Intervention", function() use ($pdf, $workOrder, $interventionRequestDate) {
            $pdf->Cell(95, 10, "Demande: ". $workOrder->getInterventionRequester(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Date: " . $interventionRequestDate, 0, 1, 'C');
            $pdf->Cell(0, 10, "Machine: ". $workOrder->getMachineName(), 0, 0, 'C');
            $pdf->Ln(15);
            $pdf->MultiCell(0, 10, $workOrder->getTechnicalDetails(), 0, 'C');
        });

        // SECTION 2 : Informations sur la panne
        $this->drawSection($pdf, "Informations sur la panne", function() use ($pdf, $workOrder) {
            $pdf->Cell(95, 10, "Début panne: " . ($workOrder->getDowntimeStartTime()?->format('d/m/Y H:i') ?? 'N/A'), 0, 0, 'C');
            $pdf->Cell(95, 10, "Début intervention: " . ($workOrder->getInterventionStartTime()?->format('d/m/Y H:i') ?? 'N/A'), 0, 1, 'C');
            $pdf->Cell(95, 10, "Fin panne: " . ($workOrder->getDowntimeEndTime()?->format('d/m/Y H:i') ?? 'N/A'), 0, 0, 'C');
            $pdf->Cell(95, 10, "Fin intervention: " . ($workOrder->getInterventionEndTime()?->format('d/m/Y H:i') ?? 'N/A'), 0, 1, 'C');
            $intMinutes = $workOrder->getInterventionTime(); // entier en minutes
            $dowMinutes = $workOrder->getDowntimeTime();     // entier en minutes

            $pdf->Cell(95, 10, "Durée panne (h) : " . $this->minutesToHhMm($dowMinutes), 0, 0, 'C');
            $pdf->Cell(95, 10, "Durée intervention (h) : " . $this->minutesToHhMm($intMinutes), 0, 1, 'C');


        });


        // SECTION 3 : Informations techniques
        $this->drawSection($pdf, "Technique & maintenance", function() use ($pdf, $workOrder) {
            $pdf->Cell(95, 10, "Technicien(s): " . $workOrder->getTechnicianName(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Poste technique: " . $workOrder->getTechnicalPosition(), 0, 1, 'C');
            $pdf->Cell(95, 10, "Domaine: " . $workOrder->getFieldIntervention(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Type de maintenance: " . $workOrder->getMaintenanceType(), 0, 1, 'C');
        });

        // SECTION 4 : Commentaire
        $this->drawSection($pdf, "Commentaires / Observations", function() use ($pdf, $workOrder) {
            $pdf->MultiCell(0, 10, $workOrder->getAdditionalDetails(), 0, 'C');
        });

        $this->drawSection($pdf, "Plan Électrique", function() use ($pdf, $workOrder) {
            $pdf->Cell(0, 10, "Est-ce qu'il y a un plan éléc ? : " . ($workOrder->isElecPlan() ? 'Oui' : 'Non'), 0, 1, 'C');
            $pdf->Cell(0, 10, "Est-ce qu'il y a des modifications au plan élec ? : " . ($workOrder->isChangedElecPlan() ? 'Oui' : 'Non'), 0, 1, 'C');

            if ($workOrder->getElecPlanPicture()) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . $workOrder->getElecPlan();
                if (file_exists($imagePath)) {
                    $imgWidth = 50;
                    $imgHeight = 0;
                    $pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];
                    $x = ($pageWidth - $imgWidth) / 2 + $pdf->getMargins()['left'];
                    $y = $pdf->GetY() + 5;

                    $pdf->Image($imagePath, $x, $y, $imgWidth, $imgHeight);
                    $pdf->Ln($imgHeight + 5); // laisse un peu d'espace après l'image
                } else {
                    $pdf->Cell(0, 10, "⚠️ Image non trouvée", 0, 1, 'C');
                }
            }
        });

        $pdf->Ln(30);
        $this->drawSection($pdf, "Pièce", function() use ($pdf, $workOrder) {
            $pdf->Cell(0, 10, "Est-ce qu'il y a une pièce ? : " . ($workOrder->isPiece() ? 'Oui' : 'Non'), 0, 1, 'C');
            $pdf->Cell(0, 10, "Est-ce qu'il y a besoin d'une pièce ? : " . ($workOrder->isPieceNeeded() ? 'Oui' : 'Non'), 0, 1, 'C');
        });

        $pdf->Output($filePath, 'F');


        $pdf->Output($filePath, 'F');
    }
}
