<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use Doctrine\ORM\EntityManager;
use App\Model\User\Entity\User\User;
use Ramsey\Uuid\Uuid;

class Handler
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(Command $command): void
    {
        $email = mb_strtolower($command->email);

        if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            throw new \DomainException('User already exist!');
        }

        $user = new User(
            Uuid::uuid4()->toString(),
            new \DateTimeImmutable(),
            $email,
            password_hash($command->password, PASSWORD_ARGON2I)
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}