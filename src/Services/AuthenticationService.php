<?php


namespace App\Services;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * AuthenticationService constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->em = $entityManager;
    }

    /**
     * Check login info and get or create the authentication token
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function getToken(Request $request): string
    {
        $data = json_decode($request->getContent(), true);
        if (is_null($data)) {
            throw new \InvalidArgumentException('Please provide login information');
        }

        $login = $data['login'];
        $password = $data['password'];

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['login' => $login]);
        if (is_null($user)) {
            throw new \InvalidArgumentException('Unknown user login');
        }

        $validPassword = password_verify($password, $user->getPassword());
        if (!$validPassword) {
            throw new \InvalidArgumentException('Authentication failed');
        }

        // Check if the token already exists and is not expired
        if (!is_null($user->getApiToken())) {
            $token = $user->getApiToken();
        } else {
            $token = bin2hex(random_bytes(60));
            $user->setApiToken($token);
        }

        $user->setApiTokenExpiryDate(new \DateTime('+1 hour'));
        $this->em->flush();

        return $token;
    }

    /**
     * Delete a token from a user
     * @param Request $request
     * @throws \Exception
     */
    public function deleteToken(Request $request)
    {
        $authenticationToken = $request->headers->get('MUST-API-TOKEN');
        if (is_null($authenticationToken)) {
            return;
        }

        $user = $this->userRepository->findOneBy(['apiToken' => $authenticationToken]);
        if (is_null($user)) {
            return;
        }

        $user->setApiToken(null);
        $user->setApiTokenExpiryDate(null);
        $this->em->flush();
    }
}
