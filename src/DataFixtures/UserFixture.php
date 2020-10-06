<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public const USER_REFERENCE = 'user-paul';

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUserName('Paul');
        $user->setLogin('polo');
        $user->setPassword('mdp');

        $manager->persist($user);
        $manager->flush();
    }
}
