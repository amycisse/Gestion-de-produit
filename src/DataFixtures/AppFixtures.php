<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHashed;

    public function __construct(UserPasswordHasherInterface $passwordHashed)
    {
          $this->passwordHashed = $passwordHashed;
    }
    public function load(ObjectManager $manager): void
    {
         $user = new User();
         $plainPassword = "admin1234";
         $hashedPassword = $this->passwordHashed->hashPassword($user,$plainPassword);
         $user->setUsername("admin");
         $user->setPassword($hashedPassword);
         $user->setRoles(['ROLE_ADMIN']);
         $manager->persist($user);

        $manager->flush();
    }
}
