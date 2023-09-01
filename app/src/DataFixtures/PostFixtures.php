<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\PostMedia;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PostFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class,
        ];
    }

    public function loadData(): void
    {
       for ($i = 0; $i < 25; $i++) {
        $post = new Post();
        $postMedia = new PostMedia();

        $post->setCaption($this->faker->catchPhrase());
        $post->setCreatedAt(new \DateTimeImmutable());
        
        $userReference = $this->getReference('user_' . random_int(0, 7));
        $post->setCreatedBy($userReference);

        $categoryReference = $this->getReference('category_' . random_int(0, 4));
        $post->setCategory($categoryReference);

        $postMedia->setImageUrl($this->faker->imageUrl());
        $post->setPostMedia($postMedia);

        $this->manager->persist($postMedia);
        $this->manager->persist($post);
       }

       $this->manager->flush();
    }
}