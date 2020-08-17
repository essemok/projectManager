<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByResetToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $token]);
    }

    /**
     * @param Id $id
     * @return User|object
     * @throws EntityNotFoundException
     */
    public function get(Id $id): User
    {
        if (!$user = $this->repo->find($userId = $id->getValue())) {
            throw new EntityNotFoundException('User is not found, id: ' . $userId);
        }

        return $user;
    }

    /**
     * @param Email $email
     * @return User|object
     * @throws EntityNotFoundException
     */
    public function getByEmail(Email $email): User
    {
        if (!$user = $this->repo->findOneBy(['email' => $userEmail = $email->getValue()])) {
            throw new EntityNotFoundException('User is not found, email: ' . $userEmail);
        }

        return $user;
    }

    /**
     * @param Email $email
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param string $network
     * @param string $identity
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('u')
            ->select('COUNT(u,id)')
            ->innerJoin('u.networks', 'n')
            ->andWhere('n.network = :network and n.identity = :identity')
            ->setParameter(':network', $$network)
            ->setParameter(':identity', $identity)
            ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param User $user
     */
    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}