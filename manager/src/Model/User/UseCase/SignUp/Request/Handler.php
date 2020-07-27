<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Request;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Entity\User\User;
use App\Model\User\Flusher;
use App\Model\User\Service\ConfirmTokenizer;
use App\Model\User\Service\ConfirmTokenSender;
use App\Model\User\Service\PasswordHasher;

Class Handler
{
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @var ConfirmTokenizer
     */
    private $tokenizer;

    /**
     * @var ConfirmTokenSender
     */
    private $sender;

    /**
     * @var Flusher
     */
    private $flusher;


    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        ConfirmTokenizer $tokenizer,
        ConfirmTokenSender $sender,
        Flusher $flusher
    ) {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    /**
     * @param Command $command
     * @throws \DomainException
     */
    public function handle(Command $command)
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User already exist!');
        }

        $user = new User(
            Id::next(),
            new \DateTimeImmutable(),
            $email,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate()
        );

        $this->users->add($user);
        $this->sender->send($email, $token);
        $this->flusher->flush();
    }
}