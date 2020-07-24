<?php

namespace Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class RequestTest extends TestCase
{
    public function testSuccess()
    {
        $user = new User(
            $id = Uuid::uuid4()->toString(),
            $dateOfCreation = new \DateTimeImmutable(),
            $email = 'test@app.test',
            $hash = 'hash'
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($dateOfCreation, $user->getDateOfCreation());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
    }
}