<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

class User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $dateOfCreation;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * User constructor.
     *
     * @param string $email
     * @param string $passwordHash
     */
    public function __construct(string $id, \DateTimeImmutable $dateOfCreation, string $email, string $passwordHash)
    {
        $this->id = $id;
        $this->dateOfCreation = $dateOfCreation;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDateOfCreation(): \DateTimeImmutable
    {
        return $this->dateOfCreation;
    }
}