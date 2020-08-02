<?php

declare(strict_types=1);

namespace Unit\Model\User\Entity\User\SignUp;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testSuccess()
    {
        $user = User::signUpByEmail(
            $id = Id::next(),
            $dateOfCreation = new \DateTimeImmutable(),
            $email = new Email('test@app.test'),
            $hash = (new PasswordHasher())->hash('hash'),
            $token = (new ConfirmTokenizer())->generate()
        );

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        self::assertEquals($id, $user->getId());
        self::assertEquals($dateOfCreation, $user->getDateOfCreation());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getConfirmToken());

        self::assertTrue($user->getRole()->isUser());
    }
}