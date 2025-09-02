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

        $WorkOrderRequestForm = $this->createForm(WorkOrderRequestType::class, $workOrder);
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

    private function drawSection(TCPDF $pdf, string $title, callable $contentCallback): void
    {
        $pdf->Ln(8);
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

    private function generatePDF(WorkOrder $workOrder)
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

        $this->drawSection($pdf, "Demande Intervention", function() use ($pdf, $workOrder, $interventionRequestDate) {
            $pdf->Cell(95, 10, "Demande: ". $workOrder->getInterventionRequester(), 0, 0, 'C');
            $pdf->Cell(95, 10, "Date: " . $interventionRequestDate, 0, 1, 'C');
            $pdf->Cell(0, 10, "Machine: ". $workOrder->getMachineName(), 0, 0, 'C');

            $pdf->Ln(5);

            $pdf->MultiCell(0, 10, $workOrder->getTechnicalDetails(), 0, 'C');
        });


        $pdf->Output($filePath, 'F');
    }
}
