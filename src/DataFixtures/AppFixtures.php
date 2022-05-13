<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Conversation;
use App\Entity\Participant;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
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
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        $paul = new User();
        $paul->setUsername('paul');
        $paul->setEmail('paul@mail.com');
        $paulPassword = $this->passwordHasher->hashPassword(
            $paul,
            'Test1234?'
        );
        $paul->setPassword($paulPassword);
        $paul->setRoles(["ROLE_ADMIN"]);
        $manager->persist($paul);

        $conversation = new Conversation();
        $manager->persist($conversation);

        $participant = new Participant();
        $participant->setConversation($conversation);
        $participant->setUser($admin);
        $manager->persist($participant);

        $participant2 = new Participant();
        $participant2->setConversation($conversation);
        $participant2->setUser($paul);
        $manager->persist($participant2);

        $message = new Message();
        $message->setContent("yo");
        $message->setConversation($conversation);
        $message->setNew(false);
        $message->setUser($admin);
        $manager->persist($message);

        $message2 = new Message();
        $message2->setContent("salut");
        $message2->setConversation($conversation);
        $message2->setNew(false);
        $message2->setUser($paul);
        $manager->persist($message2);

        $manager->flush();
    }
}
