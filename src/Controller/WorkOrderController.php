<?php

namespace App\Controller;


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
        $workOrderForm = $this->createForm(WorkOrder::class, $workOrder);
        
        //Gestion des requêtes
        $workOrderForm->handleRequest($request);


        if ($workOrderForm->isSubmitted() && $workOrderForm->isValid()) {
            // Enregistrer le participant dans la base de données
            $entityManager->persist($workOrder);

            $entityManager->flush();

            $this->addFlash('success', 'Ordre de Travail enregistré avec succés !');
            return $this->redirectToRoute('work_order_generator');
        }

        return $this->render('work_order.html.twig', [
            'participantForm' => $workOrderForm->createView(),          
        ]);
    }
}
