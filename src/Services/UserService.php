<?php


namespace App\Services;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getAllUsers(): array {
        $users = $this->userRepository->findAll();

        $jsonUsers = [];
        foreach ($users as $user) {
            $jsonUsers[] = [
                'id' => $user->getId(),
                'name' => $user->getUserName(),
                'login' => $user->getLogin(),
            ];
        }

        return $jsonUsers;
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function deleteUser(?User $user): bool
    {
        if (is_null($user)) {
            return false;
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return true;
    }
}
