<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use DomainException;

class User
{
    private const STATUS_NEW    = 'new';
    private const STATUS_WAIT   = 'wait';
    private const STATUS_ACTIVE = 'active';

    /**
     * @var Id
     */
    private $id;

    /**
     * @var DateTimeImmutable
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
     * @var Role
     */
    private $role;

    /**
     * @var Network[]|ArrayCollection
     */
    private $networks;

    /**
     * @var ResetToken|null
     */
    private $resetToken;

    private function __construct(Id $id, DateTimeImmutable $dateOfCreation)
    {
        $this->id = $id;
        $this->dateOfCreation = $dateOfCreation;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();

    }

    public static function signUpByEmail(
        Id $id, DateTimeImmutable $date,
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

    public static function signUpByNetwork(
        Id $id, DateTimeImmutable $date,
        string $network,
        string $identity
    ): self {
        $user = new self($id, $date);
        $user->attachNetwork($network, $identity);
        $user->status = self::STATUS_ACTIVE;

        return $user;
    }

    public function confirmSignUp(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('User is already confirmed.');
        }
        $this->status = self::STATUS_ACTIVE;
        $this->confirmToken = null;
    }

    public function requestPasswordReset(ResetToken $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }
        if (!$this->email) {
            throw new DomainException('Email is not specified.');
        }
        if ($this->resetToken && !$this->resetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting is already requesting.');
        }

        $this->resetToken = $token;
    }

    public function passwordReset(DateTimeImmutable $now, string $hash): void
    {
        if (!$this->resetToken) {
            throw new DomainException('Resetting is not requested.');
        }

        if ($this->resetToken->isExpiredTo($now)) {
            throw new DomainException('Reset token is expired.');
        }

        $this->passwordHash = $hash;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqual($role)) {
            throw new DomainException('Role is already same.');
        }

        $this->role = $role;
    }

    private function attachNetwork(string $network, string $identity): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->isAlreadyAttached($network)) {
                throw new DomainException('Network is already attached.');
            }
        }
        $this->networks->add(new Network($this, $network, $identity));
    }

    /**
     * @return ResetToken
     */
    public function getResetToken(): ResetToken
    {
        return $this->resetToken;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateOfCreation(): DateTimeImmutable
    {
        return $this->dateOfCreation;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
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
     * @return Network[]
     */
    public function getNetworks(): array
    {
        return $this->networks->toArray();
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
    }
}