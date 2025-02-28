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
        // Créer un objet TCPDF
        $pdf = new TCPDF();

        // Ajouter une page
        $pdf->AddPage();

        // Définir la police (par exemple, Helvetica, taille 12)
        $pdf->SetFont('helvetica', '', 12);

        // Ajouter un titre
        $pdf->Cell(0, 10, 'Ordre de Travail', 0, 1, 'C');

        // Ajouter des informations à partir de l'objet WorkOrder
        $pdf->Ln(5); // Saut de ligne

        // Exemple d'ajout de données du formulaire
        $pdf->Cell(0, 10, 'Technician Name: ' . $workOrder->getTechnicianName(), 0, 1);
        $pdf->Cell(0, 10, 'Machine Name: ' . $workOrder->getMachineName(), 0, 1);
        $pdf->Cell(0, 10, 'Maintenance Type: ' . $workOrder->getMaintenanceType(), 0, 1);
        $pdf->Cell(0, 10, 'Technical Position: ' . $workOrder->getTechnicalPosition(), 0, 1);
        $pdf->Cell(0, 10, 'Intervention Date: ' . $workOrder->getInterventionDate()->format('Y-m-d'), 0, 1);

        // Ajouter d'autres champs si nécessaire
        // $pdf->Cell(0, 10, 'Another Field: ' . $workOrder->getSomeField(), 0, 1);

        // Générer le PDF (enregistrer dans le fichier ou l'afficher)
        // Enregistrer le fichier
        $pdf->Output('/var/www/WorkOrder/pdfot/ordre_de_travail.pdf', 'F'); // F pour enregistrer sur le serveur

        // Ou afficher le PDF directement dans le navigateur
        //$pdf->Output('ordre_de_travail.pdf', 'I'); // I pour afficher dans le navigateur
    }
}
