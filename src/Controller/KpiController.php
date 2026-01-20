<?php

namespace App\Controller;

use App\Entity\WorkOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KpiController extends AbstractController
{
    // Configuration des heures de production par secteur (par mois)
    private const SECTOR_HOURS_PER_MONTH = [
        'BATIMENT' => 480,
        'MOYEN GENERAUX' => 480,
        'EXTERIEUR' => 480,
        'BLOCK 1' => 480,
        'BLOCK 2' => 480,
        'BLOCK 3' => 480,
        'BLOCK 4' => 480,
        'SSL1' => 480,
        'MACHINE TEST' => 480,
        'ASSEMBLAGE' => 480,
        'HPV1' => 480,
        'SOUFFLAGE' => 160,
    ];

    #[Route('/kpi', name: 'kpi_dashboard')]
    public function dashboard(Request $request, EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(WorkOrder::class);

        // ===== FILTRES =====
        $sector = $request->query->get('sector');
        $dateStart = $request->query->get('date_start');
        $dateEnd = $request->query->get('date_end');

        // Par défaut : 30 derniers jours
        if (!$dateStart) {
            $dateStart = (new \DateTime('-30 days'))->format('Y-m-d');
        }
        if (!$dateEnd) {
            $dateEnd = (new \DateTime())->format('Y-m-d');
        }

        // ===== RÉCUPÉRATION OT CLÔTURÉS (status = false) =====
        $qb = $repo->createQueryBuilder('w')
            ->where('w.status = :status')
            ->andWhere('w.intervention_request_date BETWEEN :start AND :end')
            ->setParameter('status', false)
            ->setParameter('start', new \DateTime($dateStart))
            ->setParameter('end', new \DateTime($dateEnd));

        if ($sector) {
            $qb->andWhere('w.sector = :sector')
                ->setParameter('sector', $sector);
        }

        $closedOrders = $qb->getQuery()->getResult();

        // ===== CALCUL KPI GLOBAUX =====
        $globalKpi = $this->calculateGlobalKPI($closedOrders, $dateStart, $dateEnd, $sector);

        // ===== CALCUL KPI PAR SECTEUR =====
        $sectorStats = $this->calculateSectorStats($closedOrders, $dateStart, $dateEnd);

        return $this->render('kpi/dashboard.html.twig', [
            // KPI globaux
            'mttr' => $globalKpi['mttr'],
            'mtbf' => $globalKpi['mtbf'],
            'preventiveRate' => $globalKpi['preventive_rate'],
            'preventiveCount' => $globalKpi['preventive_count'],
            'totalInterventions' => $globalKpi['total_interventions'],
            'downtimeRate' => $globalKpi['downtime_rate'],
            'totalDowntimeHours' => $globalKpi['total_downtime_hours'],
            'totalProductionHours' => $globalKpi['total_production_hours'],

            // Stats par secteur
            'sectorStats' => $sectorStats,

            // Filtres
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'selectedSector' => $sector,
        ]);
    }

    /**
     * Calcule les 4 KPI globaux
     */
    private function calculateGlobalKPI(array $workOrders, string $dateStart, string $dateEnd, ?string $sector): array
    {
        $totalInterventionTime = 0;  // En minutes
        $totalDowntimeTime = 0;      // En minutes
        $preventiveCount = 0;
        $totalCount = count($workOrders);

        // Parcourir tous les OT
        foreach ($workOrders as $wo) {
            $totalInterventionTime += $wo->getInterventionTime() ?? 0;
            $totalDowntimeTime += $wo->getDowntimeTime() ?? 0;

            if ($wo->getMaintenanceType() === 'Préventif') {
                $preventiveCount++;
            }
        }

        // ===== KPI 1 : MTTR (Mean Time To Repair) =====
        $mttrMinutes = $totalCount > 0 ? $totalInterventionTime / $totalCount : 0;
        $mttrFormatted = $this->minutesToHhMm($mttrMinutes);

        // ===== Calcul temps de production de la période (CORRIGÉ) =====
        $days = (new \DateTime($dateStart))->diff(new \DateTime($dateEnd))->days + 1;

        if ($sector && isset(self::SECTOR_HOURS_PER_MONTH[$sector])) {
            // Si secteur spécifique sélectionné
            $productionHours = (self::SECTOR_HOURS_PER_MONTH[$sector] / 30) * $days;
        } else {
            // ===== MOYENNE PONDÉRÉE PAR SECTEUR SELON LES OT =====
            $sectorCount = [];

            // Compter les OT par secteur
            foreach ($workOrders as $wo) {
                $woSector = $wo->getSector() ?? 'Inconnu';
                if (!isset($sectorCount[$woSector])) {
                    $sectorCount[$woSector] = 0;
                }
                $sectorCount[$woSector]++;
            }

            // Calculer la production pondérée
            $totalProductionHours = 0;
            foreach ($sectorCount as $sectorName => $count) {
                $hoursPerMonth = self::SECTOR_HOURS_PER_MONTH[$sectorName] ?? 480; // Par défaut 480h
                $sectorProductionHours = ($hoursPerMonth / 30) * $days;

                // Pondération : on multiplie par le nombre d'OT du secteur
                // puis on divise par le total d'OT pour la moyenne
                $totalProductionHours += ($sectorProductionHours * $count);
            }

            // Moyenne pondérée
            $productionHours = $totalCount > 0 ? $totalProductionHours / $totalCount : 0;
        }

        $downtimeHours = round($totalDowntimeTime / 60, 2);

        // ===== KPI 2 : MTBF (Mean Time Between Failures) =====
        $mtbfHours = $totalCount > 0 ? $productionHours / $totalCount : 0;
        $mtbfFormatted = round($mtbfHours, 1) . 'h';

        // ===== KPI 3 : % Préventif =====
        $preventiveRate = $totalCount > 0
            ? round(($preventiveCount / $totalCount) * 100, 1)
            : 0;

        // ===== KPI 4 : % Taux de Panne =====
        $downtimeRate = ($productionHours + $downtimeHours) > 0
            ? round(($downtimeHours / ($downtimeHours + $productionHours)) * 100, 2)
            : 0;

        return [
            'mttr' => $mttrFormatted,
            'mtbf' => $mtbfFormatted,
            'preventive_rate' => $preventiveRate,
            'preventive_count' => $preventiveCount,
            'total_interventions' => $totalCount,
            'downtime_rate' => $downtimeRate,
            'total_downtime_hours' => $downtimeHours,
            'total_production_hours' => round($productionHours, 2),
        ];
    }


    /**
     * Calcule les KPI par secteur
     */
    private function calculateSectorStats(array $workOrders, string $dateStart, string $dateEnd): array
    {
        $sectorData = [];

        // Grouper les OT par secteur
        foreach ($workOrders as $wo) {
            $sectorName = $wo->getSector() ?? 'Inconnu';

            if (!isset($sectorData[$sectorName])) {
                $sectorData[$sectorName] = [
                    'name' => $sectorName,
                    'interventions' => [],
                    'downtime_minutes' => 0,
                ];
            }

            $sectorData[$sectorName]['interventions'][] = $wo;
            $sectorData[$sectorName]['downtime_minutes'] += $wo->getDowntimeTime() ?? 0;
        }

        $stats = [];
        $days = (new \DateTime($dateStart))->diff(new \DateTime($dateEnd))->days + 1;

        foreach ($sectorData as $name => $data) {
            $interventions = $data['interventions'];
            $count = count($interventions);

            // MTTR du secteur
            $totalIntervention = array_sum(array_map(fn($wo) => $wo->getInterventionTime() ?? 0, $interventions));
            $mttr = $count > 0 ? $this->minutesToHhMm($totalIntervention / $count) : 'N/A';

            // % Préventif du secteur
            $preventiveCount = count(array_filter($interventions, fn($wo) => $wo->getMaintenanceType() === 'Préventif'));
            $preventiveRate = $count > 0 ? round(($preventiveCount / $count) * 100, 1) : 0;

            // Temps production du secteur
            $productionHours = isset(self::SECTOR_HOURS_PER_MONTH[$name])
                ? (self::SECTOR_HOURS_PER_MONTH[$name] / 30) * $days
                : (480 / 30) * $days; // Défaut 480h/mois

            // MTBF du secteur
            $mtbf = $count > 0 ? round($productionHours / $count, 1) . 'h' : 'N/A';

            // Taux de panne du secteur
            $downtimeHours = $data['downtime_minutes'] / 60;
            $downtimeRate = ($productionHours + $downtimeHours) > 0
                ? round(($downtimeHours / ($downtimeHours + $productionHours)) * 100, 2)
                : 0;

            $stats[] = [
                'name' => $name,
                'total_interventions' => $count,
                'mttr' => $mttr,
                'mtbf' => $mtbf,
                'preventive_rate' => $preventiveRate,
                'downtime_rate' => $downtimeRate,
            ];
        }

        // Trier par nom de secteur
        usort($stats, fn($a, $b) => strcmp($a['name'], $b['name']));

        return $stats;
    }

    /**
     * Convertit des minutes en format HHhMM
     */
    private function minutesToHhMm(?float $minutes): string
    {
        if ($minutes === null || $minutes === 0) {
            return '0h00';
        }
        $h = intdiv((int)$minutes, 60);
        $m = (int)$minutes % 60;
        return sprintf('%dh%02d', $h, $m);
    }
}
