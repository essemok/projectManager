<?php

declare(strict_types=1);

use App\Model\User\Entity\User\Role;
use App\Tests\Unit\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        self::assertTrue($user->getRole()->isUser());

        $user->changeRole(Role::admin());
        self::assertTrue($user->getRole()->isAdmin());
        self::assertFalse($user->getRole()->isUser());

        $user->changeRole(Role::user());
        self::assertTrue($user->getRole()->isUser());
        self::assertFalse($user->getRole()->isAdmin());
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        self::expectExceptionMessage('Role is already same.');
        $user->changeRole(Role::user());
    }

    public function testWrongRole(): void
    {
        $user = (new UserBuilder())->viaEmail()->build();

        self::expectException(InvalidArgumentException::class);
        $user->changeRole(new Role('ROLE MASTER'));
    }
}