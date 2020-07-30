<?php


namespace Unit\Model\User\Entity\User\Reset;

use App\Model\User\Entity\User\ResetToken;
use PHPUnit\Framework\TestCase;
use App\Tests\Unit\Builder\User\UserBuilder;

class ResetTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->build();

        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now->modify('+1 day'));

        $user->requestPasswordReset($token, $now);

        self::assertNotNull($user->getResetToken());

        $user->passwordReset($now, $hash = 'hash');

        self::assertNotNull($user->getResetToken());
        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testExpiredToken(): void
    {
        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->build();

        $now = new \DateTimeImmutable();
        $token = new ResetToken('token', $now);

        $user->requestPasswordReset($token, $now);

        self::expectExceptionMessage('Reset token is expired');

        $user->passwordReset($now->modify('+1 dya'), 'hash');
    }

    public function testNotRequested(): void
    {
        $user = $user = (new UserBuilder())
            ->viaEmail()
            ->build();

        $now = new \DateTimeImmutable();

        self::expectExceptionMessage('Resetting is not requested.');

        $user->passwordReset($now, 'hash');
    }
}