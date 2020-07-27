<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

class User
{
    private const STATUS_WAIT = 'wait';

    private const STATUS_ACTIVE = 'active';
    /**
     * @var Id
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $dateOfCreation;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var string
     */
    private $confirmToken;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $network;

    /**
     * @var string
     */
    private $identity;


    private function __construct(Id $id, \DateTimeImmutable $dateOfCreation)
    {
        $this->id = $id;
        $this->dateOfCreation = $dateOfCreation;
    }

    public static function signUpByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $hash,
        string $token
    ): self {
        $user = new self($id, $date);

        $user->email = $email;
        $user->passwordHash = $hash;
        $user->confirmToken = $token;
        $user->status = self::STATUS_WAIT;

        return $user;
    }

    public static function singUpByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        string $network,
        string $identity
    ): self {
        $user = new self($id, $date);

        $user->network = $network;
        $user->identity = $identity;
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @throws \DomainException
     */
    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new \DomainException('User is already confirmed.');
        }
        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDateOfCreation(): \DateTimeImmutable
    {
        return $this->dateOfCreation;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getConfirmToken(): ?string
    {
        return $this->confirmToken;
    }

    /**
     * @return string
     */
    public function getNetwork(): string
    {
        return $this->network;
    }

    /**
     * @return string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }
}