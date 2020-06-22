<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;


class User
{
    /**
     * @var int
     */
    private $id;

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
    public function __construct(int $id, string $email, string $passwordHash)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return string
     */
    public function getEmail()
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}