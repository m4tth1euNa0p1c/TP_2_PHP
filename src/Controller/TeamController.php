<?php

namespace App\Controller;

use App\Entity\Driver;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/team')]
#[IsGranted('ROLE_USER')]
class TeamController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/{id}/drivers', name: 'api_team_drivers_list', methods: ['GET'])]
    public function listDrivers(int $id, Request $request): JsonResponse
    {
        $team = $this->entityManager->getRepository(Team::class)->find($id);

        if (!$team) {
            return $this->json([
                'status' => 404,
                'code' => 'TEAM_NOT_FOUND',
                'message' => 'Écurie introuvable'
            ], Response::HTTP_NOT_FOUND);
        }

        $isStarter = $request->query->get('isStarter');
        $drivers = $team->getDrivers();

        
        if ($isStarter !== null) {
            $isStarterBool = filter_var($isStarter, FILTER_VALIDATE_BOOLEAN);
            $drivers = $drivers->filter(fn(Driver $d) => $d->isStarter() === $isStarterBool);
        }

        $result = array_map(function (Driver $driver) {
            return [
                'id' => $driver->getId(),
                'firstName' => $driver->getFirstName(),
                'lastName' => $driver->getLastName(),
                'isStarter' => $driver->isStarter(),
                'licensePoints' => $driver->getLicensePoints(),
                'status' => $driver->getStatus(),
                'f1StartDate' => $driver->getF1StartDate()->format('Y-m-d')
            ];
        }, $drivers->toArray());

        return $this->json([
            'status' => 200,
            'team' => [
                'id' => $team->getId(),
                'name' => $team->getName()
            ],
            'drivers' => array_values($result)
        ]);
    }

    #[Route('/{id}/drivers', name: 'api_team_drivers_update', methods: ['PATCH'])]
    #[IsGranted('ROLE_MANAGER')]
    public function updateDrivers(int $id, Request $request): JsonResponse
    {
        try {
            $team = $this->entityManager->getRepository(Team::class)->find($id);

            if (!$team) {
                return $this->json([
                    'status' => 404,
                    'code' => 'TEAM_NOT_FOUND',
                    'message' => 'Écurie introuvable'
                ], Response::HTTP_NOT_FOUND);
            }

            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return $this->json([
                    'status' => 400,
                    'code' => 'INVALID_JSON',
                    'message' => 'JSON invalide'
                ], Response::HTTP_BAD_REQUEST);
            }

            $add = $data['add'] ?? [];
            $remove = $data['remove'] ?? [];

            
            foreach ($add as $driverId) {
                $driver = $this->entityManager->getRepository(Driver::class)->find($driverId);

                if (!$driver) {
                    return $this->json([
                        'status' => 404,
                        'code' => 'DRIVER_NOT_FOUND',
                        'message' => "Pilote ID $driverId introuvable"
                    ], Response::HTTP_NOT_FOUND);
                }

                
                if ($driver->getTeam() && $driver->getTeam()->getId() !== $team->getId()) {
                    return $this->json([
                        'status' => 409,
                        'code' => 'DRIVER_ALREADY_IN_TEAM',
                        'message' => "Le pilote {$driver->getFullName()} appartient déjà à {$driver->getTeam()->getName()}"
                    ], Response::HTTP_CONFLICT);
                }

                $driver->setTeam($team);
                $this->entityManager->persist($driver);
            }

            
            foreach ($remove as $driverId) {
                $driver = $this->entityManager->getRepository(Driver::class)->find($driverId);

                if (!$driver) {
                    return $this->json([
                        'status' => 404,
                        'code' => 'DRIVER_NOT_FOUND',
                        'message' => "Pilote ID $driverId introuvable"
                    ], Response::HTTP_NOT_FOUND);
                }

                
                if (!$driver->getTeam() || $driver->getTeam()->getId() !== $team->getId()) {
                    return $this->json([
                        'status' => 409,
                        'code' => 'DRIVER_NOT_IN_TEAM',
                        'message' => "Le pilote {$driver->getFullName()} n'appartient pas à cette écurie"
                    ], Response::HTTP_CONFLICT);
                }

                $driver->setTeam(null);
                $this->entityManager->persist($driver);
            }

            $this->entityManager->flush();

            
            $this->entityManager->refresh($team);

            $drivers = array_map(function (Driver $driver) {
                return [
                    'id' => $driver->getId(),
                    'firstName' => $driver->getFirstName(),
                    'lastName' => $driver->getLastName(),
                    'isStarter' => $driver->isStarter()
                ];
            }, $team->getDrivers()->toArray());

            return $this->json([
                'status' => 200,
                'message' => 'Pilotes mis à jour avec succès',
                'team' => [
                    'id' => $team->getId(),
                    'name' => $team->getName()
                ],
                'drivers' => array_values($drivers)
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'code' => 'INTERNAL_ERROR',
                'message' => 'Une erreur est survenue'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', name: 'api_team_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $teams = $this->entityManager->getRepository(Team::class)->findAllWithEngine();

        $result = array_map(function (Team $team) {
            return [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'engine' => $team->getEngine() ? [
                    'id' => $team->getEngine()->getId(),
                    'brand' => $team->getEngine()->getBrand()
                ] : null,
                'driversCount' => $team->getDrivers()->count()
            ];
        }, $teams);

        return $this->json([
            'status' => 200,
            'count' => count($result),
            'data' => $result
        ]);
    }
}
