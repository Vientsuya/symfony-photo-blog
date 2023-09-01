<?php

namespace App\DataFixtures;

use App\Entity\User;

class UserFixtures extends AbstractBaseFixtures
{    
    public function loadData(): void
    {
        for ($i = 0; $i < 8; ++$i) {
            $user = new User();
            $user->setEmail($this->faker->email());
            $user->setPassword($this->faker->password());
            $user->setRoles(['user']);
            
            $this->manager->persist($user);
        }
        $this->manager->flush();
    }
}
