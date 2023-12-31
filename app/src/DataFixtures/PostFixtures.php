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
       for ($i = 0; $i < 200; $i++) {
        $post = new Post();
        $postMedia = new PostMedia();

        $post->setCaption($this->faker->catchPhrase());
        $post->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-2 years', '-100 days')));
        $post->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days')));
        
        $userReference = $this->getReference('user_' . random_int(0, 14));
        $post->setCreatedBy($userReference);

        $categoryReference = $this->getReference('category_' . random_int(0, 4));
        $post->setCategory($categoryReference);

        $postMedia->setImageUrl($this->faker->imageUrl());
        $post->setPostMedia($postMedia);

        $this->addReference('post_' . $i, $post);

        $this->manager->persist($postMedia);
        $this->manager->persist($post);
       }

       $this->manager->flush();
    }
}