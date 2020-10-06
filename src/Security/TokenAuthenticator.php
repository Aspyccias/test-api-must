<?php


namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private EntityManagerInterface $entityManager;

    /**
     * TokenAuthenticator constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Called on every requests on each authentication is required to check if MUST-API-TOKEN has been sent in headers
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Check if a request has the MUST-API-TOKEN in its headers
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->headers->has('MUST-API-TOKEN');
    }

    /**
     * Get the MUST-API-TOKEN of the request headers
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return $request->headers->get('MUST-API-TOKEN');
    }

    /**
     * Get the User entity according to the MUST-API-TOKEN
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (is_null($credentials)) {
            // No token header in the request: authentication fails and returns 401 unauthorized
            return null;
        }

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['apiToken' => $credentials]);

        if (!is_null($user)) {
            if ($user->isApiTokenExpired()) {
                throw new CustomUserMessageAuthenticationException(
                    'Token expired'
                );
            }
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // No check needed for a token authentication
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => $exception->getMessageKey()
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
