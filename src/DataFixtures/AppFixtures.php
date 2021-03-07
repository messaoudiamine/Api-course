<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();

    }
    
    public function load(ObjectManager $manager)
    {
        $this->loadUser($manager);
        $this->loadPost($manager);
        $this->loadComment($manager);
    }
    public function loadPost(ObjectManager $manager)
    {
        for ($i=0; $i < 100 ; $i++) { 
            
            $post = new Post();
    
            $post->setTitle($this->faker->sentence());
            $post->setSlug($this->faker->slug);
            $post->setContent($this->faker->text());
            $post->setPublished(new \DateTime());
    
            $user= $this->getReference("user_admin_" . rand(0,9));
    
            $this->addReference("post_$i", $post);

            $post->setAuthor($user);
            $manager->persist($post);

        }
        $manager->flush($post);

    }
    public function loadComment(ObjectManager $manager)
    {
        for ($i=0; $i < 1000 ; $i++) { 
            $comment = new Comment();

            $comment->setContent($this->faker->text(50));
            $comment->setPublished(new \DateTime());
            $user= $this->getReference("user_admin_" . rand(0,9));
            $comment->setAuthor($user);

            $post = $this->getReference("post_" . rand(0,99));
            $comment->setPost($post);

            $manager->persist($comment);
        }       
        $manager->flush($comment);

    }
    public function loadUser(ObjectManager $manager)
    {
        for ($i=0; $i < 10 ; $i++) { 
            
            $user = new User();
    
            $user->setUsername($this->faker->userName);
            $user->setName($this->faker->name);
            $user->setEmail($this->faker->email);
            $password = $this->passwordEncoder->encodePassword($user, 'passwordamine1234');
            $user->setPassword($password);
    
            $this->addReference("user_admin_$i", $user);
            $manager->persist($user);
            
        }
        $manager->flush($user);

    }
}
