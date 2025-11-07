<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const REGULAR_USER_REFERENCE = 'regular-user';
    public const MANAGER_USER_REFERENCE = 'manager-user';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, 'admin123')
        );
        $admin->setIsActive(true);
        $manager->persist($admin);
        $this->addReference(self::ADMIN_USER_REFERENCE, $admin);

        
        $manager_user = new User();
        $manager_user->setEmail('manager@example.com');
        $manager_user->setRoles(['ROLE_MANAGER']);
        $manager_user->setPassword(
            $this->passwordHasher->hashPassword($manager_user, 'manager123')
        );
        $manager_user->setIsActive(true);
        $manager->persist($manager_user);
        $this->addReference(self::MANAGER_USER_REFERENCE, $manager_user);

        
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'user123')
        );
        $user->setIsActive(true);
        $manager->persist($user);
        $this->addReference(self::REGULAR_USER_REFERENCE, $user);

        $manager->flush();
    }
}
