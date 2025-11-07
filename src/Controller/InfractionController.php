<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Infraction;
use App\Entity\Team;
use App\Repository\InfractionRepository;
use App\Service\InfractionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/infractions')]
class InfractionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private InfractionManager $infractionManager,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'api_infractions_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            // var_dump($data); // debug

            if (!$data) {
                return $this->json([
                    'status' => 400,
                    'code' => 'INVALID_JSON',
                    'message' => 'JSON invalide'
                ], Response::HTTP_BAD_REQUEST);
            }

            
            $requiredFields = ['type', 'amount', 'raceName', 'description', 'occurredAt'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return $this->json([
                        'status' => 400,
                        'code' => 'MISSING_FIELD',
                        'message' => "Le champ '$field' est requis"
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            
            $hasDriver = isset($data['driverId']);
            $hasTeam = isset($data['teamId']);

            if (!$hasDriver && !$hasTeam) {
                return $this->json([
                    'status' => 400,
                    'code' => 'TARGET_REQUIRED',
                    'message' => 'Vous devez spécifier soit driverId soit teamId'
                ], Response::HTTP_BAD_REQUEST);
            }

            if ($hasDriver && $hasTeam) {
                return $this->json([
                    'status' => 400,
                    'code' => 'MULTIPLE_TARGETS',
                    'message' => 'Vous ne pouvez pas cibler à la fois un pilote et une écurie'
                ], Response::HTTP_BAD_REQUEST);
            }

            // TODO: Valider plus strictement le format de date
            try {
                $occurredAt = new \DateTime($data['occurredAt']);
            } catch (\Exception $e) {
                return $this->json([
                    'status' => 422,
                    'code' => 'INVALID_DATE',
                    'message' => 'Format de date invalide. Utilisez ISO8601 (ex: 2025-03-08T14:00:00Z)'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $infraction = null;

            
            if ($hasDriver && $data['type'] === Infraction::TYPE_PENALTY_POINTS) {
                $driver = $this->entityManager->getRepository(Driver::class)->find($data['driverId']);

                if (!$driver) {
                    return $this->json([
                        'status' => 404,
                        'code' => 'DRIVER_NOT_FOUND',
                        'message' => 'Pilote introuvable'
                    ], Response::HTTP_NOT_FOUND);
                }

                $infraction = $this->infractionManager->createPenaltyForDriver(
                    $driver,
                    (int) $data['amount'],
                    $data['raceName'],
                    $data['description'],
                    $occurredAt
                );

                
                $this->entityManager->refresh($driver);

                return $this->json([
                    'status' => 201,
                    'message' => 'Pénalité créée avec succès',
                    'infraction' => [
                        'id' => $infraction->getId(),
                        'type' => $infraction->getType(),
                        'amount' => $infraction->getAmount(),
                        'raceName' => $infraction->getRaceName(),
                        'occurredAt' => $infraction->getOccurredAt()->format('c'),
                    ],
                    'driver' => [
                        'id' => $driver->getId(),
                        'name' => $driver->getFullName(),
                        'licensePoints' => $driver->getLicensePoints(),
                        'status' => $driver->getStatus()
                    ]
                ], Response::HTTP_CREATED);
            }

            
            if ($hasTeam && $data['type'] === Infraction::TYPE_FINE_EUR) {
                $team = $this->entityManager->getRepository(Team::class)->find($data['teamId']);

                if (!$team) {
                    return $this->json([
                        'status' => 404,
                        'code' => 'TEAM_NOT_FOUND',
                        'message' => 'Écurie introuvable'
                    ], Response::HTTP_NOT_FOUND);
                }

                $infraction = $this->infractionManager->createFineForTeam(
                    $team,
                    (float) $data['amount'],
                    $data['raceName'],
                    $data['description'],
                    $occurredAt
                );

                return $this->json([
                    'status' => 201,
                    'message' => 'Amende créée avec succès',
                    'infraction' => [
                        'id' => $infraction->getId(),
                        'type' => $infraction->getType(),
                        'amount' => $infraction->getAmount(),
                        'raceName' => $infraction->getRaceName(),
                        'occurredAt' => $infraction->getOccurredAt()->format('c'),
                    ],
                    'team' => [
                        'id' => $team->getId(),
                        'name' => $team->getName()
                    ]
                ], Response::HTTP_CREATED);
            }

            
            return $this->json([
                'status' => 400,
                'code' => 'TYPE_TARGET_MISMATCH',
                'message' => 'Type d\'infraction incompatible avec la cible'
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'code' => 'INTERNAL_ERROR',
                'message' => 'Une erreur est survenue'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'api_infractions_list', methods: ['GET'])]
    public function list(Request $request, InfractionRepository $repository): JsonResponse
    {
        try {
            
            $teamId = $request->query->get('teamId');
            $driverId = $request->query->get('driverId');
            $from = $request->query->get('from');
            $to = $request->query->get('to');

            
            $fromDate = $from ? new \DateTime($from) : null;
            $toDate = $to ? new \DateTime($to) : null;

            
            $infractions = $repository->search(
                $teamId ? (int) $teamId : null,
                $driverId ? (int) $driverId : null,
                $fromDate,
                $toDate
            );

            $result = array_map(function (Infraction $infraction) {
                $data = [
                    'id' => $infraction->getId(),
                    'type' => $infraction->getType(),
                    'amount' => $infraction->getAmount(),
                    'raceName' => $infraction->getRaceName(),
                    'description' => $infraction->getDescription(),
                    'occurredAt' => $infraction->getOccurredAt()->format('c'),
                ];

                if ($infraction->getDriver()) {
                    $data['driver'] = [
                        'id' => $infraction->getDriver()->getId(),
                        'name' => $infraction->getDriver()->getFullName()
                    ];
                }

                if ($infraction->getTeam()) {
                    $data['team'] = [
                        'id' => $infraction->getTeam()->getId(),
                        'name' => $infraction->getTeam()->getName()
                    ];
                }

                return $data;
            }, $infractions);

            return $this->json([
                'status' => 200,
                'count' => count($result),
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'code' => 'INTERNAL_ERROR',
                'message' => 'Une erreur est survenue'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
