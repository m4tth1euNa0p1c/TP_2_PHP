<?php

namespace App\Controller;

use App\Entity\Driver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/driver')]
#[IsGranted('ROLE_USER')]
class DriverController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'api_driver_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $drivers = $this->entityManager->getRepository(Driver::class)->findAll();

        $result = array_map(function (Driver $driver) {
            return [
                'id' => $driver->getId(),
                'firstName' => $driver->getFirstName(),
                'lastName' => $driver->getLastName(),
                'isStarter' => $driver->isStarter(),
                'licensePoints' => $driver->getLicensePoints(),
                'status' => $driver->getStatus(),
                'f1StartDate' => $driver->getF1StartDate()->format('Y-m-d'),
                'team' => $driver->getTeam() ? [
                    'id' => $driver->getTeam()->getId(),
                    'name' => $driver->getTeam()->getName()
                ] : null
            ];
        }, $drivers);

        return $this->json([
            'status' => 200,
            'count' => count($result),
            'data' => $result
        ]);
    }

    #[Route('/{id}', name: 'api_driver_get', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $driver = $this->entityManager->getRepository(Driver::class)->find($id);

        if (!$driver) {
            return $this->json([
                'status' => 404,
                'code' => 'DRIVER_NOT_FOUND',
                'message' => 'Pilote introuvable'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'status' => 200,
            'data' => [
                'id' => $driver->getId(),
                'firstName' => $driver->getFirstName(),
                'lastName' => $driver->getLastName(),
                'isStarter' => $driver->isStarter(),
                'licensePoints' => $driver->getLicensePoints(),
                'status' => $driver->getStatus(),
                'f1StartDate' => $driver->getF1StartDate()->format('Y-m-d'),
                'team' => $driver->getTeam() ? [
                    'id' => $driver->getTeam()->getId(),
                    'name' => $driver->getTeam()->getName()
                ] : null,
                'infractionsCount' => $driver->getInfractions()->count()
            ]
        ]);
    }
}
