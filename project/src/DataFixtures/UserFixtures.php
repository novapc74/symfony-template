<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    public function loadData(ObjectManager $manager): void
    {
        $admin = $manager->getRepository(User::class)->findOneBy(['email' => 'test-email@test.com']);

        if (null === $admin) {
            $this->createEntity(User::class, 1, function (User $user) {
                $user
                    ->setEmail('test-email@test.com')
                    ->setRoles(["ROLE_ADMIN"])
                    ->setPassword($this->passwordEncoder->hashPassword($user, 'super-password'));
            });

            $manager->flush();
        }
    }
}

