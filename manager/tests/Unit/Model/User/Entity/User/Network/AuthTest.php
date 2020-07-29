<?php

declare(strict_types=1);

namespace Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Network;
use App\Model\User\Entity\User\User;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByNetwork($network = 'vk', $identity = '00000001');

        self::assertTrue($user->isActive());

        self::assertCount(1, $userNetworks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $firstNetwork = reset($userNetworks));
        self::assertEquals($network, $firstNetwork->getNetwork());
        self::assertEquals($identity, $firstNetwork->getIdentity());
    }

    public function testAlready(): void
    {
        $user = new User(Id::next(), new \DateTimeImmutable());

        $user->signUpByNetwork($network = 'vk', $identity = '00000001');

        self::expectExceptionMessage('User is already signed up.');

        $user->signUpByNetwork($network, $identity);
    }
}