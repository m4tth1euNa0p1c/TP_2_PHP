<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        
        
        throw new \LogicException('This method should be intercepted by the security firewall.');
    }

    #[Route('/user/create', name: 'api_user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'status' => 400,
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Email et mot de passe requis'
                ], Response::HTTP_BAD_REQUEST);
            }

            
            $existingUser = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $data['email']]);

            if ($existingUser) {
                return $this->json([
                    'status' => 409,
                    'code' => 'EMAIL_ALREADY_EXISTS',
                    'message' => 'Cet email est déjà utilisé'
                ], Response::HTTP_CONFLICT);
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $data['password'])
            );
            $user->setIsActive(true);

            
            $errors = $this->validator->validate($user);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
                }

                return $this->json([
                    'status' => 422,
                    'code' => 'VALIDATION_ERROR',
                    'errors' => $errorMessages
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json([
                'status' => 201,
                'message' => 'Utilisateur créé avec succès',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles()
                ]
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'status' => 500,
                'code' => 'INTERNAL_ERROR',
                'message' => 'Une erreur est survenue'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
