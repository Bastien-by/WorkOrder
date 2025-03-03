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
            dump($workOrder);
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

    private function generatePDF(WorkOrder $workOrder)
    {

        $machineName = $workOrder->getMachineName();
        $interventionDate = $workOrder->getInterventionDate();

        $id_ot = $workOrder->getId();
        $interventionDate = $workOrder->getInterventionDate()->format('d_m_Y'); // Exemple : 2025-02-28
        $interventionRequestDate = $workOrder->getInterventionRequestDate()->format('d_m_Y');
        $downtime_start_time = $workOrder->getDowntimeStartTime()->format('H:i');
        $downtime_end_time = $workOrder->getDowntimeEndTime()->format('H:i');
        $intervention_start_time = $workOrder->getInterventionStartTime()->format('H:i');
        $intervention_end_time = $workOrder->getInterventionEndTime()->format('H:i');

        // Définir le chemin complet du fichier avec la date
        $filePath = "/var/www/WorkOrder/pdfot/ot-$id_ot-$interventionDate-$machineName.pdf";
        $pdf = new TCPDF();

        // Ajouter une page
        $pdf->AddPage();
        $pdf->SetTextColor(14, 52, 113);
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/images/OPMobility.jpg', 0, 14.55, 37.5, 15, 'JPG');
        $pdf->Ln(9); // Saut de ligne
        // Ajouter un titre
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(200, 10, 'Ordre de Maintenance tracking 2025', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 15);
        // Tester les positions sans remplir les champs du formulaire
        $pdf->Ln(12); // Saut de ligne
        $pdf->Cell(95, 10, 'Nom(s) Intervenants:', 0, 0, 'C');
        $pdf->Cell(95, 10, 'Nom Machine/Secteur:', 0, 1, 'C');
        $pdf->Cell(95, 10, '' . $workOrder->getTechnicianName(), 0, 0, 'C');
        $pdf->Cell(95, 10, '' . $workOrder->getMachineName(), 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, 'Type Maintenance:', 0, 0, 'C');
        $pdf->Cell(95, 10, 'Poste Technique:', 0, 1, 'C');
        $pdf->Cell(95, 10, '' . $workOrder->getMaintenanceType() , 0, 0, 'C');
        $pdf->Cell(95, 10, '' . $workOrder->getTechnicalPosition(), 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(0, 10, "Date d'Intervention:", 0, 1, 'C');
        $pdf->Cell(0, 10, "" . $interventionDate, 0, 1, 'C');

        $pdf->Ln(12); // Saut de ligne
        // Ajouter un titre
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, 'Durée Panne/Intervention', 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 15);
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, 'Heure Début Panne: ' . $downtime_start_time, 0, 0, 'C');
        $pdf->Cell(95, 10, 'Heure Début Intervention: ' . $intervention_start_time, 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, 'Heure Fin Panne: ' . $downtime_end_time, 0, 0, 'C');
        $pdf->Cell(95, 10, 'Heure Fin Intervention: ' . $intervention_end_time, 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, 'Durée Panne: ', 0, 0, 'C');
        $pdf->Cell(95, 10, 'Durée Intervention: ', 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(0, 10, "Domaine d'Intervention:", 0, 1, 'C');
        $pdf->Cell(0, 10, "" . $workOrder->getFieldIntervention(), 0, 1, 'C');

        $pdf->Ln(12); // Saut de ligne
        // Ajouter un titre
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, "Demande Intervention", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 15);
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, "Demandeur Intervention: ". $workOrder->getInterventionRequester(), 0, 0, 'C');
        $pdf->Cell(95, 10, "Date Demande Intervention: " . $interventionRequestDate, 0, 1, 'C');

        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(0, 10, "Description Technique Identifiée", 0, 1, 'C');
        $pdf->MultiCell(0, 10, '' . $workOrder->getTechnicalDetails(), 1, 'C');

        $pdf->Ln(20); // Saut de ligne
        // Ajouter un titre
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, "Descriptif Intervention Technique", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 15);
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(0, 10, "Description Technique Identifiée", 0, 1, 'C');
        $pdf->MultiCell(0, 10, 'Test en manuel, Petite vitesse , OK ,pas de bruit, Vitesse de production, bruit audible, Reglage Tension courroie et test ,Bruit disparu, Changement de courroie a prévoir , pas de stock', 1, 'C');

        $pdf->Ln(12); // Saut de ligne
        // Ajouter un titre
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, "Gestion Magasin", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 15);
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, "Sortie Pièces: " . $workOrder->isPieceIssued(), 0, 0, 'C');
        $pdf->Cell(95, 10, "Si NON: Pièces non crées", 0, 1, 'C');
        $pdf->Cell(95, 10, "Type de Pièces: " . $workOrder->getPieceType(), 0, 0, 'C');
        $pdf->Cell(95, 10, "Marque Fabricant: " . $workOrder->getPieceBrand(), 0, 1, 'C');
        $pdf->Cell(95, 10, "Référence SAP: " . $workOrder->getSapReference(), 0, 0, 'C');
        $pdf->Cell(95, 10, "Quantité: " . $workOrder->getQuantity(), 0, 1, 'C');

        $pdf->Ln(12); // Saut de ligne
        // Ajouter un titre
        $pdf->SetFont('helvetica', '', 20);
        $pdf->Cell(0, 10, "Détails pièces à créer", 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 15);
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, "Marque : " . $workOrder->getBrand(), 0, 0, 'C');
        $pdf->Cell(95, 10, "Type : " . $workOrder->getType(), 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(95, 10, "Ref Fabricant : " . $workOrder->getManufacturerReference(), 0, 0, 'C');
        $pdf->Cell(95, 10, "Dimension : " . $workOrder->getSize(), 0, 1, 'C');
        $pdf->Ln(5); // Saut de ligne
        $pdf->Cell(0, 10, "Quantité : " . $workOrder->getCreatedPieceQuantity(), 0, 1, 'C');

        $pdf->Cell(0, 10, "Descriptif supplémentaire :", 0, 1, 'C');
        $pdf->MultiCell(0, 10, '' . $workOrder->getAdditionalDetails(), 1, 'C');

        // Générer le PDF et l'afficher dans le navigateur
        //$pdfContent = $pdf->Output('', 'S'); // Générer le PDF sans le sauvegarder

        
        // Enregistrer le fichier
        $pdf->Output($filePath, 'F'); // F pour enregistrer sur le serveur

        // Ou afficher le PDF directement dans le navigateur
        //$pdf->Output('ordre_de_travail.pdf', 'I'); // I pour afficher dans le navigateur
    }
}
