<?php

declare(strict_types=1);

namespace Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Network;
use PHPUnit\Framework\TestCase;
use App\Tests\Unit\Builder\User\UserBuilder;

class AuthTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder())
            ->viaNetwork($network = 'vk', $identity = '00001')
            ->confirmed()
            ->build();

        self::assertTrue($user->isActive());

        self::assertCount(1, $userNetworks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $firstNetwork = reset($userNetworks));
        self::assertEquals($network, $firstNetwork->getNetwork());
        self::assertEquals($identity, $firstNetwork->getIdentity());

        self::assertTrue($user->getRole()->isUser());
    }
}