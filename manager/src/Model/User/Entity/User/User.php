<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

class User
{
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
    private $token;


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
        $this->token = $token;
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
    public function getToken(): string
    {
        return $this->token;
    }
}