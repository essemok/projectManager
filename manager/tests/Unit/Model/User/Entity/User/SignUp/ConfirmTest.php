<?php

declare(strict_types=1);

namespace Unit\Model\User\Entity\User\SignUp;

use PHPUnit\Framework\TestCase;
use App\Tests\Unit\Builder\User\UserBuilder;

class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->build();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        self::assertNull($user->getConfirmToken());
    }

    public function testAlready(): void
    {
        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->build();

        $this->expectExceptionMessage('User is already confirmed.');
        $user->confirmSignUp();
    }
}