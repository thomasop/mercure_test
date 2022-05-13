<?php

declare(strict_types=1);

namespace App\tests\Entity;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageTest extends KernelTestCase
{
    public function test(): void
    {
        $message = (new Message())
        ->setContent('content')
        ->setNew(true);
        self::bootKernel();
        $error = self::getContainer()->get('validator')->validate($message);
        $this->assertCount(0, $error);
    }

    public function testContent(): void
    {
        $message = new Message();
        $message->setContent('test content');
        $this->assertEquals('test content', $message->getContent());
    }

    public function testNew(): void
    {
        $new = new Message();
        $new->setNew(true);
        $this->assertEquals(true, $new->getNew());
    }
}
