<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('brumen');
        $admin->setEmail('mail@mail.com');
        $adminPassword = $this->passwordHasher->hashPassword(
            $admin,
            'Test1234?'
        );
        $admin->setPassword($adminPassword);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $paul = new User();
        $paul->setUsername('paul');
        $paul->setEmail('paul@mail.com');
        $paulPassword = $this->passwordHasher->hashPassword(
            $paul,
            'Test1234?'
        );
        $paul->setPassword($paulPassword);
        $paul->setRoles(['ROLE_ADMIN']);
        $manager->persist($paul);

        $manager->flush();
    }
}
