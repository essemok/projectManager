<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;

class User
{
    private const STATUS_NEW = 'new';
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
     * @var Email|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $passwordHash;

    /**
     * @var string|null
     */
    private $confirmToken;

    /**
     * @var string
     */
    private $status;

    /**
     * @var Network[]|ArrayCollection
     */
    private $networks;

    /**
     * @var ResetToken|null
     */
    private $resetToken;


    public function __construct(Id $id, \DateTimeImmutable $dateOfCreation)
    {
        $this->id = $id;
        $this->dateOfCreation = $dateOfCreation;
        $this->status = self::STATUS_NEW;
        $this->networks = new ArrayCollection();

    }

    public function signUpByEmail(Email $email, string $hash, string $token)
    {
        if (!$this->isNew()) {
            throw new \DomainException('User is already signed up.');
        }
        $this->email = $email;
        $this->passwordHash = $hash;
        $this->confirmToken = $token;
        $this->status = self::STATUS_WAIT;
    }

    public function signUpByNetwork(string $network, string $identity)
    {
        if (!$this->isNew()) {
            throw new \DomainException('User is already signed up.');
        }

        $this->attachNetwork($network, $identity);
        $this->status = self::STATUS_ACTIVE;
    }

    public function requestPasswordReset(ResetToken $token, \DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new \DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new \DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new \DomainException('Resetting is already requesting.');
        }

        $this->resetToken = $token;
    }

    public function passwordReset(\DateTimeImmutable $now, string $hash): void
    {
        if (!$this->resetToken) {
            throw new \DomainException('Resetting is not requested.');
        }

        if ($this->resetToken->isExpiredTo($now)) {
            throw new \DomainException('Reset token is expired.');
        }

        $this->passwordHash = $hash;
    }

    public function getResetToken(): ResetToken
    {
        return $this->resetToken;
    }

    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
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

    private function attachNetwork(string $network, string $identity)
    {
        foreach ($this->networks as $existing) {
            if ($existing->isAlreadyAttached($network)) {
                throw new \DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }
}