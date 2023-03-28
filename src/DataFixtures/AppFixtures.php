<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');

        $password = $this->hasher->hashPassword($user, '321');
        $user->setPassword($password);
        $user->setRoles(["ROLE_ADMIN","ROLE_USER"]);

        $manager->persist($user);
        $manager->flush();

        $secondaryUser = new User();
        $secondaryUser->setUsername('umut');

        $password = $this->hasher->hashPassword($secondaryUser, '123');
        $secondaryUser->setPassword($password);
        $secondaryUser->setRoles(["ROLE_USER"]);

        $manager->persist($secondaryUser);
        $manager->flush();
    }
}