<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use Doctrine\ORM\EntityManager;
use App\Model\User\Entity\User\User;


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
            $email,
            password_hash($command->password, PASSWORD_ARGON2I)
        );

        $this->em->persist();
        $this->em->flush();
    }
}