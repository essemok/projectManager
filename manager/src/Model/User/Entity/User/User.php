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


    public function __construct(
        Id $id,
        \DateTimeImmutable $dateOfCreation,
        Email $email,
        string $hash,
        string $token
    ) {
        $this->id = $id;
        $this->dateOfCreation = $dateOfCreation;
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->confirmToken = $token;
        $this->status = self::STATUS_WAIT;
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
}