<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{    
    public function getDependencies()
    {
        return [
            PostFixtures::class,
            UserFixtures::class
        ];
    }

    public function loadData(): void
    {
        for ($i = 0; $i < 200; ++$i) {
            $comment = new Comment();
            $comment->setCommentContent($this->faker->sentence());
            $comment->setCreatedAt(\DatetimeImmutable::createFromMutable($this->faker->dateTimeBetween('-2 years')));

            $userReference = $this->getReference('user_' . random_int(0, 7));
            $comment->setCreatedBy($userReference);

            $postReference = $this->getReference('post_' . random_int(0, 24));
            $comment->setPost($postReference);
            
            $this->manager->persist($comment);
        }
        $this->manager->flush();
    }
}
