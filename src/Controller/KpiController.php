<?php

namespace App\Controller;

use App\Entity\WorkOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KpiController extends AbstractController
{
    #[Route('/kpi', name: 'kpi_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(WorkOrder::class);

        // 1) Total OT créés
        $totalOrders = (int) $repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // 2) OT ouverts (status = true)
        $openOrders = (int) $repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->where('w.status = :status')
            ->setParameter('status', true)
            ->getQuery()
            ->getSingleScalarResult();

        // 3) OT fermés (status = false)
        $closedOrders = (int) $repo->createQueryBuilder('w')
            ->select('COUNT(w.id)')
            ->where('w.status = :status')
            ->setParameter('status', false)
            ->getQuery()
            ->getSingleScalarResult();

        // 4) Temps d'intervention moyen (en minutes)
        $avgIntervention = $repo->createQueryBuilder('w')
            ->select('AVG(w.intervention_time)')
            ->where('w.intervention_time IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
        $avgIntervention = $avgIntervention !== null ? (float) $avgIntervention : null;

        // 5) Temps de panne moyen (en minutes)
        $avgDowntime = $repo->createQueryBuilder('w')
            ->select('AVG(w.downtime_time)')
            ->where('w.downtime_time IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
        $avgDowntime = $avgDowntime !== null ? (float) $avgDowntime : null;

        $avgInterventionFormatted = $this->formatMinutesToHHMM($avgIntervention);
        $avgDowntimeFormatted = $this->formatMinutesToHHMM($avgDowntime);

        // 6) Statistiques mensuelles (12 derniers mois) via DBAL SQL natif (MySQL)
        $conn = $em->getConnection();
        // Activer la locale française pour les noms de mois
        $conn->executeStatement("SET lc_time_names = 'fr_FR';");

// Requête principale avec mois en lettres
        $sql = "
            SELECT
                DATE_FORMAT(intervention_request_date, '%Y-%m') AS month_key,
                DATE_FORMAT(intervention_request_date, '%M %Y') AS month_label,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS open_count,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS closed_count
            FROM work_order
            WHERE intervention_request_date IS NOT NULL
              AND intervention_request_date >= :startDate
            GROUP BY month_key, month_label
            ORDER BY month_key ASC
        ";

        // Paramètres
        $params = [
            'startDate' => (new \DateTime('-12 months'))->format('Y-m-d H:i:s'),
        ];

        // Exécution
        $monthlyStats = $conn->executeQuery($sql, $params)->fetchAllAssociative();


        return $this->render('kpi/dashboard.html.twig', [
            'totalOrders' => $totalOrders,
            'openOrders' => $openOrders,
            'closedOrders' => $closedOrders,
            'avgIntervention' => $avgInterventionFormatted,
            'avgDowntime' => $avgDowntimeFormatted,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function formatMinutesToHHMM(?float $minutes): string
    {
        if ($minutes === null) {
            return 'N/A';
        }
        $hours = (int) floor($minutes / 60);
        $mins = (int) round($minutes % 60);
        return sprintf('%02d:%02d', $hours, $mins);
    }
}
