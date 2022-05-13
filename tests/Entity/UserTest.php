<?php declare(strict_types=1);

namespace App\tests\Entity;

use App\Entity\User;
use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function test(): void
    {
        $user = (new User())
        ->setEmail("admin@mail.com")
        ->setPassword("Test1234?")
        ->setRoles(["ROLE_ADMIN"])
        ->setUsername("test");
        self::bootKernel();
        $error = self::getContainer()->get('validator')->validate($user);
        $this->assertCount(0, $error);
    }

    public function testEmail(): void
    {
        $user = new User();
        $user->setEmail("admin@mail.com");
        $this->assertEquals("admin@mail.com", $user->getEmail());
    }

    public function testPassword(): void
    {
        $password = new User();
        $password->setPassword("Test1234?");
        $this->assertEquals("Test1234?", $password->getPassword());
    }

    public function testRole(): void
    {
        $role = new User();
        $role->setRoles(["ROLE_USER"]);
        $this->assertEquals(["ROLE_USER"], $role->getRoles());
    }

    public function testUsername(): void
    {
        $username = new User();
        $username->setUsername("test");
        $this->assertEquals("test", $username->getUsername());
    }
}