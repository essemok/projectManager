<?php

declare(strict_types=1);

namespace Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use PHPUnit\Framework\TestCase;
use App\Tests\Unit\Builder\User\UserBuilder;

class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->build();

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());
    }

    public function testAlready(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->build();

        $user->requestPasswordReset($token, $now);

        self::expectExceptionMessage('Resetting is already requesting.');

        $user->requestPasswordReset($token, $now);
    }

    public function testExpired(): void
    {
        $now = new \DateTimeImmutable();
        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->confirmed()
            ->build();

        $token1 = new ResetToken('token', $now->modify('+1 day'));

        $user->requestPasswordReset($token1, $now);

        self::assertEquals($token1, $user->getResetToken());

        $token2 = new ResetToken('token', $now->modify('+3 day'));
        $user->requestPasswordReset($token2, $now->modify('+2 day'));

        self::assertEquals($token2, $user->getResetToken());
    }

    public function testNotConfirmed(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = (new UserBuilder())->viaEmail()->build();

        $this->expectExceptionMessage('User is not active.');
        $user->requestPasswordReset($token, $now);
    }

    public function testWithoutEmail(): void
    {
        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user = $user = (new UserBuilder())->viaNetwork()->build();

        self::expectExceptionMessage('Email is not specified.');
        $user->requestPasswordReset($token, $now);
    }
}